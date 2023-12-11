<?php

namespace Manta\LaravelUploads\Models;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Exception\InvalidArgumentException as ExceptionInvalidArgumentException;
use Intervention\Image\Exception\NotSupportedException;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Intervention\Image\ImageManagerStatic as Image;
use setasign\Fpdi\Fpdi;
use App\Services\PdfToImage;

class MantaUpload extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'manta_uploads';

    // Disable Laravel's mass assignment protection
    // protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'sort',
        'main',
        'created_by',
        'updated_by',
        'user_id',
        'company_id',
        'host',
        'locale',
        'title',
        'seo_title',
        'private',
        'disk',
        'url',
        'location',
        'filename',
        'extension',
        'mime',
        'size',
        'model',
        'pid',
        'identifier',
        'originalName',
        'comments',
        'pdfLock',
        'error',
        'pages',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * @param null|string $getLocale
     * @return array
     * @throws BindingResolutionException
     */
    public function translation(?string $getLocale = null): array
    {
        $return = ['get', 'org'];
        if ($this->pid) {
            $return['org'] = MantaUpload::find($this->pid);
        } else {
            $return['org'] = $this;
        }
        $return['get'] = $return['org'];
        if ($getLocale != config('manta-users.locale')) {
            $item = MantaUpload::where(['pid' => $return['org']->id, 'locale' => $getLocale])->first();
            if ($item) {
                $return['get'] = $item;
            }
        }
        return $return;
    }

    /**
     * @param int|null $size
     * @param bool $check_exist
     * @return string
     * @throws BindingResolutionException
     * @throws NotReadableException
     * @throws ExceptionInvalidArgumentException
     * @throws NotSupportedException
     */
    function full_path(int $size = null, bool $check_exist = false): string
    {
        if ($check_exist) {
            $file_exist = @fopen($this->url . $this->location . 'cache/thumbnails/' . $size . '/' . $this->filename, 'r');
        }
        if (isset($file_exist) && !$file_exist) {
            foreach (config('manta-uploads.thumbnails') as $resize) {
                $this->resize($resize);
            }
            if (!in_array($size, config('manta-uploads.thumbnails'))) {
                $this->resize($size);
            }
        }
        if ($size != null) {
            $file = $this->url . $this->location . 'cache/thumbnails/' . $size . '/' . $this->filename;
        } else {
            $file = $this->url . $this->location . $this->filename;
        }
        return $file;
    }

    /**
     * @return bool
     * @throws LogicException
     */
    public function remove(): bool
    {
        $storage = Storage::disk($this->disk)->delete($this->location . $this->filename);
        $this->delete();
        return $storage;
    }

    /**
     * @param mixed $file
     * @param array $config
     * @return null|object
     * @throws BindingResolutionException
     * @throws SuspiciousOperationException
     */
    public function upload(mixed $file, array $config = [], ?MantaUpload $upload = null): ?object
    {
        $disk = isset($config['disk']) ? $config['disk'] : 'azure';
        $location = isset($config['location']) ? $config['location'] : 'uploads/' . date('y') . '/' . date('m') . '/' . date('d') . '/';

        if (preg_match('#azure#', $disk)) {
            $url = env('AZURE_STORAGE_URL') . env('AZURE_STORAGE_CONTAINER') . '/';
        } else {
            $url = env('APP_URL') . '/';
        }

        if (is_string($file) && !is_object($file)) {
            if (isset($config['replace']) && !$upload) {
                return false;
            }
            if ($upload) $config['locale'] = $upload->locale;
            if ($upload) $config['seo_title'] = $upload->seo_title;
            if ($upload) $config['model'] = $upload->model;
            if ($upload) $config['pid'] = $upload->pid;
            if ($upload) $config['identifier'] = $upload->identifier;
            if ($upload) $config['comments'] = $upload->comments;
            //
            $originalName = $config['filename'];
            $extension = $upload ? $upload->extension : pathinfo($config['filename'], PATHINFO_EXTENSION);
            $mime = $upload ? $upload->mime : null;
            $size = $upload ? $upload->size : null;
            if (isset($config['replace']) && $config['replace'] == 1) {
                $disk = $upload->disk;
                $location = $upload->location;
                $filename = $upload->filename;
            } else {
                $filename = $this->uniqueFileName($originalName, $disk, $location, false);
            }
            $data = $file;
            if (Storage::disk($disk)->putFileAs($location, $filename, $data)) {
            } else {
                return false;
            }
        } elseif (is_object($file)) {
            // dd($file->getRealPath());
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mime = $file->getMimeType();
            $size = $file->getSize();
            $filename = $this->uniqueFileName($originalName, $disk, $location, true);
            Storage::disk($disk)->makeDirectory($location);
            if (Storage::disk($disk)->putFileAs($location, $file, $filename)) {
                $infoPath = pathinfo(app()->path() . '/storage/app/' . $disk . '' . $location . $filename);
                // dd($infoPath);
            } else {
                // dd($location, $filename, $file);
                return false;
            }
        } else {
            return false;
        }


        $item = [
            'sort' => isset($config['sort']) ? $config['sort'] : 0,
            'main' => isset($config['main']) ? $config['main'] : 0,
            'title' => isset($config['title']) ? $config['title'] : $originalName,
            'seo_title' => isset($config['seo_title']) ? $config['seo_title'] : $originalName,
            'disk' => $disk,
            'url' => $url,
            'location' => $location,
            'filename' => $filename,
            'extension' => $extension,
            'mime' => $mime,
            'size' => $size,
            'comments' => isset($config['comments']) ? $config['comments'] : null,
        ];
        if (isset($config['replace']) && $config['replace'] == 1 && $upload) {
            $item['updated_by'] = auth()->user()->name;
            MantaUpload::where('id', $upload->id)->update($item);
        } else {
            $item['created_by'] = auth()->user()->name;
            $item['user_id'] = auth()->user()->id;
            $item['locale'] = isset($config['locale']) ? $config['locale'] : app()->getLocale();
            $item['host'] = request()->getHost();
            $item['company_id'] = (int)$this->company_id;
            $item['originalName'] = $originalName;
            $item['pid'] = isset($config['pid']) ? $config['pid'] : null;
            $item['model'] = isset($config['model']) ? $config['model'] : null;
            $item['identifier'] = isset($config['identifier']) ? $config['identifier'] : null;
            $upload = MantaUpload::create($item);
        }
        if (in_array($upload->extension, ['jpg', 'jpeg', 'png'])) {
            foreach (config('manta-uploads.thumbnails') as $size) {
                $upload->resize($size);
            }
        }
        if (in_array($upload->extension, ['pdf'])) {
            $upload->pdfToPages();
        }

        return $upload;
    }

    /**
     * @param mixed $filename
     * @param string $disk
     * @param string $location
     * @param bool $timename
     * @return null|string
     * @throws BindingResolutionException
     */
    public function uniqueFileName(mixed $filename, string $disk, string $location, bool $timename = true): ?string
    {
        try {
            $basename   = pathinfo($filename, PATHINFO_FILENAME);
            $basename   = Str::slug($basename, '-');
            $basename   = substr($basename, 0, 20);
            $extension  = pathinfo($filename, PATHINFO_EXTENSION);

            if ($timename) $basename = time();
            $fullfile = $basename . '.' . $extension;
            if (Storage::disk($disk)->exists($location) && Storage::disk($disk)->exists($location . $fullfile)) {
                $imageToken     = substr(sha1(mt_rand()), 0, 5);
                return $basename . '-' . $imageToken . '.' . $extension;
            } else {
                return $basename  . '.' . $extension;
            }
        } catch (\Exception $e) {
            $message = [
                'Class: ' . __CLASS__,
                'Function: ' . __FUNCTION__,
                'Line: ' . __LINE__,
                'location: ' . $location,
                'file: ' . $fullfile,
                'Error: ' . $e->getMessage()
            ];
            if (auth()->user()) {
                $message[] = 'User id: ' . auth()->user()->id;
                $message[] = 'User name: ' . auth()->user()->name;
            }
            // Mail::to(env('MAIL_FROM_ADDRESS'))->send(new MailDeveloper($message));
            return null;
        }
    }

    /** @return float  */
    public function file_upload_max_size(): float
    {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = $this->parse_size(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = $this->parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    /**
     * @param string $size
     * @return int
     */
    public function parse_size(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    /**
     * @param int $decimals
     * @return string
     */
    function convert_filesize(int $decimals = 2): string
    {
        $bytes = $this->size;
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function image(): array
    {
        $return = ['src' => false, 'url' => false];
        if ($this->filename && in_array($this->extension, ['jpg', 'jpeg', 'png', 'svg'])) {
            if ($this->disk == 'azure') {
                $return['src'] = env('AZURE_STORAGE_URL') . env('AZURE_STORAGE_CONTAINER') . '/' . $this->location . $this->filename;
                $return['url'] = env('AZURE_STORAGE_URL') . env('AZURE_STORAGE_CONTAINER') . '/' . $this->location . $this->filename;
            } else {
                $return['src'] = env('APP_URL') . '/' . $this->location . $this->filename;
                $return['url'] = env('APP_URL') . '/' . $this->location . $this->filename;
            }
        }
        return $return;
    }

    /** @return string  */
    public function getIcon(): string
    {
        if (in_array($this->extension, ['xls', 'xlsx'])) {
            return '<i class="fa-solid fa-file-excel"></i>';
        } elseif (in_array($this->extension, ['doc', 'docx'])) {
            return '<i class="fa-solid fa-file-word"></i>';
        } elseif (in_array($this->extension, ['jpg', 'jpeg', 'png', 'svg', 'gif', 'tiff', 'bmp'])) {
            return '<i class="fa-solid fa-image"></i>';
        } elseif ($this->extension == 'pdf') {
            return '<i class="fa-solid fa-file-pdf"></i>';
        } else {
            return '<i class="fa-solid fa-file"></i>';
        }
    }

    /**
     * @param int $width
     * @param int|null $height
     * @return void
     * @throws NotReadableException
     * @throws ExceptionInvalidArgumentException
     * @throws NotSupportedException
     */
    public function resize(int $width = 400, int $height = null): void
    {
        if ($width == null && $height = null) {
            $width = 400;
        }
        $folderSize = $width;
        if ($width == null) {
            $folderSize = $height;
        }

        $stream = Image::make(Storage::disk($this->disk)->get($this->location . $this->filename))->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        // dd($this->disk, (string)$stream->encode($this->extension));
        // $test = Storage::disk($this->disk)->makeDirectory($this->location . '/../cache/thumbnails/' . $folderSize . '/', 0777);
        Storage::disk($this->disk)->put($this->location . '/cache/thumbnails/' . $folderSize . '/' . $this->filename,  (string)$stream->encode($this->extension));
        $stream->destroy();
    }

    public function pdfToPages(): void
    {
        if ($this->pdfLock == 0 && $this->extension == 'pdf' && Storage::disk($this->disk)->exists($this->location . $this->filename)) {
            $this->pdfLock = 1;
            $this->save();
            Storage::disk('local')->put("/pdf_temp/" . $this->id . "/" . $this->filename, Storage::disk($this->disk)->get($this->location . $this->filename));
            $temp_location = Storage::disk('local')->path("/pdf_temp/" . $this->id . "/" . $this->filename);
            /**
             * * Try to read PDF
             */
            try {
                $pdfi = new Fpdi();
                $pdfi->setSourceFile($temp_location);
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                $this->save();
            }
            /**
             * * Try to create thumbnails
             */
            try {
                $pdf = new PdfToImage($temp_location);
                $pdf->setCompressionQuality(60);
                $pdf->setOutputFormat('jpg');
                $pdf->setColorspace(1);
                $this->pages = $pdf->getNumberOfPages();
                foreach (range(1, $this->pages) as $pageNumber) {
                    $tempPath = storage_path("app/pdf_temp/" . $this->id . "/") . "page" . $pageNumber . ".jpg";
                    $pdf->setPage($pageNumber)
                        // ->setOutputFormat('jpg')
                        ->saveImage($tempPath);
                    Storage::disk($this->disk)->put($this->location . "/cache/pdf/" . $this->id . "/page{$pageNumber}.jpg", file_get_contents($tempPath), 'public');
                }
                Storage::disk('local')->deleteDirectory("pdf_temp/" . $this->id . "/");
                Storage::disk('local')->deleteDirectory("pdf_temp" . $this->location . "../");
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                $this->save();
            }
        }
    }

    public function pdfGetImages()
    {
        return Storage::disk($this->disk)->allFiles($this->location . "/cache/pdf/" . $this->id);
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        'location',
        'filename',
        'extension',
        'mime',
        'size',
        'model',
        'pid',
        'identifier',
        'originalName',
        'description',
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

    public function remove() : bool
    {
        $storage = Storage::disk($this->disk)->delete($this->location.$this->filename);
        $this->delete();
        return $storage;
    }

    public function upload(mixed $file, array $config = []) : ?object
    {
        $disk = isset($config['disk']) ? $config['disk'] : 'azure';
        $location = isset($config['location']) ? $config['location'] : date('y') . '/' . date('m') . '/' . date('d') . '/';

        if(is_string($file)){
            $getClientOriginalName = $config['filename'];
            $getClientOriginalExtension = pathinfo($config['filename'], PATHINFO_EXTENSION);
            $getMimeType = null;
            $getSize = null;
            $filename = $this->uniqueFileName($getClientOriginalName, $disk, $location, false);
            $data = $file;
        } else {
            $getClientOriginalName = $file->getClientOriginalName();
            $getClientOriginalExtension = $file->getClientOriginalExtension();
            $getMimeType = $file->getMimeType();
            $getSize = $file->getSize();
            $filename = $this->uniqueFileName($getClientOriginalName, $disk, $location, true);
            $data = Storage::disk($disk)->get($file->getRealPath());
        }

        if (Storage::disk($disk)->put($location.$filename, $data)) {
        } else {
            return false;
        }

        $item = [
            'sort' => isset($config['sort']) ? $config['sort'] : 0,
            'main' => isset($config['main']) ? $config['main'] : 0,
            'created_by' => auth()->user()->name,
            'user_id' => auth()->user()->id,
            'company_id' => (int)$this->company_id,
            'host'  => request()->getHost(),
            'locale' => isset($config['locale']) ? $config['locale'] : null,
            'title' => isset($config['title']) ? $config['title'] : $getClientOriginalName,
            'seo_title' => isset($config['identifier']) ? $config['identifier'] : $getClientOriginalName,
            'disk' => $disk,
            'location' => $location,
            'filename' => $filename,
            'extension' => $getClientOriginalExtension,
            'mime' => $getMimeType,
            'size' => $getSize,
            'originalName' => $getClientOriginalName,
            'model' => isset($config['model']) ? $config['model'] : null,
            'pid' => isset($config['pid']) ? $config['pid'] : null,
            'identifier' => isset($config['identifier']) ? $config['identifier'] : null,
            'description' => isset($config['description']) ? $config['description'] : null,
        ];
        return MantaUpload::create($item);
    }

    /**
     * @param mixed $location
     * @param mixed $file
     * @param string $disk
     * @return string
     */
    public function uniqueFileName($filename, string $disk, string $location, bool $timename = true)
    {
        try {
            $basename   = pathinfo($filename, PATHINFO_FILENAME);
            $basename   = Str::slug($basename, '-');
            $extension  = pathinfo($filename, PATHINFO_EXTENSION);

            if($timename) $basename = time();
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

    public function file_upload_max_size()
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

    public function parse_size($size)
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

    public function image() : array
    {
        $return = ['src' => false,'url' => false];
        if($this->filename && in_array($this->extension, ['jpg', 'jpeg', 'png', 'svg'])){
            if($this->disk == 'azure'){
                $return['src'] = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$this->location.$this->filename;
                $return['url'] = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$this->location.$this->filename;
            } else {
                $return['src'] = env('APP_URL').'/'.$this->location.$this->filename;
                $return['url'] = env('APP_URL').'/'.$this->location.$this->filename;
            }
        }
        return $return;
    }
}

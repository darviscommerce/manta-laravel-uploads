<?php

namespace App\Http\Livewire\Uploads;

use App\Models\MantaUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

use Intervention\Image\ImageManagerStatic as Image;

class UploadsCrop extends Component
{
    public MantaUpload $item;

    public ?string $sort = null;
    public ?string $main = null;
    public ?string $created_by = null;
    public ?string $updated_by = null;
    public ?string $user_id = null;
    public ?string $company_id = null;
    public ?string $host = null;
    public ?string $locale = null;
    public ?string $title = null;
    public ?string $seo_title = null;
    public ?string $disk = null;
    public ?string $location = null;
    public ?string $filename = null;
    public ?string $extension = null;
    public ?string $mime = null;
    public ?string $size = null;
    public ?string $model = null;
    public ?string $pid = null;
    public ?string $identifier = null;
    public ?string $originalName = null;
    public ?string $description = null;

    public ?string $image = null;
    public ?string $newimage = null;

    public function mount(Request $request, $input)
    {
        $item = MantaUpload::find($input);
        if($request->input('locale')){
            $item = MantaUpload::where('locale', $request->input('locale'))->where('pid', $input)->first();
            if($item == null){
                return redirect()->to(route('manta.uploads.create', ['locale' => $request->input('locale'), 'pid' => $input]));
            }
        }
        if ($item == null) {
            return redirect()->to(route('manta.uploads.list'));
        }
        $this->item = $item;

        $this->image = Image::make(Storage::disk($item->disk)->get($item->location.$item->filename))->stream('data-url');

    }

    public function render()
    {
        return view('livewire.uploads.uploads-crop')->layout('layouts.manta-bootstrap');
    }

    public function store($post){
        (new MantaUpload)->upload(file_get_contents($post['newimage']), ['private' => 1, 'filename' => 'test '.date('His').'.jpg']);
    }
}


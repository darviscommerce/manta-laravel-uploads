<?php

namespace App\Http\Livewire\Uploads;

use Manta\LaravelUploads\Models;
use Illuminate\Http\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class UploadsCreate extends Component
{
    use WithFileUploads;

    public $files;

    public ?MantaUpload $item = null;

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
    public ?string $private = null;
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
    public ?string $comments = null;

    public function mount(Request $request)
    {
        $this->host = request()->getHost();
        $this->locale = config('manta-cms.locale');
        if ($request->input('pid') && $request->input('locale')) {
            $this->item = MantaUpload::find($request->input('pid'));
            if ($this->item) {
                $this->pid = $request->input('pid');
                $this->locale = $request->input('locale');
            }
        }
    }

    public function render()
    {
        return view('livewire.uploads.uploads-create')->layout('layouts.manta-bootstrap');
    }

    public function updatedTitle()
    {
        $this->slug = Str::of($this->title)->slug('-');
        $this->seo_title = $this->title;
    }

    public function updatedSlug()
    {
        $this->slug = Str::of($this->slug)->slug('-');
    }

    public function store($input)
    {
        $this->validate(
            [
                'title' => 'required|min:1',
            ],
            [
                'title.required' => 'Titel is verplicht',
            ]
        );

        foreach ($this->files as $file) {
            (new MantaUpload)->upload($file, ['private' => 1]);
        }

        toastr()->addInfo('Item opgeslagen');

        return redirect()->to(route('manta.uploads.list'));
    }
}

<?php

namespace App\Http\Livewire\Uploads;

use Manta\LaravelUploads\Models;
use Illuminate\Http\Request;
use Livewire\Component;
use Illuminate\Support\Str;

class UploadsUpdate extends Component
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

    public ?string $redirect = null;

    public function mount(Request $request, $input)
    {
        $item = MantaUpload::find($input);
        if ($item == null) {
            return redirect()->to(route('manta.uploads.list'));
        }
        if ($request->input('redirect')) {
            $this->redirect = $request->input('redirect');
        }
        $this->item = $item;
        $this->sort = $item->sort;
        $this->main = $item->main;
        $this->created_by = $item->created_by;
        $this->updated_by = $item->updated_by;
        $this->user_id = $item->user_id;
        $this->company_id = $item->company_id;
        $this->host = $item->host;
        $this->locale = $item->locale;
        $this->title = $item->title;
        $this->seo_title = $item->seo_title;
        $this->private = $item->private;
        $this->disk = $item->disk;
        $this->location = $item->location;
        $this->filename = $item->filename;
        $this->extension = $item->extension;
        $this->mime = $item->mime;
        $this->size = $item->size;
        $this->model = $item->model;
        $this->pid = $item->pid;
        $this->identifier = $item->identifier;
        $this->originalName = $item->originalName;
        $this->comments = $item->comments;
    }

    public function render()
    {
        return view('livewire.uploads.uploads-update')->layout('layouts.manta-bootstrap');
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

        $items = [
            'updated_by' => auth()->user()->name,
            'title' => $this->title,
            'seo_title' => $this->seo_title,
            'comments' => $this->comments,
        ];
        MantaUpload::where('id', $this->item->id)->update($items);

        toastr()->addInfo('Item opgeslagen');
    }
}

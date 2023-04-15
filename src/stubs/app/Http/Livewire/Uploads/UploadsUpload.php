<?php

namespace App\Http\Livewire\Uploads;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MantaUpload;

class UploadsUpload extends Component
{

    use WithFileUploads;

    public $documents = [];
    public $max_upload_size = 1024;

    public function mount()
    {
        $upload = new MantaUpload();
        $this->max_upload_size = $upload->file_upload_max_size()/1024;
    }

    public function render()
    {
        return view('livewire.uploads.uploads-upload')->layout('layouts.manta-bootstrap');
    }


    public function updatedDocuments()
    {
        $this->validate([
            'documents.*' => 'image|max:'.$this->max_upload_size,
        ]);
    }

    public function store($input)
    {
        $this->validate([
            'documents.*' => 'image|max:'.$this->max_upload_size, // 1MB Max
        ]);

        foreach ($this->documents as $photo) {
            $photo->store('photos');
        }
    }
}

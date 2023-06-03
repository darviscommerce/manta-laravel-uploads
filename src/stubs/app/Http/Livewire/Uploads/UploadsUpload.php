<?php

namespace App\Http\Livewire\Uploads;

use Livewire\Component;
use Livewire\WithFileUploads;
use Manta\LaravelUploads\Models;

class UploadsUpload extends Component
{

    use WithFileUploads;

    public mixed $documents = [];
    public int $max_upload_size = 1024;

    public mixed $pid;
    public string $model;

    public function mount()
    {
        $upload = new MantaUpload();
        $this->max_upload_size = $upload->file_upload_max_size() / 1024;
    }

    public function render()
    {
        return view('livewire.uploads.uploads-upload')->layout('layouts.manta-bootstrap');
    }


    public function updatedDocuments()
    {
        $this->validate([
            'documents.*' => 'image|max:' . $this->max_upload_size,
        ]);
    }

    public function store($input)
    {
        $this->validate([
            'documents.*' => 'image|max:' . $this->max_upload_size, // 1MB Max
        ]);

        $this->emit('uploadsCreated');

        $upload = new MantaUpload();
        foreach ($this->documents as $photo) {
            $upload->upload($photo, ['pid' => $this->pid, 'model' => $this->model]);
        }

        $this->documents = [];
    }
}

<?php

namespace App\Http\Livewire\Uploads;

use Manta\LaravelUploads\Models;
use Livewire\Component;

class UploadsUploads extends Component
{

    public object $items;

    public mixed $pid;
    public string $model;

    public ?int $trashed = null;
    public ?string $deleteId = null;

    public string $orderjson = '';

    public function mount()
    {
    }

    public function render()
    {
        $this->items = MantaUpload::where(['pid' => $this->pid, 'model' => $this->model])->orderBy('sort', 'ASC')->orderBy('created_at', 'ASC')->get();
        return view('livewire.uploads.uploads-uploads');
    }

    public function main($id)
    {
        MantaUpload::where(['pid' => $this->pid, 'model' => $this->model])->update(['main' => 0]);
        MantaUpload::where('id', $id)->update(['main' => 1]);
        $this->emit('uploadsCreated');
    }

    public function delete($id)
    {
        $this->deleteId = $id;
    }

    public function deleteCancel()
    {
        $this->deleteId = null;
    }

    public function deleteConfirm()
    {
        MantaUpload::find($this->deleteId)->delete();
        $this->deleteId = null;
        $this->trashed = count(MantaUpload::onlyTrashed()->get());
    }

    public function updatedOrderjson()
    {
        $i = 0;
        $rows = json_decode($this->orderjson);

        foreach ($rows->items as $key => $value) {
            MantaUpload::where('id', $value)->update(['sort' => $i++]);
        }
        $this->emit('uploadsCreated');
    }
}

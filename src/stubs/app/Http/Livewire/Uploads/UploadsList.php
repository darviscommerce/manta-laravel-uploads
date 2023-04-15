<?php

namespace App\Http\Livewire\Uploads;

use App\Models\MantaUpload;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class UploadsList extends Component
{
    use WithPagination;
    use WithSorting;

    protected $paginationTheme = 'bootstrap';

    public $search;
    protected $queryString = ['search'];

    public string $show = 'active';
    public ?int $trashed = null;
    public ?string $deleteId = null;

    public function mount()
    {
        $this->sortBy = 'title';
        $this->sortDirection = 'ASC';
    }

    public function render()
    {
        $obj = MantaUpload::orderBy($this->sortBy, $this->sortDirection);
        if($this->show == 'trashed'){
            $obj->onlyTrashed();
        }
        if($this->search){
            $keyword = $this->search;
            $obj->where(function ($query) use($keyword) {
                $query->where('title', 'like', '%' . $keyword . '%')
                   ->orWhere('content', 'like', '%' . $keyword . '%');
              });
        // ->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%');
        }
        $items = $obj->paginate(20);
        return view('livewire.uploads.uploads-list', ['items' => $items])->layout('layouts.manta-bootstrap');
    }

    public function loadTrash()
    {
        $this->trashed = count(MantaUpload::onlyTrashed()->get());
    }

    public function show($show)
    {
        $this->show = $show;
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
        MantaUpload::find($this->deleteId)->remove();
        $this->deleteId = null;
        $this->trashed = count(MantaUpload::onlyTrashed()->get());
    }

    public function restore($id)
    {
        MantaUpload::withTrashed()->where('id', $id)->restore();
        $this->trashed = count(MantaUpload::onlyTrashed()->get());
        $this->show = 'active';
    }
}

<?php

namespace App\Http\Livewire\Uploads;

use Livewire\Component;
use Manta\LaravelUploads\Models\MantaUpload;

class UploadsDeveloper extends Component
{
    public ?string $url = null;
    public ?string $urlNew = null;

    public function mount()
    {
        if (auth()->user()->developer != 1) {
            return redirect()->to(route('manta.uploads.list'));
        }
    }

    public function render()
    {

        $urlGroup = MantaUpload::groupBy('url')->select('url')->get();
        $obj = MantaUpload::select('url');
        if ($this->url) {
            $obj->where('url', $this->url);
        } else {
            $obj->whereNull('url');
        }
        $urlGroupResult = count($obj->get());

        return view('livewire.uploads.uploads-developer', ['urlGroup' => $urlGroup, 'urlGroupResult' => $urlGroupResult])->layout('layouts.manta-bootstrap');
    }

    public function replaceUrl()
    {
        if ($this->url) {
            MantaUpload::where('url', $this->url)->update(['url' => $this->urlNew]);
        } else {
            MantaUpload::whereNull('url')->update(['url' => $this->urlNew]);
        }

        toastr()->addInfo('Url aangepast naar: ' . $this->urlNew);
    }
}

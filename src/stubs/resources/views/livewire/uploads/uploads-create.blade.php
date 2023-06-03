<div class="container">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manta.uploads.list') }}">Uploads</a></li>
            <li class="breadcrumb-item active" aria-current="page">Toevoegen</li>
        </ol>
    </nav>

    @if (count(config('manta-cms.locales')) > 1 && $item)
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
          <a class="nav-link {{ $pid == null ? 'active' : null }}" aria-current="page" href="{{ route('manta.uploads.update', ['input' => $pid]) }}">{{ config('manta-cms.locales')[config('manta-cms.locale')]['language'] }} <span class="{{ config('manta-cms.locales')[config('manta-cms.locale')]['css'] }}"></span></a>
        </li>
        @foreach (config('manta-cms.locales') as $key => $value)
            @if($key != config('manta-cms.locale'))
        <li class="nav-item">
          <a class="nav-link {{ $pid && $key == $locale ? 'active' : null }}" href="{{ route('manta.uploads.update', ['locale' => $key, 'input' => $item->id]) }}">{{ $value['language'] }} <span class="{{ $value['css'] }}"></span></a>
        </li>
            @endif
        @endforeach
      </ul>
    @endif
    <form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target)))">
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Titel</label>
            <div class="col-sm-4">
                <input type="text" class="form-control form-control-sm @error('title')is-invalid @enderror"
                    id="title" wire:model="title">
                @error('title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <label for="initials" class="col-sm-1 col-form-label"></label>
            <div class="col-sm-5">
                @if ($item && $locale != config('manta-cms.locale'))
                <em>{!! $item->translation()['get']->title !!}</em>
                @endif
            </div>
        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Bestand</label>
            <div class="col-sm-4">
                <input type="file" wire:model="files" multiple>
                @error('files')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <label for="initials" class="col-sm-1 col-form-label"></label>
            <div class="col-sm-5">

            </div>
        </div>
        @if ($files)
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-10">
                @foreach ($files as $file)
                <div class="mantaUploadsDiv ui-sortable-handle" data-id="d4493e00-a81f-11eb-96a3-6f0d8db49bb5">
                    <a href="javascript:;"
                        {{-- onclick="$('#uploadsModal').modal('show'); Livewire.emit('uploadPopupEdit', 'd4493e00-a81f-11eb-96a3-6f0d8db49bb5')" --}}
                        >
                        <div class="mantaUploadsImage mantaUploadsItem"
                            style="background-image: url('{{ $file->temporaryUrl() }}'); ">
                        </div>
                    </a>
                    <div class="mantaUploadscomments">
                        <a href="javascript:;"
                        {{-- wire:click="main('d4493e00-a81f-11eb-96a3-6f0d8db49bb5')" --}}
                        ><i
                                class="fa-solid fa-pen-to-square  text-danger  " data-bs-toggle="tooltip"
                                data-bs-placement="top" aria-label="Hoofd bestand in de lijst"></i></a>
                        <a
                            {{-- href="https://luthermuseum.nl/cms/uploads/cropper/d4493e00-a81f-11eb-96a3-6f0d8db49bb5?redirect=https%3A%2F%2Fluthermuseum.nl%2F%2Fcms%2Fsliders%2Fedit%2F3d431c80-a81f-11eb-b394-4b9eaec0d544" --}}
                            ><i
                                class="fa-solid fa-pen-to-square" data-bs-toggle="tooltip" data-bs-placement="top"
                                aria-label="Pas het formaat van het plaatje aan"></i></a> <br>
                                {{ $file->getClientOriginalName() }}
                    </div>
                </div>
            @endforeach
            </div>
        </div>
        @endif
        <div class="mb-3 row">
            <div class="col-sm-12">
                {{-- @include('includes.form_error') --}}
                <input class="btn btn-sm btn-primary" type="submit" value="Opslaan" wire:loading.class="btn-secondary"
                    wire:loading.attr="disabled" />
            </div>
        </div>
    </form>
</div>

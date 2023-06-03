<div class="container">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manta.uploads.list') }}">Uploads</a></li>
            <li class="breadcrumb-item active" aria-current="page">Aanpassen</li>
        </ol>
    </nav>

    @if ($redirect)
        <a href="{{ url($redirect) }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-arrow-left"></i> Ga
            terug</a>
    @endif

    <form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target)))">
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-10">
                @if ($item->image()['src'])
                    <a href="{{ $item->image()['url'] }}" data-fancybox="gallery"
                        data-caption="{{ $item->title }}"><img src="{{ $item->image()['src'] }}"
                            style="height: 150px;"></a>
                @elseif ($item->pages > 0)
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($item->pdfGetImages() as $pdfImage)
                        @php
                            $i++;
                            $uploadspath = Storage::disk($item->disk)->get($pdfImage);
                            $data = (string) Image::make($uploadspath)->encode('data-url');
                        @endphp
                        <a href="{{ $data }}" data-fancybox="gallery"
                            data-caption="Pagina {{ $i . '/' . $item->pages }}"><img src="{{ $data }}"
                                class="mantaImgCropped_100"></a>
                    @endforeach
                @else
                    <div class="ps-4 fs-3">{!! $item->getIcon() !!}</div>
                @endif
            </div>
        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Aangemaakt</label>
            <div class="col-sm-4">
                {{ Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }} {{ $item->created_by }}
            </div>
            <label for="title" class="col-sm-2 col-form-label">Aangepast</label>
            <div class="col-sm-4">
                {{ Carbon\Carbon::parse($item->updated_at)->format('d-m-Y H:i') }} {{ $item->created_by }}
            </div>
        </div>
        {{-- <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Disk</label>
            <div class="col-sm-4">
                {{ $item->disk }}
            </div>
            <label for="title" class="col-sm-2 col-form-label">Host</label>
            <div class="col-sm-4">
                {{ $item->host }}
            </div>
        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Model</label>
            <div class="col-sm-4">
                {{ $item->model }}
            </div>
            <label for="title" class="col-sm-2 col-form-label">Parent ID</label>
            <div class="col-sm-4">
                {{ $item->pid }}
            </div>
        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Extensie</label>
            <div class="col-sm-4">
                {{ $item->extension }}
            </div>
            <label for="title" class="col-sm-2 col-form-label">Identifier</label>
            <div class="col-sm-4">
                {{ $item->identifier }}
            </div>
        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Locatie</label>
            <div class="col-sm-4">
                {{ $item->location . $item->filename }}
            </div>
            <label for="title" class="col-sm-2 col-form-label">Originele naam</label>
            <div class="col-sm-4">
                {{ $item->originalName }}
            </div>

        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Gebruiker ID</label>
            <div class="col-sm-4">
                {{ $item->user_id }}
            </div>
            <label for="title" class="col-sm-2 col-form-label">Bedrijf ID</label>
            <div class="col-sm-4">
                {{ $item->company_id }}
            </div>
        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">MIME type</label>
            <div class="col-sm-4">
                {{ $item->mime }}
            </div>
            <label for="title" class="col-sm-2 col-form-label">Grootte</label>
            <div class="col-sm-4">
                {{ $item->convert_filesize() }}
            </div>
        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Basis taal</label>
            <div class="col-sm-4">
                {{ $item->locale }}
            </div>
            <label for="title" class="col-sm-2 col-form-label">Hoofdbestand</label>
            <div class="col-sm-4">
                {{ $item->main }}
            </div>
        </div> --}}
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Titel</label>
            <div class="col-sm-4">
                <input type="text" class="form-control form-control-sm @error('title')is-invalid @enderror"
                    id="title" wire:model.defer="title">
                @error('title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <label for="seo_title" class="col-sm-2 col-form-label">SEO Titel</label>
            <div class="col-sm-4">
                <input type="text" class="form-control form-control-sm @error('seo_title')is-invalid @enderror"
                    id="seo_title" wire:model.defer="seo_title">
                @error('seo_title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="mb-3 row">
            <label for="comments" class="col-sm-2 col-form-label">Opmerkingen</label>
            <div class="col-sm-4">
                <textarea class="form-control form-control-sm @error('comments')is-invalid @enderror" id="comments"
                    wire:model.defer="comments"></textarea>
                @error('comments')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <label for="seo_title" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-4">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="title" class="col-sm-2 col-form-label">Actie</label>
            <div class="col-sm-4">

                @error('action')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <label for="seo_title" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-4">

            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-12">
                {{-- @include('includes.form_error') --}}
                <input class="btn btn-sm btn-primary" type="submit" value="Opslaan" wire:loading.class="btn-secondary"
                    wire:loading.attr="disabled" />


                <a href="{{ route('file.download', $item) }}" target="download" download
                    class="btn btn-sm btn-secondary"><i class="fa-solid fa-download"></i> Download</a>
            </div>
        </div>
    </form>
</div>

<div class="container">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manta.uploads.list') }}">Uploads</a></li>
            <li class="breadcrumb-item active" aria-current="page"><em>{!! $item->translation()['get']->title !!}</em> aanpassen</li>
        </ol>
    </nav>
    @if (count(config('manta-cms.locales')) > 1)
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ config('manta-cms.locale') == $locale ? 'active' : null }}" aria-current="page"
                    href="{{ route('manta.uploads.update', ['input' => $item->translation()['org']->id]) }}">{{ config('manta-cms.locales')[config('manta-cms.locale')]['language'] }} <span
                        class="{{ config('manta-cms.locales')[config('manta-cms.locale')]['css'] }}"></span></a>
            </li>
            @foreach (config('manta-cms.locales') as $key => $value)
                @if ($key != config('manta-cms.locale'))
                    <li class="nav-item">
                        <a class="nav-link {{ $key == $locale ? 'active' : null }}"
                            href="{{ route('manta.uploads.update', ['locale' => $key, 'input' => $item->id]) }}">{{ $value['language'] }}
                            <span class="{{ $value['css'] }}"></span></a>
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
                @if ($item->locale != config('manta-cms.locale'))
                <em>{!! $item->translation()['get']->title !!}</em>
                @endif
            </div>
        </div>

        <div class="mb-3 row">
            <div class="col-sm-12">
                {{-- @include('includes.form_error') --}}
                <input class="btn btn-primary" type="submit" value="Opslaan" wire:loading.class="btn-secondary"
                    wire:loading.attr="disabled" />
            </div>
        </div>
    </form>
</div>

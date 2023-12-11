<div class="container">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manta.uploads.list') }}">Uploads</a></li>
            <li class="breadcrumb-item active" aria-current="page">Developer</li>
        </ol>
    </nav>

    <div class="mb-3 row">
        <label for="url" class="col-sm-1 col-form-label">Vervang url</label>
        <div class="col-sm-4">
            <select class="form-control form-control-sm" wire:model="url">
                <option value="">Kies</option>
                @foreach ($urlGroup as $value)
                    <option value="{{ $value->url }}">{{ $value->url }}</option>
                @endforeach
            </select>
            @error('url')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <label for="urlNew" class="col-sm-2 col-form-label">Vervang door</label>
        <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm @error('urlNew')is-invalid @enderror"
                id="urlNew" wire:model="urlNew">
            @error('urlNew')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="mb-3 row">
        <label for="url" class="col-sm-1 col-form-label">Totaal ({{ $urlGroupResult }})</label>
        <div class="col-sm-5">
            {{ $url ? $url : 'waar NULL' }}
        </div>
        <label for="url" class="col-sm-1 col-form-label"></label>
        <div class="col-sm-5">
            {{ $urlNew }}
        </div>
    </div>
    <div class="mb-3 row">
        <label for="url" class="col-sm-1 col-form-label"></label>
        <div class="col-sm-5">
            <button class="btn btn-sm btn-danger" wire:click="replaceUrl">Vervang URL</button>
        </div>
        <label for="url" class="col-sm-1 col-form-label"></label>
        <div class="col-sm-5">

        </div>
    </div>
</div>

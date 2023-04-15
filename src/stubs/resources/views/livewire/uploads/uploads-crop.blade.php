<div class="container">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manta.uploads.list') }}">Uploads</a></li>
            <li class="breadcrumb-item active" aria-current="page"><em>{!! $item->translation()['get']->title !!}</em> </li>
        </ol>
    </nav>
    <style>
        img {
            display: block;
            /* This rule is very important, please don't ignore this */
            max-height: 400px;
            max-width: 400px;
        }
    </style>
    <div class="row" wire:ignore>
        <div class="col-6">
            <img src="{{ $this->image }}" id="image">
        </div>
        <div class="col-1">
        </div>
        <div class="col-5">
            <img id="cropped" src="" alt="">
        </div>
    </div>
    <form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target)))">
        <div class="row mt-4">
            <div class="col">


            </div>
        </div>
    <div class="row mt-4">
        <div class="col">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-floppy-disk"></i>
                    Opslaan</button>
                <a href="javascript:;" id="download" download="plaaje.jpg" class="btn btn-sm btn-primary"><i
                        class="fa-solid fa-download"></i> Download</a>
                <textarea name="newimage" id="newimage" class="d-none"></textarea>

        </div>
    </div>
</form>
    @push('scripts')
        <script>
            const image = document.getElementById('image');
            cropper = new Cropper(image, {
                // aspectRatio: 16 / 9,
                crop(event) {
                    // console.log(event.detail.x);
                    // console.log(event.detail.y);
                    // console.log(event.detail.width);
                    // console.log(event.detail.height);
                    // console.log(event.detail.rotate);
                    // console.log(event.detail.scaleX);
                    // console.log(event.detail.scaleY);

                    let imgSrc = cropper.getCroppedCanvas({
                        width: 200 // input value
                    }).toDataURL("image/jpeg", 0.7);

                    document.getElementById("cropped").src = imgSrc;
                    document.getElementById("download").href = imgSrc;
                    document.getElementById("newimage").value = imgSrc;
                },
            });
        </script>
    @endpush
</div>

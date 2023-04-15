<div>
    <div class="row">
        <div class="col">
            <form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target))">
                <input type="file" wire:model="documents" multiple>
                @error('documents.*')
                    <span class="error">{{ $message }}</span>
                @enderror
                <button type="submit">Opslaan</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="sortableUploadsSubuploads ui-sortable" data-id="3d431c80-a81f-11eb-b394-4b9eaec0d544"
                id="sortableUploadsSubuploads3d431c80-a81f-11eb-b394-4b9eaec0d544">


                @if ($documents)
                @foreach ($documents as $photo)
                <p>
                    {!! dd($photo) !!}
            </p>
                <div class="mantaUploadsDiv ui-sortable-handle" data-id="d4493e00-a81f-11eb-96a3-6f0d8db49bb5">
                    <a href="javascript:;"
                        {{-- onclick="$('#uploadsModal').modal('show'); Livewire.emit('uploadPopupEdit', 'd4493e00-a81f-11eb-96a3-6f0d8db49bb5')" --}}
                        >

                        <div class="mantaUploadsImage mantaUploadsItem"
                            style="background-image: url('{{ $photo->temporaryUrl() }}'); ">
                        </div>
                    </a>
                    <div class="mantaUploadsDescription">
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
                                {{ $photo->getClientOriginalName() }}
                    </div>
                </div>
                @endforeach
            @endif


            </div>


        </div>
    </div>
</div>

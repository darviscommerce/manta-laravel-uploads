<div>
    <form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target)))">
        <div class="row">
            <div class="col">
                <input type="file" wire:model="documents" multiple>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
                @error('documents.*')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="mt-2 row">
            <div class="col">
                @if ($documents)
                    <p>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i>
                            Uploads opslaan</button>
                    </p>
                @endif
            </div>
        </div>
    </form>
    <div class="mt-2 row">
        <div class="col">
            <div class="sortableUploadsSubuploads ui-sortable" data-id="3d431c80-a81f-11eb-b394-4b9eaec0d544"
                id="sortableUploadsSubuploads3d431c80-a81f-11eb-b394-4b9eaec0d544">
                @if ($documents)

                    @foreach ($documents as $photo)
                        <div class="mantaUploadsDiv ui-sortable-handle" data-id="d4493e00-a81f-11eb-96a3-6f0d8db49bb5">
                            <a href="javascript:;" {{-- onclick="$('#uploadsModal').modal('show'); Livewire.emit('uploadPopupEdit', 'd4493e00-a81f-11eb-96a3-6f0d8db49bb5')" --}}>

                                <div class="mantaUploadsImage mantaUploadsItem"
                                    style="background-image: url('{{ $photo->temporaryUrl() }}'); ">
                                </div>
                            </a>
                            <div class="mantaUploadsDescription">
                                {{-- <a href="javascript:;"><i class="fa-solid fa-pen-to-square text-danger "
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Hoofd bestand in de lijst"></i></a>
                                <a href="javascript:;"><i class="fa-solid fa-pen-to-square" data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        aria-label="Pas het formaat van het plaatje aan"></i></a>
                                <br> --}}
                                {{ substr($photo->getClientOriginalName(), 0, 15) }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>

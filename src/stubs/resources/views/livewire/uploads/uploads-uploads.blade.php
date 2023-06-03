<div class="sortableUploadsSubuploads">
    @foreach ($items as $upload)
        <div class="mantaUploadsDiv ui-sortable-handle" data-id="{{ $upload->id }}">
            <a href="{{ $upload->full_path() }}" data-fancybox="gallery" data-caption="{{ $upload->title }}">
                <div class="mantaUploadsImage mantaUploadsItem"
                    style="background-image: url('{{ $upload->full_path() }}'); ">
                </div>
            </a>
            <div class="mantaUploadsDescription">
                @if ($deleteId && $deleteId == $upload->id)
                    Weet u het zeker?
                    <button class="btn btn-sm btn-success" wire:click="deleteConfirm('{{ $upload->id }}')">Ja</button>
                    <button class="btn btn-sm btn-danger" wire:click="deleteCancel('{{ $upload->id }}')">Nee</button>
                @else
                    <a href="{{ route('manta.uploads.update', ['input' => $upload->id, 'redirect' => url()->full()]) }}"
                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Aanpassen"><i
                            class="fa-solid fa-pen-to-square text-warning"></i></a>
                    <a href="javascript:;" wire:click="main('{{ $upload->id }}')" data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-title="{{ $upload->main ? ' Hoofd plaatje' : ' Gebruik als hoofdplaatje' }}"><i
                            class="fa-solid fa-house {{ $upload->main ? ' text-success' : ' text-black' }}"></i></a>
                    <a href="javascript:;" wire:click="delete('{{ $upload->id }}')" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="Verwijderen"><i class="fa-solid fa-trash-can"></i></a>

                    <br>
                    {{ substr($upload->title, 0, 15) }}
                @endif
            </div>
        </div>
    @endforeach

    @push('scripts')
        <script>
            $(function() {
                $(".sortableUploadsSubuploads").sortable({
                    update: function(event, ui) {
                        var postData = $(this).sortable('toArray', {
                            attribute: 'data-id'
                        });
                        @this.set('orderjson', JSON.stringify({
                            items: postData
                        }));
                    }
                });
                $(this).disableSelection();
            });
        </script>
    @endpush
</div>

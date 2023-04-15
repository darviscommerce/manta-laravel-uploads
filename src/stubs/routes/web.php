
Route::group(['prefix' => config('manta-cms.prefix'), 'middleware' => config('manta-cms.middleware')], function () {
    Route::get('/uploads', App\Http\Livewire\Uploads\UploadsList::class)->name('manta.uploads.list');
    Route::get('/uploads/toevoegen', App\Http\Livewire\Uploads\UploadsCreate::class)->name('manta.uploads.create');
    Route::get('/uploads/aanpassen/{input}', App\Http\Livewire\Uploads\UploadsUpdate::class)->name('manta.uploads.update');
});

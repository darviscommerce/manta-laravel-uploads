
Route::group(['prefix' => config('manta-cms.prefix'), 'middleware' => config('manta-cms.middleware')], function () {
    Route::get('/uploads', App\Http\Livewire\Uploads\UploadsList::class)->name('manta.uploads.list');
    Route::get('/uploads/toevoegen', App\Http\Livewire\Uploads\UploadsCreate::class)->name('manta.uploads.create');
    Route::get('/uploads/aanpassen/{input}', App\Http\Livewire\Uploads\UploadsUpdate::class)->name('manta.uploads.update');
    Route::get('/uploads/crop/{input}', App\Http\Livewire\Uploads\UploadsCrop::class)->name('manta.uploads.crop');
});

/**
 * * Downloads
 */
Route::get('/file/download/{uploads}', [App\Http\Controllers\MantaUploadController::class, 'download'])->name('file.download');
Route::get('/file/serve/{uploads}', [App\Http\Controllers\MantaUploadController::class, 'serve'])->name('file.serve');

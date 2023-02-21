<?php

use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsCreate;
use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsList;
use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsUpdate;
use Illuminate\Support\Facades\Route;


Route::get('/pagina', UploadsList::class)->name('manta.uploads.list');
Route::get('/pagina/toevoegen', UploadsCreate::class)->name('manta.uploads.create');
Route::get('/pagina/aanpassen/{input}', UploadsUpdate::class)->name('manta.uploads.update');

<?php

use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsCreate;
use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsList;
use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsUpdate;
use Illuminate\Support\Facades\Route;


Route::get('/uploads', UploadsList::class)->name('manta.uploads.list');
Route::get('/uploads/toevoegen', UploadsCreate::class)->name('manta.uploads.create');
Route::get('/uploads/aanpassen/{input}', UploadsUpdate::class)->name('manta.uploads.update');

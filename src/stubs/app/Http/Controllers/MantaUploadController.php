<?php

namespace App\Http\Controllers;

use Manta\LaravelUploads\Models;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MantaUploadController extends Controller
{
    /**
     * @param MantaUpload $uploads
     * @return mixed
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function download(MantaUpload $uploads): mixed
    {
        if (Auth::user()) {
            return Storage::disk($uploads->disk)->download($uploads->location . $uploads->filename);
        } else {
            // Don't make the visitors wiser
            return abort('404');
        }
    }

    /**
     * @param MantaUpload $uploads
     * @return mixed
     * @throws BindingResolutionException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function serve(MantaUpload $uploads): mixed
    {
        if (Auth::user()) {
            $uploadspath = Storage::disk($uploads->disk)->get($uploads->location . $uploads->filename);
            return response($uploadspath, 200)->header('Content-Type', $uploads->mime);
        } else {
            // Don't make the visitors wiser
            return abort('404');
        }
    }
}

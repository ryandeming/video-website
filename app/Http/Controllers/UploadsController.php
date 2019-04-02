<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Upload;

class UploadsController extends Controller
{
    public function link() {
        $openload = Upload::linkUploads();

        return $openload;
    }
}

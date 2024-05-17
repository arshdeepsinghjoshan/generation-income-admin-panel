<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function imageUpload(Request $request, $file, $dir = '/public/uploads')
    {
        $data = null; // Initialize data variable

        // Check if file is present in the request
        if ($request->hasFile($file)) {
            // Retrieve the file
            $uploadedFile = $request->file($file);

            // Generate unique filename
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();

            // Move the file to the specified directory
            $uploadedFile->move(base_path() . $dir, $filename);

            // Assign the filename to $data
            $data = $filename;
        }

        return $data; // Return the filename or null if no file was uploaded
    }
}

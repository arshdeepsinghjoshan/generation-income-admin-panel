<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionMethod;

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


    public function relationTable(Request $request, $id = null)
    {
        // Retrieve the relation and model instance
        $relation = $request->relation;
        $modelInstance = $request->modelType::find($request->modelId);

        // Check if the model instance exists
        if (!$modelInstance) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        // Use reflection to get the related model class name
        $reflectionMethod = new ReflectionMethod($modelInstance, $relation);
        $relationInstance = $reflectionMethod->invoke($modelInstance);
        $relatedModelClass = get_class($relationInstance->getRelated());

        // Retrieve related data
        $relatedData = $modelInstance->$relation;

        // Ensure related data is always treated as a collection
        $relatedCollection = $relatedData instanceof \Illuminate\Database\Eloquent\Collection ?
            $relatedData : ($relatedData !== null ? collect([$relatedData]) : collect());

        // Call the relationGridView method on the related model class
        return (new $relatedModelClass())->relationGridView($relatedCollection, $request);
    }
}

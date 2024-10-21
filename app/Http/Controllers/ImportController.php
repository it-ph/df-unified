<?php

namespace App\Http\Controllers;

use App\Imports\SLAImport;
use App\Imports\UserImport;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;
use App\Http\Requests\ImportRequest;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    use ResponseTraits;

    public function importUser(Request $request)
    {
        $path = $request->file('import_file')->getRealPath();

        $import = new UserImport;
        Excel::import($import, $path);

        $errors = $import->getErrors();
        $errorsArray = array_unique($errors);

        if (count($errorsArray) > 1) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Import failed due to validation errors.',
                'error' => $errorsArray
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User uploaded successfully!'
        ]);
    }

    public function importSLA(Request $request)
    {
        $path = $request->file('import_file')->getRealPath();

        $import = new SLAImport;
        Excel::import($import, $path);

        $errors = $import->getErrors();
        $errorsArray = array_unique($errors);

        if (count($errorsArray) > 1) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Import failed due to validation errors.',
                'error' => $errorsArray
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Request Type uploaded successfully!'
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\RequestType;
use Illuminate\Support\Facades\Auth;

class RequestTypesServices
{
    public function load()
    {
        $datastorage = [];
        $request_types = RequestType::with([
            'thecreatedby:id,first_name,last_name',
            'theupdatedby:id,first_name,last_name',
        ])
        ->get();

        foreach($request_types as $value) {
            $name = $value->name;
            $created_by = $value->thecreatedby ? $value->thecreatedby->full_name : '-';
            $created_at = $value->created_at ? date('d-M-y h:i:s a', strtotime($value->created_at)) : '-';
            $updated_by = $value->theupdatedby ? $value->theupdatedby->full_name : '-';
            $updated_at = $value->updated_at ? date('d-M-y h:i:s a', strtotime($value->updated_at)) : '-';
            $status = $value->status == 'active' ? '<span class="text-success"><strong>Active</strong></span>' : '<label class="text-danger"><strong>Inactive</strong></label>';
            $action ='<button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Edit Request Type" onclick=REQUEST_TYPE.show('.$value->id.')><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" title="Delete Request Type" onclick=REQUEST_TYPE.destroy('.$value->id.')><i class="fas fa-times"></i></button>';

            $datastorage[] = [
                'id' => $value->id,
                'name' => $name,
                'created_at' => $created_at,
                'created_by' => $created_by,
                'updated_at' => $updated_at,
                'updated_by' => $updated_by,
                'status' => $status,
                'action' => $action,
            ];
        }

        return $datastorage;
    }
}

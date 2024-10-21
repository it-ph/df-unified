<?php

namespace App\Services;

use App\Models\RequestVolume;
use Illuminate\Support\Facades\Auth;

class RequestVolumesServices
{
    public function load()
    {
        $datastorage = [];
        $request_volumes = RequestVolume::with([
            'thecreatedby:id,first_name,last_name',
            'theupdatedby:id,first_name,last_name',
        ])
        ->get();

        foreach($request_volumes as $value) {
            $name = $value->name;
            $created_by = $value->thecreatedby ? $value->thecreatedby->full_name : '-';
            $created_at = $value->created_at ? date('d-M-y h:i:s a', strtotime($value->created_at)) : '-';
            $updated_by = $value->theupdatedby ? $value->theupdatedby->full_name : '-';
            $updated_at = $value->updated_at ? date('d-M-y h:i:s a', strtotime($value->updated_at)) : '-';
            $status = $value->status == 'active' ? '<span class="text-success"><strong>Active</strong></span>' : '<label class="text-danger"><strong>Inactive</strong></label>';
            $action ='<button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Edit Request Volume" onclick=REQUEST_VOLUME.show('.$value->id.')><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" title="Delete Request Volume" onclick=REQUEST_VOLUME.destroy('.$value->id.')><i class="fas fa-times"></i></button>';

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

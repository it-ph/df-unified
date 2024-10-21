<?php

namespace App\Services;

use App\Models\RequestSLA;
use Illuminate\Support\Facades\Auth;

class RequestSLAsServices
{
    public function load()
    {
        $datastorage = [];
        $request_slas = RequestSLA::with([
            'therequesttype:id,name',
            'therequestvolume:id,name',
            'thecreatedby:id,first_name,last_name',
            'theupdatedby:id,first_name,last_name',
        ])
        ->get();

        foreach($request_slas as $value) {
            $request_type = $value->therequesttype ? $value->therequesttype->name : '-';
            $num_pages = $value->therequestvolume ? $value->therequestvolume->name : '-';
            $agreed_sla = $value->agreed_sla;
            $created_by = $value->thecreatedby ? $value->thecreatedby->full_name : '-';
            $created_at = $value->created_at ? date('d-M-y h:i:s a', strtotime($value->created_at)) : '-';
            $updated_by = $value->theupdatedby ? $value->theupdatedby->full_name : '-';
            $updated_at = $value->updated_at ? date('d-M-y h:i:s a', strtotime($value->updated_at)) : '-';
            $status = $value->status == 'active' ? '<span class="text-success"><strong>Active</strong></span>' : '<label class="text-danger"><strong>Inactive</strong></label>';
            $action ='<button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Edit Request SLA" onclick=REQUEST_SLA.show('.$value->id.')><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" title="Delete Request SLA" onclick=REQUEST_SLA.destroy('.$value->id.')><i class="fas fa-times"></i></button>';

            $datastorage[] = [
                'id' => $value->id,
                'request_type' => $request_type,
                'num_pages' => $num_pages,
                'agreed_sla' => $agreed_sla,
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

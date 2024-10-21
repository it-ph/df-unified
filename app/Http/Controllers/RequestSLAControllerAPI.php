<?php

namespace App\Http\Controllers;

use App\Models\RequestSLA;
use Illuminate\Http\Request;

class RequestSLAControllerAPI extends Controller
{
    // GET ALL SLAs
    public function getAllSLAs(Request $request)
    {
        if($request->ajax())
        {
            $request_slas = RequestSLA::with([
                'therequesttype:id,name',
                'therequestvolume:id,name',
                'thecreatedby:id,first_name,last_name',
                'theupdatedby:id,first_name,last_name',
            ]);

            return datatables($request_slas)
                ->editColumn('request_type_id', (function($value){
                    return $value->therequesttype ? $value->therequesttype->name : '-';
                }))
                ->editColumn('request_volume_id', (function($value){
                    return $value->therequestvolume ? $value->therequestvolume->name : '-';
                }))
                ->editColumn('created_by', (function($value){
                    return $value->thecreatedby ? $value->thecreatedby->full_name : '-';
                }))
                ->editColumn('created_at', (function($value){
                    return $value->created_at ? date('d-M-y h:i:s a', strtotime($value->created_at)) : '-';
                }))
                ->editColumn('updated_by', (function($value){
                    return $value->theupdatedby ? $value->theupdatedby->full_name : '-';
                }))
                ->editColumn('updated_at', (function($value){
                    return $value->updated_at ? date('d-M-y h:i:s a', strtotime($value->updated_at)) : '-';
                }))
                ->editColumn('status', (function($value){
                    return $value->status == 'active' ? '<span class="text-success"><strong>Active</strong></span>' : '<label class="text-danger"><strong>Inactive</strong></label>';
                }))
                ->addColumn('action', (function($value){
                        return '<button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Edit Request SLA" onclick=REQUEST_SLA.show('.$value->id.')><i class="fas fa-pencil-alt"></i></button>
                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" title="Delete Request SLA" onclick=REQUEST_SLA.destroy('.$value->id.')><i class="fas fa-times"></i></button>';
                }))
                ->rawColumns(
                [
                    'action',
                ])
                ->escapeColumns([])
                ->make(true);
        }
    }
}

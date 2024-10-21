<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestVolume;

class RequestVolumeControllerAPI extends Controller
{
    // GET ALL VOLUMES
    public function getAllVolumes(Request $request)
    {
        if($request->ajax())
        {
            $request_volumes = RequestVolume::with([
                'thecreatedby:id,first_name,last_name',
                'theupdatedby:id,first_name,last_name',
            ])
            ->orderBy('id','asc');

            return datatables($request_volumes)
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
                    return '<button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Edit Request Volume" onclick=REQUEST_VOLUME.show('.$value->id.')><i class="fas fa-pencil-alt"></i></button>
                        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" title="Delete Request Volume" onclick=REQUEST_VOLUME.destroy('.$value->id.')><i class="fas fa-times"></i></button>';
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

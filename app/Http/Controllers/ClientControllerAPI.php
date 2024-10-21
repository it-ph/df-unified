<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientControllerAPI extends Controller
{
    // GET ALL Clients
    public function getAllClients(Request $request)
    {
        if($request->ajax())
        {
            $clients = Client::with([
                'thecreatedby:id,first_name,last_name',
                'theupdatedby:id,first_name,last_name',
            ]);

            return datatables($clients)
                ->editColumn('workshift', (function($value){
                    return Carbon::parse($value->start)->format('h:i:s a').' to '.Carbon::parse($value->end)->format('h:i:s a');;
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
                ->addColumn('action', (function($value){
                        return '<button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Edit Client" onclick=CLIENT.show('.$value->id.')><i class="fas fa-pencil-alt"></i></button>
                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" title="Delete Client" onclick=CLIENT.destroy('.$value->id.')><i class="fas fa-times"></i></button>';
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

<?php

namespace App\Http\Controllers;

use App\Models\RequestSLA;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;
use App\Services\RequestSLAsServices;
use App\Http\Requests\RequestSLAStoreRequest;
use App\Http\Controllers\GlobalVariableController;

class RequestSLAController extends GlobalVariableController
{
    use ResponseTraits;

    public function __construct()
    {
        parent::__construct();
        $this->model = new RequestSLA();
        $this->service = new RequestSLAsServices();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->successResponse('Request SLAs loaded successfully!');
        try
        {
            $result["data"] =  $this->service->load();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function store(RequestSLAStoreRequest $request)
    {
        $result = $this->successResponse('Request SLA created successfully!');
        try{
            if($request->edit_id === null)
            {
                $request['created_by'] = auth()->user()->id;
                $request_sla = RequestSLA::create($request->except('edit_id'));
            }
            else
            {
                $request['updated_by'] = auth()->user()->id;
                $result = $this->update($request, $request->edit_id);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function show($id)
    {
        $result = $this->successResponse('Request SLA retrieved successfully!');
        try {
            $result["data"] = RequestSLA::findOrfail($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function update($request, $id)
    {
        $result = $this->successResponse('Request SLA updated successfully!');
        try {
            RequestSLA::findOrfail($id)->update($request->except('edit_id'));
        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RequestSLA  $request_sla
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = RequestSLA::findOrfail($id);
        $result = $this->successResponse('Request SLA deleted successfully!');
        try {
            $client->delete();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function get($typeId, $volumeId)
    {
        $sla = RequestSLA::query()
            ->where('request_type_id', $typeId)
            ->where('request_volume_id', $volumeId)
            ->where('status','active')
            ->select('id','request_type_id','request_volume_id','agreed_sla','status')
            ->first();

        // $sla = $sla ? $sla : 'not found';

        return $sla;
    }
}

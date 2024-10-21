<?php

namespace App\Http\Controllers;

use App\Models\RequestType;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;
use App\Services\RequestTypesServices;
use App\Http\Requests\RequestTypeStoreRequest;

class RequestTypeController extends Controller
{
    use ResponseTraits;

    public function __construct()
    {
        $this->model = new RequestType();
        $this->service = new RequestTypesServices();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->successResponse('Request Types loaded successfully!');
        try
        {
            $result["data"] =  $this->service->load();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function store(RequestTypeStoreRequest $request)
    {
        $result = $this->successResponse('Request Type created successfully!');
        try{
            if($request->edit_id === null)
            {
                $request['created_by'] = auth()->user()->id;
                $request_type = RequestType::create($request->except('edit_id'));
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
        $result = $this->successResponse('Request Type retrieved successfully!');
        try {
            $result["data"] = RequestType::findOrfail($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function update($request, $id)
    {
        $result = $this->successResponse('Request Type updated successfully!');
        try {
            RequestType::findOrfail($id)->update($request->except('edit_id'));
        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RequestType  $request_type
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = RequestType::findOrfail($id);
        $result = $this->successResponse('Request Type deleted successfully!');
        try {
            $client->delete();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }
}

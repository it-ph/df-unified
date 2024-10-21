<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestVolume;
use App\Traits\ResponseTraits;
use App\Services\RequestVolumesServices;
use App\Http\Requests\RequestVolumeStoreRequest;

class RequestVolumeController extends Controller
{
    use ResponseTraits;

    public function __construct()
    {
        $this->model = new RequestVolume();
        $this->service = new RequestVolumesServices();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->successResponse('Request Volumes loaded successfully!');
        try
        {
            $result["data"] =  $this->service->load();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function store(RequestVolumeStoreRequest $request)
    {
        $result = $this->successResponse('Request Volume created successfully!');
        try{
            if($request->edit_id === null)
            {
                $request['created_by'] = auth()->user()->id;
                $request_volume = RequestVolume::create($request->except('edit_id'));
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
        $result = $this->successResponse('Request Volume retrieved successfully!');
        try {
            $result["data"] = RequestVolume::findOrfail($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function update($request, $id)
    {
        $result = $this->successResponse('Request Volume updated successfully!');
        try {
            RequestVolume::findOrfail($id)->update($request->except('edit_id'));
        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RequestVolume  $request_volume
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = RequestVolume::findOrfail($id);
        $result = $this->successResponse('Request Volume deleted successfully!');
        try {
            $client->delete();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }
}

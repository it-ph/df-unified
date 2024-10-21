<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Traits\ResponseTraits;
use App\Services\ClientsServices;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Controllers\GlobalVariableController;

class ClientController extends GlobalVariableController
{
    use ResponseTraits;

    public function __construct()
    {
        $this->model = new Client();
        $this->service = new ClientsServices();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->successResponse('Clients loaded successfully!');
        try
        {
            $result["data"] =  $this->service->load();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function store(ClientStoreRequest $request)
    {
        $result = $this->successResponse('Client created successfully!');
        try{
            if($request->edit_id === null)
            {
                $start = strtotime($request['start']);
                $end = strtotime($request['end']);
                $shift_hours = round($end - $start) / 3600;

                $request['shift_hours'] = $shift_hours;
                $request['created_by'] = auth()->user()->id;
                $client = Client::create($request->except('edit_id'));
            }
            else
            {
                $start = strtotime($request['start']);
                $end = strtotime($request['end']);
                $shift_hours = round($end - $start) / 3600;

                $request['shift_hours'] = $shift_hours;
                $request['updated_by'] = auth()->user()->id;
                $result = $this->update($request, $request->edit_id);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function updateEmailConfig(ClientStoreRequest $request)
    {
        $result = $this->successResponse('Configuration updated successfully!');
        try{

                $start = strtotime($request['start']);
                $end = strtotime($request['end']);
                $shift_hours = round($end - $start) / 3600;

                $request['shift_hours'] = $shift_hours;
                $request['updated_by'] = auth()->user()->id;
                Client::findOrfail($request['edit_id'])->update($request->except('edit_id'));

        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function show($id)
    {
        $result = $this->successResponse('Client retrieved successfully!');
        try {
            $result["data"] = Client::findOrfail($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function update($request, $id)
    {
        $result = $this->successResponse('client updated successfully!');
        try {
            Client::findOrfail($id)->update($request->except('edit_id'));
        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::findOrfail($id);
        $result = $this->successResponse('Client deleted successfully!');
        try {
            $client->delete();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }
}

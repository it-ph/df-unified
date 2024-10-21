<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;
use App\Services\DashboardServices;

class DashboardController extends Controller
{
    use ResponseTraits;

    public function __construct()
    {
        $this->dashboard = new DashboardServices();
    }

    public function index(Request $request)
    {
        $result = $this->successResponse('Dashboard data loaded successfully!');
        try
        {
            $date_range_selected = explode("to", $request['date_range']);
            $request['date_from'] = trim($date_range_selected[0]);
            $request['date_to'] = trim($date_range_selected[1]);

            $date_from =  Carbon::parse($request['date_from'])->format('Y-m-d');
            $date_to =  Carbon::parse($request['date_to'])->format('Y-m-d');

            $result["data"] =  $this->dashboard->dashboard($date_from, $date_to, $request->client_id, $request->platform, $request->request_type_id, $request->developer_id);
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }
}

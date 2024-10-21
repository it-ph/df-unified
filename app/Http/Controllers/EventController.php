<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;
use App\Http\Requests\EventStoreRequest;

class EventController extends Controller
{
    use ResponseTraits;

    public function __construct()
    {
        $this->model = new Event();
    }

    public function store(EventStoreRequest $request)
    {
        $result = $this->successResponse('Event created successfully!');
        try{
            if($request->edit_id === null)
            {
                $date_range = explode(' - ', $request['daterange']);
                $request['start'] = trim($date_range[0]);
                $request['end'] = trim($date_range[1]);

                $request['created_by'] = auth()->user()->id;
                $event = Event::create($request->except([
                    'edit_id',
                    'daterange'
                ]));
            }
            else
            {
                $date_range = explode(' - ', $request['daterange']);
                $request['start'] = trim($date_range[0]);
                $request['end'] = trim($date_range[1]);

                $request['updated_by'] = auth()->user()->id;
                $result = $this->update($request, $request->edit_id);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function isconflict(Request $request)
    {
        $date_range = explode(' - ', $request['date_range']);
        $request['start'] = trim($date_range[0]);
        $request['end'] = trim($date_range[1]);

        $start =  Carbon::parse($request['start'])->format('Y-m-d H:i:s');
        $end =  Carbon::parse($request['end'])->format('Y-m-d H:i:s');

        $event = Event::query()
            // ->clientevents()
            ->where('client_id', $request['client_id'])
            ->where('start','<=',$end)
            ->where('end','>=',$start)
            ->first();

        $is_conflict = $event ? 1 : 0;

        return response()->json([
            'is_conflict' => $is_conflict,
            'event' => $event,
            'status' => 200,
        ]);
    }

    public function show($id)
    {
        $result = $this->successResponse('Event retrieved successfully!');
        try {
            $result["data"] = Event::findOrfail($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function update($request, $id)
    {
        $result = $this->successResponse('Event updated successfully!');
        try {
            Event::findOrfail($id)->update($request->except([
                'edit_id',
                'daterange'
            ]));
        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    public function destroy($id)
    {
        $client = Event::findOrfail($id);
        $result = $this->successResponse('Event deleted successfully!');
        try {
            $client->delete();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }
}

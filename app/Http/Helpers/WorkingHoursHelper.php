<?php

namespace App\Http\Helpers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;

class WorkingHoursHelper {
    private $events;

    /** Getter and Setter */
    public function setEvents()
    {
        $events = Event::query()
            ->clientevents()
            ->select('id','title','description','client_id','start','end')
            ->get();

        $this->events = $events;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function get_set_events()
    {
        $this->setEvents();
        $events = $this->getEvents();

        return $events;
    }

    public function getWorkingHours($start_at, $end_at, $shift_start, $shift_end, $shift_hours)
    {
        // OLD JOB SERVICES
        // $start_at = strtotime($value->start_at);
        // $end_at = strtotime(Carbon::now());
        // $shift_start = $value->theclient->start;
        // $shift_end = $value->theclient->end;
        // $shift_hours = $value->theclient->shift_hours;

        // $working_hours = WorkingHoursHelper::getWorkingHours($start_at, $end_at, $shift_start, $shift_end, $shift_hours);
        // $time_taken = WorkingHoursHelper::convertTime($working_hours);

        // OLD AUDITLOG SERVICES
        // $start_at = strtotime($value->thejob->start_at);
        // $end_at = strtotime(Carbon::now());
        // $shift_start = $value->theclient->start;
        // $shift_end = $value->theclient->end;
        // $shift_hours = $value->theclient->shift_hours;

        // $working_hours = WorkingHoursHelper::getWorkingHours($start_at, $end_at, $shift_start, $shift_end, $shift_hours);
        // $time_taken = WorkingHoursHelper::convertTime($working_hours);


        // date_default_timezone_set("GMT");
        $events = $this->get_set_events();

        // set work_shift and convert to 12hr time format

        $shift_start = str_replace(":","",$shift_start);
        $shift_end = str_replace(":","",$shift_end);

        $shift_start_12r = date('h:i a', strtotime($shift_start));
        $shift_end_12r = date('h:i a', strtotime($shift_end));

        // shift hours
        $shift_hours = $shift_hours;

        // hours (in seconds) to remove from valid work days
        $remove_hours = (24 - $shift_hours) * 3600;

        // d-m-Y H:i:s format
        $start = $start_at;
        $end = $end_at;

        $startTime = date("H:i:s", $start);
        $startTyme = date("His", $start);
        $startDate = date("d-m-Y", $start);
        $startDay =  date("w", $start);

        $endTime = date("H:i:s", $end);
        $endTyme = date("His", $end);
        $endDate = date("d-m-Y", $end);
        $endDay =  date("w", $end);

        $workdays = array(1, 2, 3, 4, 5);
        $holidays = array(
            "01-01-2013", "29-03-2013", "01-04-2013", "06-05-2013", "27-05-2013", "26-08-2013",
            "25-12-2013", "26-12-2013",    "01-01-2014", "18-04-2014", "21-04-2014", "05-05-2014",
            "26-05-2014", "25-08-2014", "25-12-2014",    "26-12-2014", "01-01-2015", "03-04-2015",
            "06-04-2015", "04-05-2015", "25-05-2015", "31-08-2015",    "25-12-2015", "28-12-2015"
        );
        // includes UK bank holidays until end of 2015

        // echo '-- INPUT --<br>';
        // echo '$start = ' . date("D d-m-Y H:i:s", $start) . '<br>';
        // echo '$end = ' . date("D d-m-Y H:i:s", $end) . '<br>';

        // echo '<br>-- CHANGES --<br>';

        # -- $start -------------------------------------

        # if $start is before $shift_start_12r > move to $shift_start_12r
        if ($startTyme < $shift_start) {
            $start = strtotime($shift_start_12r, $start);
            // echo '$start is before $shift_start_12r > move to $shift_start_12r (' . date("D d-m-Y H:i:s", $start) . ')<br>';
            # elseif $start is after $shift_end_12r > move to $shift_start_12r next workday
        } elseif ($startTyme > $shift_end) {
            $start = strtotime("+1 weekday $shift_start_12r", $start);
            // echo '$start is after $shift_end_12r > move to $shift_start_12r next workday (' . date("D d-m-Y H:i:s", $start) . ')<br>';
        }

        # if $start is a holiday > move to next workday $shift_start_12r
        if (in_array(date("d-m-Y", $start), $holidays)) {
            $start = strtotime("+1 weekday $shift_start_12r", $start);
            // echo '$start is a holiday > move to next workday (' . date("D d-m-Y H:i:s", $start) . ')<br>';
            # Boxing Day Check
            if (date("d-m", $start) == "26-12" || date("d-m-Y", $start) == "28-12-2015") {
                $start = strtotime("+1 weekday $shift_start_12r", $start);
                // echo '$start is boxing day > move to next workday (' . date("D d-m-Y H:i:s", $start) . ')<br>';
            }
        }

        # if $start is a weekend > move to next workday $shift_start_12r
        if (!in_array(date("w", $start), $workdays)) {
            $start = strtotime("+1 weekday $shift_start_12r", $start);
            // echo '$start is a weekend > move to next workday (' . date("D d-m-Y H:i:s", $start) . ')<br>';
            # Holiday Check
            if (in_array(date("d-m-Y", $start), $holidays)) {
                $start = strtotime("+1 weekday " . date('H:i:s', $start) . "", $start);
                // echo '$start is a holiday > move to next workday (' . date("D d-m-Y H:i:s", $start) . ')<br>';
            }
        }

        # -- $end -------------------------------------

        # if $end is before $shift_start_12r > move to $shift_start_12r
        if ($endTyme < $shift_start) {
            $end = strtotime($shift_start_12r, $end);
            // echo '$end is before $shift_start_12r > move to $shift_start_12r (' . date("D d-m-Y H:i:s", $end) . ')<br>';
            # elseif $end is after $shift_end_12r > move to $shift_end_12r
        } elseif ($endTyme > $shift_end) {
            $end = strtotime($shift_end_12r, $end);
            // echo '$end is after $shift_end_12r > move to $shift_end_12r (' . date("D d-m-Y H:i:s", $end) . ')<br>';
        }

        # if $end is a holiday > move to last workday $shift_end_12r
        if (in_array(date("d-m-Y", $end), $holidays)) {
            $end = strtotime("-1 weekday $shift_end_12r", $end);
            // echo '$end is a holiday > move to last workday (' . date("D d-m-Y H:i:s", $end) . ')<br>';
            # Boxing Day Check
            if (date("d-m", $end) == "26-12" || date("d-m-Y", $end) == "28-12-2015") {
                $end = strtotime("-1 weekday $shift_end_12r", $end);
                // echo '$end is boxing day > move to last workday (' . date("D d-m-Y H:i:s", $end) . ')<br>';
            }
        }

        # if $end is a weekend > move to last workday $shift_end_12r
        if (!in_array(date("w", $end), $workdays)) {
            $end = strtotime("-1 weekday $shift_end_12r", $end);
            // echo '$end is a weekend > move to last workday (' . date("D d-m-Y H:i:s", $end) . ')<br>';
            # Holiday Check
            if (in_array(date("d-m-Y", $end), $holidays)) {
                $end = strtotime("-1 weekday $shift_end_12r", $end);
                // echo '$end is a holiday > move to last workday (' . date("D d-m-Y H:i:s", $end) . ')<br>';
            }
        }

        // echo '<br>-- OUTPUT --<br>';
        // echo '$start = ' . date("D d-m-Y H:i:s", $start) . '<br>';
        // echo '$end = ' . date("D d-m-Y H:i:s", $end) . '<br>';

        // echo '<br>-- INPUT DIFFERENCE --<br>';
        $diff = ($end - $start);
        // echo 'Difference in seconds is ' . $diff . '<br>';
        # work out hours, mins, secs
        # if $diff is negative > set to 0
        if ($diff < 0) {
            $diff = 0;
            $h = 0;
            $m = 0;
            $s = 0;
            // echo '$diff is negative > set to 0<br>';
        }

        // echo '$end - $start = ' . ($end - $start) . '<br>';
        $h = (int) ($diff / 3600);
        $m = (int) (($diff - $h * 3600) / 60);
        $s = (int) ($diff - $h * 3600 - $m * 60);
        // echo '' . $h . 'h ' . $m . 'm ' . $s . 's<br>';

        // echo '<br>-- LOOP<br>';
        $start12 = strtotime("12pm", $start);
        $end12 = strtotime("12pm", $end);
        $daysBetween = ceil(abs($end12 - $start12) / 86400);
        // echo '$daysBetween = ' . $daysBetween . '<br>';

        if ($daysBetween == 0) {

            // do nothing

        } elseif ($daysBetween == 1) {

            // echo 'Loop stage: ' . date("d-m-Y H:i:s", $start) . '<br>';

            # if $start is a valid workday and not a holiday
            if (in_array(date("w", $start), $workdays) && !in_array(date("d-m-Y", $start), $holidays)) {
                # remove remove_hours
                $diff -= $remove_hours;
                // echo '  > Removed remove_hours (is a valid day) for ' . date("d-m-Y H:i:s", $start) . '<br>';
            } else {
                # remove 24 hours
                $diff -= 86400;
                // echo '  > Removed 24 hours (isn\'t a valid day) for ' . date("d-m-Y H:i:s", $start) . '<br>';
            }
        } elseif ($daysBetween >= 2) {

            for ($i = 0; $i < $daysBetween; $i++) {

                // echo 'Loop stage: ' . date("d-m-Y H:i:s", $start) . '<br>';

                # if $start is a valid workday and not a holiday
                if (in_array(date("w", $start), $workdays) && !in_array(date("d-m-Y", $start), $holidays)) {
                    // echo '  > Removed remove_hours (is a valid day) for ' . date("d-m-Y H:i:s", $start) . '<br>';
                    $diff -= $remove_hours;
                } else {
                    # remove 24 hours
                    $diff -= 86400;
                    // echo '  > Removed 24 hours (isn\'t a valid day) for ' . date("d-m-Y H:i:s", $start) . '<br>';
                }

                $start = strtotime("+1 day", $start);
            }
        }

        // echo '<br>-- OUTPUT DIFFERENCE --<br>';
        // echo 'Difference in seconds is ' . $diff . '<br>';
        # work out hours, mins, secs
        # if $diff is negative > set to 0
        if ($diff < 0) {
            $diff = 0;
            $h = 0;
            $m = 0;
            $s = 0;
            // echo '$diff is negative > set to 0<br>';
        }

        // echo '$end - $start = ' . ($end - $start) . '<br>';
        $h = (int) ($diff / 3600);
        $m = (int) (($diff - $h * 3600) / 60);
        $s = (int) ($diff - $h * 3600 - $m * 60);
        // echo '' . $h . 'h ' . $m . 'm ' . $s . 's<br>';

        $hours = $diff/3600;
        // $working_hours = $this->convertTime($hours);
        // return $working_hours;

        return $hours;
    }

    public function convertTime($hours)
    {
        $ss = ($hours * 3600);
        $hh = floor($hours);
        $ss -= $hh * 3600;
        $mm = floor($ss / 60);
        $ss -= $mm * 60;

        return sprintf('%02d:%02d:%02d', $hh, $mm, $ss);
    }
}

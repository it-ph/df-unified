<?php

namespace App\Http\Helpers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;

class TimeElapsedHelper
{
    public function getWorkingHours()
    {
        // Client Work Shift
        $workStart = '07:00:00';
        $workEnd = '21:00:00';

        // Job Start & End Time
        $start = '2024-09-20 17:00:00';
        $end = '2024-09-23 17:00:00';

        // $end = null;
        // $end = $end == null ? Carbon::now()->format('Y-m-d H:i:s'): $end;

        // Job Pauses
        $pauses = [
            ['start' => new DateTime('2024-09-23 07:00:00'), 'end' => new DateTime('2024-09-23 08:00:00')],
        ];

        // Special Events within Job
        $specialEvents = [
            ['start' => new DateTime('2024-09-23 16:00:00'), 'end' => new DateTime('2024-09-23 16:30:00')],
        ];

        return $this->calculateWorkingTime($start, $end, $workStart, $workEnd, $pauses, $specialEvents);
    }

    public function calculateWorkingTime($start, $end, $workStart, $workEnd, $pauses = [], $specialEvents = []) {
        $workStart = new DateTime($workStart);
        $workEnd = new DateTime($workEnd);
        $start = new DateTime($start);
        $end = new DateTime($end);

        if ($start > $end) {
            throw new Exception("Start time must be before end time.");
        }

        $workStartTime = strtotime($workStart->format('H:i:s'));
        $workEndTime = strtotime($workEnd->format('H:i:s'));
        $startTime = $start->getTimestamp();
        $endTime = $end->getTimestamp();

        $totalSeconds = 0;

        $current = $startTime;

        // Convert pauses and special events to timestamps for better performance
        foreach ($pauses as &$pause) {
            $pause['start'] = strtotime($pause['start']->format('Y-m-d H:i:s'));
            $pause['end'] = strtotime($pause['end']->format('Y-m-d H:i:s'));
        }
        unset($pause);  // Unset the reference

        foreach ($specialEvents as &$event) {
            $event['start'] = strtotime($event['start']->format('Y-m-d H:i:s'));
            $event['end'] = strtotime($event['end']->format('Y-m-d H:i:s'));
        }
        unset($event);

        while ($current < $endTime) {
            $currentDayOfWeek = (int)date('N', $current);

            // Skip weekends
            if ($currentDayOfWeek >= 6) {
                $current = strtotime('next Monday', $current);
                continue;
            }

            $currentTimeOfDay = strtotime(date('H:i:s', $current));

            // If before work starts, jump to the work start time
            if ($currentTimeOfDay < $workStartTime) {
                $current = strtotime(date('Y-m-d', $current) . ' ' . date('H:i:s', $workStartTime));
                continue;
            }

            // If after work ends, jump to the next day
            if ($currentTimeOfDay >= $workEndTime) {
                $current = strtotime('next weekday ' . $workStart->format('H:i:s'), $current);
                continue;
            }

            // Check if current time is within a pause or special event
            $nextChange = $endTime;
            foreach (array_merge($pauses, $specialEvents) as $event) {
                if ($current >= $event['start'] && $current < $event['end']) {
                    $current = $event['end'];  // Skip to the end of the event
                    continue 2; // Recheck the new time
                }
                if ($current < $event['start'] && $event['start'] < $nextChange) {
                    $nextChange = $event['start'];
                }
            }

            // Calculate the time until the next work end or pause
            $nextWorkEnd = strtotime(date('Y-m-d', $current) . ' ' . date('H:i:s', $workEndTime));
            if ($nextWorkEnd < $nextChange) {
                $nextChange = $nextWorkEnd;
            }

            // Add the working time between current and the next change point
            $totalSeconds += min($nextChange, $endTime) - $current;
            $current = $nextChange;
        }

        return $totalSeconds / 3600; // Convert seconds to hours
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

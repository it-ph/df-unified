<?php

namespace App\Http\Helpers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;

class TimeElapsedHelperV1
{
    public function getWorkingHours()
    {
        // Client Work Shift
        $workStart = '09:00:00';
        $workEnd = '17:00:00';

        // Job Start & End Time
        $start = '2024-08-19 13:00:00';
        $end = '2024-08-11 16:28:22';

        $end = null;
        $end = $end == null ? Carbon::now()->format('Y-m-d H:i:s'): $end;

        // Job Pauses
        $pauses = [
            ['start' => new DateTime('2024-08-14 12:00:00'), 'end' => new DateTime('2024-08-14 13:00:00')],
            ['start' => new DateTime('2024-08-15 12:00:00'), 'end' => new DateTime('2024-08-15 13:00:00')]
        ];

        // Special Events within Job
        $specialEvents = [
            // ['start' => new DateTime('2024-08-15 09:00:00'), 'end' => new DateTime('2024-08-15 17:00:00')],
            // ['start' => new DateTime('2024-08-16 09:00:00'), 'end' => new DateTime('2024-08-16 17:00:00')]
        ];

        dd($pauses);

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

        $totalSeconds = 0;
        $current = clone $start;

        while ($current < $end) {
            if (!$this->isWorkingHour($current, $workStart, $workEnd, $pauses, $specialEvents)) {
                $current->modify('+1 second');
                continue;
            }

            $nextChange = $this->getNextChangeTime($current, $workStart, $workEnd, $pauses, $specialEvents);

            if ($nextChange > $end) {
                $nextChange = $end;
            }

            $interval = $current->diff($nextChange);
            $totalSeconds += $interval->s + $interval->i * 60 + $interval->h * 3600;
            $current = $nextChange;
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        // return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        return $totalSeconds / 3600;
    }

    public function isWorkingHour($dateTime, $workStart, $workEnd, $pauses, $specialEvents) {
        $timeOfDay = $dateTime->format('H:i:s');
        $dayOfWeek = $dateTime->format('N');

        if ($dayOfWeek >= 6) { // Saturday and Sunday
            return false;
        }

        if ($timeOfDay < $workStart->format('H:i:s') || $timeOfDay >= $workEnd->format('H:i:s')) {
            return false;
        }

        foreach ($pauses as $pause) {
            if ($dateTime >= $pause['start'] && $dateTime < $pause['end']) {
                return false;
            }
        }

        foreach ($specialEvents as $event) {
            if ($dateTime >= $event['start'] && $dateTime < $event['end']) {
                return false;
            }
        }

        return true;
    }

    public function getNextChangeTime($dateTime, $workStart, $workEnd, $pauses, $specialEvents) {
        $nextChange = clone $dateTime;
        $timeOfDay = $dateTime->format('H:i:s');

        if ($timeOfDay >= $workEnd->format('H:i:s')) {
            $nextChange->modify('next weekday');
            $nextChange->setTime($workStart->format('H'), $workStart->format('i'), $workStart->format('s'));
            return $nextChange;
        }

        foreach ($pauses as $pause) {
            if ($dateTime < $pause['start'] && $dateTime >= $pause['start']) {
                return $pause['end'];
            }
        }

        foreach ($specialEvents as $event) {
            if ($dateTime < $event['start'] && $dateTime >= $event['start']) {
                return $event['end'];
            }
        }

        $nextChange->modify('+1 second');
        return $nextChange;
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

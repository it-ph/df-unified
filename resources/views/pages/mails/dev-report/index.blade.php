<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $filename }}</title>
    <style>
        body {
            font-family: 'Calibri';
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            text-align: center;
        }
        th, td {
            padding: 3px;
        }
        th {
            background-color: #00599D;
            color: #fff;
        }
    </style>
    </style>
</head>
<body>
    <ul>
        <li>
            Completed {{ $completed }} out of {{ $total }} received,
            @if($sla_met == $completed)
                all
            @else
                {{ $sla_met }}
            @endif
            within SLA.
        </li>
        <li>
            @if($pending_external_qc == 0)
                External QC all completed.
            @else
                {{ $pending_external_qc }} job(s) pending External QC.
            @endif
        </li>
    </ul>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                @if($isAdmin)<th>Client Name</th>@endif
                <th>Type of Request</th>
                <th>Num Pages</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Agreed SLA</th>
                <th>Time Taken</th>
                <th>SLA Met</th>
                <th>Internal QC</th>
                <th>External QC</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($content as $row)
                <x-dev-report-table-row :row="$row" :isAdmin="$isAdmin" />
            @endforeach
        </tbody>
    </table>
</body>
</html>

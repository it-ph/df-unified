<tr>
    <td style="text-align: left">{{ $row['job_name'] }}</td>
    @if($isAdmin)<td>{{ $row['client'] }}</td>@endif
    <td>{{ $row['request_type'] }}</td>
    <td>{{ $row['request_volume'] }}</td>
    <td>{{ $row['created_at'] }}</td>
    <td>{{ $row['start_at'] }}</td>
    <td>{{ $row['end_at'] }}</td>
    <td>{{ $row['agreed_sla'] }}</td>
    <td>{{ $row['time_taken'] }}</td>
    <td style="background-color: {{ $slaColor }};">
        {{ $row['sla_met'] }}
    </td>
    <td style="background-color: {{ $internalQualityColor }};">
        {{ $row['internal_quality'] }}
    </td>
    <td style="background-color: {{ $externalQualityColor }};">
        {{ $row['external_quality'] }}
    </td>
    <td style="background-color: {{ $statusColor }};">
        {{ $row['status'] }}
    </td>
</tr>

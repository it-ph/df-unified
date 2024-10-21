<?php
    $status = $job['status'];
    switch ($status) {
        case "Not Started":
            $text = 'secondary';
            break;
        case "In Progress":
            $text = 'primary';
            break;
        case "On Hold":
            $text = 'warning';
            break;
        case "Info Needed":
            $text = 'warning';
            break;
        case "Sent For QC":
            $text = 'dark';
            break;
        case "Quality Check":
            $text = 'info';
            break;
            break;
        case "Bounce Back":
            $text = 'danger';
            break;
        case "Closed":
            $text = 'success';
            break;
    }
?>
<span class="text text-{{ $text }}"><strong>{{ $status }}</strong></span>

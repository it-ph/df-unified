@if($job->status == "Not Started")
    <span style="color: #74788D; font-weight: bold; font-size: 14px">
        NOT STARTED
    </span>
@elseif($job->status == "In Progress")
    <span style="color: #00599D; font-weight: bold; font-size: 14px">
        IN PROGRESS
    </span>
@elseif($job['status'] == "Sent For QC")
        <span style="color: #000; font-weight: bold; font-size: 14px">
            SENT FOR QC
        </span>
@elseif($job->status == "On Hold")
    <span style="color: #FFC600; font-weight: bold; font-size: 14px">
        ON HOLD
    </span>
@elseif($job->status == "Info Needed")
    <span style="color: #FFC600; font-weight: bold; font-size: 14px">
        INFO NEEDED
    </span>
@elseif($job->status == "Quality Check")
    <span style="color: #50A5F1; font-weight: bold; font-size: 14px">
        QUALITY CHECK
    </span>
@elseif($job->status == "Bounce Back")
    <span style="color: #c62228; font-weight: bold; font-size: 14px">
        BOUNCE BACK
    </span>
@elseif($job->status == "Closed")
    <span style="color: #28B779; font-weight: bold; font-size: 14px">
        CLOSED
    </span>
@endif

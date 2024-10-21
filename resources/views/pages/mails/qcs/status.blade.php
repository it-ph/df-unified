@if($audit_log->qc_status == "Pending")
    <span style="color: #FFC600; font-weight: bold; font-size: 14px">
        PENDING
    </span>
@elseif($audit_log->qc_status == "Pass")
    <span style="color: #28B779; font-weight: bold; font-size: 14px">
        PASS
    </span>
@elseif($audit_log->qc_status == "Fail")
    <span style="color: #c62228; font-weight: bold; font-size: 14px">
        FAIL
    </span>
@endif

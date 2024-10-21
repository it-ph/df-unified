$(document).ready(function() {
    DASHBOARD.load();
});

const DASHBOARD = (() => {
    let this_dashboard = {}

    // Store chart instances
    let charts = {
        jobsByRequestType: null,
        slaSummary: null,
        qcRounds: null,
        closedJobsInternalQualitySummary: null,
        internalQualitySummary: null,
        externalQualitySummary: null
    };

    // Fetch the CSRF token from meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    this_dashboard.load = () => {
        console.clear();

        $('.counts').hide();
        $('.loader').show();
        $('.charts').addClass('card-loading');
        axios.post(`${APP_URL}/dashboard/index`, {
                client_id: $('#client_id').val(),
                platform: $('#platform').val(),
                request_type_id: $('#request_type_id').val(),
                developer_id: $('#developer_id').val(),
                date_range: $('#date_range').val()
            }, {
                headers: {
                    'X-CSRF-Token': csrfToken // Add the CSRF token here
                }
            })
            .then(response => {
                console.log(response.data.data);
                if (response.data.status_code === 200) {

                    const data = response.data.data;

                    $('.loader').hide();
                    $('.counts').show();
                    $('#total').text(data.total_jobs);
                    $('#closed').text(data.closed_jobs);
                    $('#not_started').text(data.not_started_jobs);
                    $('#in_progress').text(data.in_progress_jobs);
                    $('#quality_check').text(data.qc_jobs);
                    $('#external_quality').text(data.external_quality_summary);
                    $('#jobs_sla_met').text(data.jobs_sla_met);
                    $('#jobs_sla_missed').text(data.jobs_sla_missed);
                    $('#jobs_qced').text(data.jobs_qced);
                    $('#jobs_qc_pass').text(data.internal_quality_pass);
                    $('#jobs_qc_fail').text(data.internal_quality_fail);
                    $('#internal_quality').text(data.internal_quality_summary);

                    // Clear existing charts
                    clearCharts();

                    // Render charts
                    renderJobsByRequestTypeChart({
                        labels: data.jobs_by_request_type.map(item => item.request_type),
                        values: data.jobs_by_request_type.map(item => item.total)
                    });

                    renderSlaSummaryChart({
                        labels: (data.jobs_sla_met == 0 && data.jobs_sla_missed == 0) ? '' : ['SLA Met', 'SLA Missed'],
                        values: (data.jobs_sla_met == 0 && data.jobs_sla_missed == 0) ? '' : [data.jobs_sla_met, data.jobs_sla_missed]
                    });

                    renderQcRoundsChart({
                        labels: data.jobs_by_qc_rounds.map(item => 'Round ' + item.qc_rounds),
                        values: data.jobs_by_qc_rounds.map(item => item.total)
                    });

                    renderClosedJobsInternalQualitySummaryChart({
                        labels: data.jobs_by_request_type.map(item => item.request_type),
                        values: data.jobs_by_request_type.map(item => item.total)
                    });

                    renderInternalQualitySummaryChart({
                        labels: data.internal_qc_summary_by_request_type.map(item => item.request_type),
                        values: data.internal_qc_summary_by_request_type.map(item => parseFloat(item.summary.toFixed(2)))
                    });

                    renderExternalQualitySummaryChart({
                        labels: data.external_qc_summary_by_request_type.map(item => item.request_type),
                        values: data.external_qc_summary_by_request_type.map(item => parseFloat(item.summary.toFixed(2)))
                    });

                    // load devs table data
                    if (data.devs.length > 1) {
                        loadDevTableData(data.devs);
                    } else {
                        $('#tbl_devs').DataTable().clear().draw();
                        $('#tbl_devs').DataTable().destroy();
                    }

                    // load auditors table data
                    if (data.auditors.length > 1) {
                        loadAuditorsTableData(data.auditors);
                    } else {
                        $('#tbl_auditors').DataTable().clear().draw();
                        $('#tbl_auditors').DataTable().destroy();
                    }

                    $('.card').removeClass('card-loading');
                    toastr.success(response.data.message);
                } else {
                    toastr.error(null);
                }
            })
            .catch(error => {
                console.error(error);
                toastr.error(null);
            });
    }

    function clearCharts() {
        for (const key in charts) {
            if (charts[key]) {
                if (charts[key] instanceof Chart) {
                    // console.log(`Destroying chart: ${key}`);
                    try {
                        charts[key].destroy();
                        charts[key] = null;
                    } catch (error) {
                        // console.error(`Error destroying chart ${key}:`, error);
                    }
                } else {
                    // console.warn(`No chart instance found for key: ${key}`);
                }
            } else {
                // console.warn(`No chart instance assigned to key: ${key}`);
            }
        }
    }

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Updated event listener with debounce
    $('.filters').on('change', debounce(function(e) {
        e.preventDefault();
        DASHBOARD.load();
    }, 300)); // 300ms debounce interval

    // For the date range picker
    $('#datefilter').on('apply.daterangepicker', debounce(function(ev, picker) {
        DASHBOARD.load();
    }, 300));

    function generateColors(numColors) {
        const colors = ['#00599D', '#E3342F', '#F1B44C', '#28B779', '#50A5F1', '#FAC930', '#E7455D'];
        for (let i = 0; i < numColors; i++) {
            // Generate a random color in hexadecimal format
            const color = `#${Math.floor(Math.random() * 16777215).toString(16)}`;
            colors.push(color);
        }
        return colors;
    }

    function renderJobsByRequestTypeChart(data) {
        const ctx = document.getElementById('jobsByRequestType').getContext('2d');
        const numColors = data.labels.length;
        const colors = generateColors(numColors);

        // Calculate the sum of all data values
        const sum = data.values.reduce((a, b) => a + b, 0);

        // Calculate percentages and round them
        const percentages = data.values.map(value => Math.round((value / sum) * 100));

        // Adjust the last percentage to ensure the total is 100%
        const totalPercentage = percentages.reduce((a, b) => a + b, 0);
        if (totalPercentage !== 100) {
            const difference = 100 - totalPercentage;
            percentages[percentages.length - 1] += difference;
        }

        charts.jobsByRequestType = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jobs Closed by Request Type',
                    data: data.values,
                    backgroundColor: colors,
                }]
            },
            options: {
                layout: {
                    padding: {
                        top: 15,
                        bottom: 15
                    }
                },
                plugins: {
                    datalabels: {
                        color: '#000',
                        formatter: (value, ctx) => {
                            // let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            // let percentage = Math.round(value * 100 / sum) + "%";
                            // return percentage;

                            let percentage = (value / sum) * 100;
                            return percentage.toFixed(2) + "%";
                        },
                        anchor: 'end',
                        align: 'end',
                        offset: -2,
                        font: {
                            size: '11',
                        },

                    }
                },
                legend: {
                    position: 'right',
                    align: 'start', // Align the legend to the right
                    labels: {
                        fontSize: 10,
                        boxWidth: 10,
                        padding: 10
                    }
                }
            }
        });
        // console.log('Jobs by Request Type chart created:', charts.jobsByRequestType);
    }

    function renderSlaSummaryChart(data) {
        const ctx = document.getElementById('slaSummary').getContext('2d');
        charts.slaSummary = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'SLA Summary',
                    data: data.values,
                    backgroundColor: ['#00599D', '#E3342F'],
                }]
            },
            options: {
                layout: {
                    padding: {
                        top: 15,
                        bottom: 15
                    }
                },
                plugins: {
                    datalabels: {
                        color: '#000',
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            // let percentage = Math.round(value * 100 / sum) + "%";
                            // return percentage;
                            let percentage = (value * 100) / sum;
                            return percentage.toFixed(2) + "%";
                        },
                        anchor: 'end',
                        align: 'end',
                        offset: -2,
                        font: {
                            size: '11',
                        }
                    }
                },
                legend: {
                    position: 'right',
                    align: 'start', // Align the legend to the right
                    labels: {
                        fontSize: 10,
                        boxWidth: 10,
                        padding: 10
                    }
                }
            }
        });
        // console.log('SLA Summary chart created:', charts.slaSummary);
    }

    function renderQcRoundsChart(data) {
        const ctx = document.getElementById('qcRounds').getContext('2d');
        const numColors = data.labels.length;
        const colors = generateColors(numColors);

        // Calculate the sum of all data values
        const sum = data.values.reduce((a, b) => a + b, 0);

        charts.qcRounds = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'QC Rounds',
                    data: data.values,
                    backgroundColor: colors,
                }]
            },
            options: {
                layout: {
                    padding: {
                        top: 15,
                        bottom: 15
                    }
                },
                plugins: {
                    datalabels: {
                        color: '#000',
                        formatter: (value, ctx) => {
                            // let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            // let percentage = Math.round(value * 100 / sum) + "%";
                            // return percentage;

                            let percentage = (value / sum) * 100;
                            return percentage.toFixed(2) + "%";
                        },
                        anchor: 'end',
                        align: 'end',
                        offset: -2,
                        font: {
                            size: '11',
                        }
                    }
                },
                legend: {
                    position: 'right',
                    align: 'start', // Align the legend to the right
                    labels: {
                        fontSize: 10,
                        boxWidth: 10,
                        padding: 10
                    }
                }
            },
        });
        // console.log('QC Rounds chart created:', charts.qcRounds);
    }

    function renderClosedJobsInternalQualitySummaryChart(data) {
        const ctx = document.getElementById('closedJobsInternalQualitySummary').getContext('2d');
        charts.closedJobsInternalQualitySummary = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Closed Jobs Internal QC Summary',
                    data: data.values,
                    backgroundColor: '#00599D', // Update the color as needed
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        color: '#FFF',
                        formatter: (value) => {
                            return value; // Display the value as needed, like "100.00"
                        },
                        anchor: 'center',
                        align: 'center',
                        offset: -10,
                        font: {
                            size: 10,
                        }
                    },
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }

    function renderInternalQualitySummaryChart(data) {
        const ctx = document.getElementById('internalQualitySummary').getContext('2d');
        charts.internalQualitySummary = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Internal Quality Summary %',
                    data: data.values,
                    backgroundColor: '#00599D',
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        color: '#FFF',
                        formatter: (value) => {
                            return value.toFixed(2) + '%';
                        },
                        anchor: 'center',
                        align: 'center',
                        offset: -10,
                        font: {
                            size: '10',
                        }
                    }
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        // console.log('Internal Quality Summary chart created:', charts.internalQualitySummary);
    }

    function renderExternalQualitySummaryChart(data) {
        const ctx = document.getElementById('externalQualitySummary').getContext('2d');
        charts.externalQualitySummary = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'External Quality Summary %',
                    data: data.values,
                    backgroundColor: '#00599D',
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        color: '#FFF',
                        formatter: (value) => {
                            return value.toFixed(2) + '%';
                        },
                        anchor: 'center',
                        align: 'center',
                        offset: -10,
                        font: {
                            size: '10',
                        }
                    }
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        // console.log('External Quality Summary chart created:', charts.externalQualitySummary);
    }

    // Handle window resize
    $(window).on('resize', function() {
        for (const key in charts) {
            if (charts[key] && charts[key] instanceof Chart) {
                charts[key].resize();
            }
        }
    });

    function loadDevTableData(data) {
        $('#tbl_devs').DataTable().destroy();
        let table = '';

        data.forEach((val, index) => {
            // Skip the last row if it is meant for the footer
            if (index < data.length - 1) {
                table +=
                    `<tr>
                    <td>${val.developer_name}</td>
                    <td>${val.num_closed_jobs}</td>
                    <td>${val.num_sla_missed}</td>
                    <td>${val.job_qc_pass}</td>
                    <td>${val.job_qc_fail}</td>
                    <td>${val.quality_score}</td>
                    <td>${val.avg_dev_time}</td>
                </tr>`;
            }
        });

        // Assuming the last object in the array is the footer totals
        const footerData = data[data.length - 1];
        table +=
            `<tr>
                <td class="fw-bold">Total</td>
                <td class="fw-bold">${footerData.num_closed_jobs}</td>
                <td class="fw-bold">${footerData.num_sla_missed}</td>
                <td class="fw-bold">${footerData.job_qc_pass}</td>
                <td class="fw-bold">${footerData.job_qc_fail}</td>
                <td class="fw-bold">${footerData.quality_score}</td>
                <td class="fw-bold">${footerData.avg_dev_time}</td>
            </tr>`;

        $('#tbl_devs tbody').html(table)

        $('#tbl_devs').DataTable({
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "order": [0, "asc"],
            "columnDefs": [{ 'targets': [0] }],
            "scrollX": true,
        });

        $('.div-spinner').hide();
    }

    function loadAuditorsTableData(data) {
        $('#tbl_auditors').DataTable().destroy();
        let table = '';

        data.forEach((val, index) => {
            // Skip the last row if it is meant for the footer
            if (index < data.length - 1) {
                table +=
                    `<tr>
                    <td>${val.auditor_name}</td>
                    <td>${val.num_qc_request}</td>
                    <td>${val.qc_pass}</td>
                    <td>${val.qc_fail}</td>
                    <td>${val.avg_qc_time}</td>
                </tr>`;
            }
        });

        // Assuming the last object in the array is the footer totals
        const footerData = data[data.length - 1];
        table +=
            `<tr>
                <td class="fw-bold">Total</td>
                <td class="fw-bold">${footerData.num_qc_request}</td>
                <td class="fw-bold">${footerData.qc_pass}</td>
                <td class="fw-bold">${footerData.qc_fail}</td>
                <td class="fw-bold">${footerData.avg_qc_time}</td>
            </tr>`;

        $('#tbl_auditors tbody').html(table)

        $('#tbl_auditors').DataTable({
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "order": [0, "asc"],
            "columnDefs": [{ 'targets': [0] }],
            "scrollX": true,
        });

        $('.div-spinner').hide();
    }

    return this_dashboard;
})()

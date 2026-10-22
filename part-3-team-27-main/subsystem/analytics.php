<?php require_once __DIR__ . '/../subsystems.php';?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>

    <link rel="stylesheet" href="/topnavbar.css">
    <link rel="stylesheet" href="/subsystem/css/analytics.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="DA-container">

        <div class="container-fluid mb-3 DA-header">
            <div class="row">

                <div class="col-md">
                    <div class="card DA-metric-card">
                        <div class="card-body">
                            <p class="card-title">Total Tasks</p>
                            <p class="card-text">14</p>
                        </div>
                    </div> 
                </div>

                <div class="col-md">
                    <div class="card DA-metric-card">
                        <div class="card-body">
                            <p class="card-title">Completed Tasks</p>
                            <p class="card-text">6</p>
                        </div>
                    </div> 
                </div>

                <div class="col-md">
                    <div class="card DA-metric-card">
                        <div class="card-body">
                            <p class="card-title">Overdue Tasks</p>
                            <p class="card-text">6</p>
                        </div>
                    </div> 
                </div> 

                <div class="col-md">
                    <div class="card DA-metric-card">
                        <div class="card-body">
                            <p class="card-title">On-time Delivery </p>
                            <p class="card-text">100%</p>
                        </div>
                    </div> 
                </div>

                <div class="col-md">
                    <div class="card DA-metric-card">
                        <div class="card-body">
                            <p class="card-title">Estimated Time to Finish</p>
                            <p class="card-text">2 Hours</p>
                        </div>
                    </div> 
                </div>

            </div>
        </div>

        <div class="container-fluid DA-charts-wrapper">
            <div class="row mb-3">

                <div class="col-md">
                    <div class="card DA-charts-card">
                        <div class="card-body">
                            <p class="card-title">Task Distribution</p>
                            <p class="card-text">Chart goes here</p>
                        </div>
                    </div>
                </div>

                <div class="col-md">
                    <div class="card DA-charts-card">
                        <div class="card-body">
                            <p class="card-title">Task Distribution</p>
                            <p class="card-text">Chart goes here</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card DA-charts-card">
                        <div class="card-body">
                            <p class="card-title">Task Distribution</p>
                            <p class="card-text">Chart goes here</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mb-3">
                <div class="col-md">
                    <div class="card DA-charts-card">
                        <div class="card-body">
                            <p class="card-title">Task Distribution</p>
                            <p class="card-text">Chart goes here</p>
                        </div>
                    </div>
                </div>

                <div class="col-md">
                    <div class="card DA-charts-card">
                        <div class="card-body">
                            <p class="card-title">Task Distribution</p>
                            <p class="card-text">Chart goes here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</body>
</html>
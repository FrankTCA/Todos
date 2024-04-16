<?php
function create_job($conn, $job, $params, $time): void {
    $sql = $conn->prepare("INSERT INTO crontab (job_descriptor, params, scheduled_for) VALUES (?, ?, ?);");
    $sql->bind_param("iss", $job, $params, $time);
    $sql->execute();
}

function run_cron_jobs($conn): void {
    $sql = $conn->prepare("SELECT * FROM crontab WHERE completed = 0 AND scheduled_for <= CURRENT_TIMESTAMP;");
    $sql->execute();

    if ($result = $sql->get_result()) {
        while ($row = $result->fetch_assoc()) {
            if ($row["job_descriptor"] == 1) {
                require_once "reminders.php";
                remind_tomorrow($conn, $row["params"], $row["id"]);
            }
            if ($row["job_descriptor"] == 2) {
                require_once "reminders.php";
                remind_today($conn, $row["params"], $row["id"]);
            }
        }
    }
}


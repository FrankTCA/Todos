<?php
require_once "../../sso/common.php";
require "../creds.php";
require "common.php";
validate_token("https://infotoast.org/todos/action/create_todo.php");

if (!(not_null($_POST["task_name"]) && not_null($_POST["description"]))) {
    die("noinfo");
}

$name = $_POST["task_name"];
$description = $_POST["description"];
$due = $_POST["due"];
$user_id = get_user_id();

if (!validate_date($due)) {
    http_response_code(400);
    die("dateformat");
}

if (isset($_POST["subtask"])) {
    $subtask_of = $_POST["subtask"];
} else {
    $subtask_of = 0;
}

if (isset($_POST["completion_method"])) {
    $completion_method = $_POST["completion_method"];
} else {
    $completion_method = 2;
}

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dberror");
}

require_once "cron.php";
run_cron_jobs($conn);

$sql = $conn->prepare("INSERT INTO tasks (user_id, name, description, subtask_of, completion_method, due_date, email_reminder) VALUES (?, ?, ?, ?, ?, ?, 1);");
$uid = $user_id;
$taskname = $name;
$taskdesc = $description;
$st = $subtask_of;
$comp_meth = $completion_method;
$due_date = $due;
$sql->bind_param('issiis', $uid, $taskname, $taskdesc, $st, $comp_meth, $due_date);
$sql->execute();
$conn->commit();

recurse_surtasks($subtask_of);

$sql2 = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND name LIKE ?;");
$uid2 = $user_id;
$task_name = $name;
$sql2->bind_param('is', $uid2, $task_name);
$sql2->execute();


if ($result = $sql2->get_result()) {
    while ($row = $result->fetch_assoc()) {
        try {
            $due_date_time = new DateTime($due);
            $today = new DateTime("now");
            $interval = $today->diff($due_date_time);
            if ($interval->days > 1) {
                create_job($conn, 2, $row["id"] . "," . get_user_email(), date('Y-m-d H:i:s', $due_date_time->getTimestamp()/*mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))*/));
            }
            if ($interval->days > 2) {
                create_job($conn, 1, $row["id"] . "," . get_user_email(), date('Y-m-d H:i:s', strtotime('-1 days', $due_date_time->getTimestamp())/*mktime(0, 0, 0, date("m")  , date("d")-2, date("Y"))*/));
            }
        } catch (Exception $e) {

        }
        http_response_code(200);
        $conn->close();
        echo "success,". $row["id"] . "," . $row["name"] . "," . $row["description"] . "," . $row["created"];
        die();
    }
}


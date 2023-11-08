<?php
require_once "../../sso/common.php";
require "../creds.php";
validate_token("https://infotoast.org/todos/action/complete.php");

if (!(not_null($_GET["id"]))) {
    http_response_code(400);
    die("noinfo");
}

$task_id = $_GET["id"];
$user_id = get_user_id();

if (not_null($_GET["amount"])) {
    $completion_amount_str = $_GET["amount"];
    $completion_amount = get_string_as_decimal($completion_amount_str);
    if ($completion_amount == null) {
        http_response_code(400);
        die("notdecimal");
    }
} else {
    $completion_amount = "1.0";
}

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dbconn");
}

$completion_method = -1;
$surtask = 0;

$sql = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?;");
$tid = $task_id;
$uid = $user_id;
$sql->bind_param('ii', $tid, $uid);
$sql->execute();

if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $completion_method = $row['completion_method'];
        $surtask = $row["subtask_of"];
    }
}

if ($completion_method == -1) {
    http_response_code(403);
    die("notask");
}

$sql1 = $conn->prepare("UPDATE tasks SET how_complete = ? WHERE id = ?;");
$comp_amt = $completion_amount;
$tid2 = $task_id;
$sql1->bind_param('di', $comp_amt, $tid2);
$sql1->execute();
$conn->commit();

if ($completion_method == 2 && $surtask != 0) {
    $subtasks_count = 0;
    $subtasks_complete = 0.0;
    $sql2 = $conn->prepare("SELECT * FROM tasks WHERE subtask_of = ?;");
    $surtask2 = $surtask;
    $sql2->bind_param('i', $surtask2);
    $sql2->execute();

    if ($result = $sql2->get_result()) {
        while ($row = $result->fetch_assoc()) {
            $subtasks_count += 1;
            $subtasks_complete += $row["how_complete"];
        }
    }

    if ($subtasks_count == 0) {
        http_response_code(500);
        die("nosubs");
    }

    $surtask_completion_amount = $subtasks_complete/$subtasks_count;

    $sql3 = $conn->prepare("UPDATE tasks SET how_complete = ? WHERE id = ?");
    $c_amt = $surtask_completion_amount;
    $surtask3 = $surtask;
    $sql3->bind_param('di', $c_amt, $surtask3);
    $sql3->execute();
    $conn->commit();
}

$conn->close();
echo "success";

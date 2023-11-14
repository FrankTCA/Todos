<?php
require_once "../../sso/common.php";
require "../creds.php";
require "common.php";
validate_token("https://infotoast.org/todos/action/delete_todo.php");

if (!not_null($_GET["id"])) {
    http_response_code(400);
    die("noinfo");
}

$task_id = $_GET["id"];
$user_id = get_user_id();

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dberror");
}

$sql = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND id = ?;");
$taskid = $task_id;
$uid = $user_id;
$sql->bind_param('ii', $uid, $taskid);
$sql->execute();
$task_belongs_to_user = false;
$parent_task = 0;

if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $task_belongs_to_user = true;
        $parent_task = $row["subtask_of"];
    }
}

if (!$task_belongs_to_user) {
    http_response_code(403);
    die("unownedtask");
}

$sql2 = $conn->prepare("DELETE FROM tasks WHERE id = ?;");
$tid = $task_id;
$sql2->bind_param('i', $tid);
$sql2->execute();
$conn->commit();

recurse_surtasks($parent_task);

$conn->close();

echo "success";

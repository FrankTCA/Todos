<?php
require "../creds.php";
require "common.php";
require "../../sso/common.php";

if (!(isset($_POST["taskid"]) && isset($_POST["name"]) && isset($_POST["description"]) && isset($_POST["due_date"]))) {
    http_response_code(400);
    die("nodata");
}

validate_token("https://infotoast.org/todos/action/edit_task.php");
$uid = get_user_id();
$task_id = $_POST["taskid"];
$name = $_POST["name"];
$description = $_POST["description"];
$due_date = $_POST["due_date"];

if (!validate_date($due_date)) {
    http_response_code(400);
    die("dateformat");
}

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dbconn");
}

$task_belongs_to_user = false;

$verify_sql = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND id = ?;");
$verify_sql->bind_param("ii", $uid, $task_id);
$verify_sql->execute();

if ($result = $verify_sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $task_belongs_to_user = true;
    }
}

if (!$task_belongs_to_user) {
    http_response_code(403);
    die("This is not your task!");
}

$update_sql = $conn->prepare("UPDATE tasks SET name = ?, description = ?, due_date = ? WHERE id = ? AND user_id = ?;");
$update_sql->bind_param("sssii", $name, $description, $due_date, $task_id, $uid);
$update_sql->execute();

$conn->commit();
$conn->close();

echo "success";

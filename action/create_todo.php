<?php
require_once "../../sso/common.php";
require "creds.php";
validate_token("https://infotoast.org/todos/action/create_todo.php");

if (!(not_null($_POST["task_name"]) && not_null($_POST["description"]))) {
    die("noinfo");
}

$name = $_POST["task_name"];
$description = $_POST["description"];
$user_id = get_user_id();

if (isset($_POST["subtask"])) {
    $subtask_of = $_POST["subtask"];
} else {
    $subtask_of = 0;
}

if (isset($_POST["completion_method"])) {
    $completion_method = $_POST["completion_method"];
} else {
    $completion_method = 0;
}

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dberror");
}

$sql = $conn->prepare("INSERT INTO tasks (user_id, name, description, subtask_of, completion_method) VALUES (?, ?, ?, ?, ?);");
$uid = $user_id;
$taskname = $name;
$taskdesc = $description;
$st = $subtask_of;
$comp_meth = $completion_method;
$sql->bind_param('issii', $uid, $taskname, $taskdesc, $st, $comp_meth);
$sql->execute();
$conn->commit();
$conn->close();

http_response_code(200);
echo "success";
<?php
require_once "../../sso/common.php";
require "../creds.php";
validate_token("https://infotoast.org/todos/action/progress.php");

if (!(not_null($_GET["id"]))) {
    http_response_code(400);
    die("noinfo");
}

$task_id = $_GET["id"];
$user_id = get_user_id();

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dbconn");
}

require_once "cron.php";
run_cron_jobs($conn);

$sql = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?;");
$id = $task_id;
$uid = $user_id;
$sql->bind_param('ii', $id, $uid);
$sql->execute();

if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        echo "success," . $row["how_complete"];
    }
}

$conn->close();

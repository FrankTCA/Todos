<?php
require_once "../../sso/common.php";
require "../creds.php";
require "common.php";
validate_token("https://infotoast.org/todos/action/toggle_reminder.php");

if (!(not_null($_GET["id"])) && !not_null($_GET["mode"])) {
    http_response_code(400);
    die("noinfo");
}

$id = $_GET["id"];
$mode = $_GET["mode"];

if (!($mode == "on" || $mode == "off")) {
    http_response_code(400);
    die("onoroff");
}

if ($mode == "on") {
    $setMode = 1;
} else {
    $setMode = 0;
}

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());

if ($conn->connect_error) {
    die("dberror");
}

$sql = $conn->prepare("UPDATE tasks SET email_reminder = ? WHERE id = ?;");
$sql->bind_param("ii", $setMode, $id);
$sql->execute();

$conn->commit();
$conn->close();

echo "success";

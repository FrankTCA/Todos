<?php
require_once "../../sso/common.php";
require "../creds.php";
validate_token("https://infotoast.org/todos/action/create_todo.php");

$user_id = get_user_id();

if (isset($_GET["subtask"])) {
    $surtask = $_GET["subtask"];
} else {
    $surtask = 0;
}

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    die("dbconn");
}

require_once "cron.php";
run_cron_jobs($conn);

$sql = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND subtask_of = ? AND how_complete < 1;");
$uid = $user_id;
$subtask_of = $surtask;
$sql->bind_param('ii', $uid, $subtask_of);
$sql->execute();

if ($result = $sql->get_result()) {
    header("Content-Type: application/json");
    echo '{"tasks":[';
    $counter = 0;
    while ($row = $result->fetch_assoc()) {
        echo "{\"id\": " . $row['id'] . ", \"name\": \"" . $row["name"] . "\", \"description\":\"" . $row["description"] . "\", \"completion_method\":" . $row["completion_method"] . ", \"how_complete\":" . $row["how_complete"] . ", \"due_date\":\"" . $row["due_date"] . "\", \"created\":\"" . $row["created"] . "\"}";
        $counter++;
        if ($counter < $result->num_rows) {
            echo ",";
        }
    }
    echo "]}";
}

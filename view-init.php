<?php
require "creds.php";
require "../sso/common.php";
validate_token("https://infotoast.org/todos/view-init.php");

if (get_user_level() < 2) {
    http_response_code(302);
    header("Location: https://infotoast.org/todos/view.php");
    die();
}

$username = get_username();
$user_id = get_user_id();

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("Could not connect to database!");
}

$sql = $conn->prepare("SELECT * FROM tokens WHERE user_id = ?;");
$uid = $user_id;
$sql->bind_param('i', $uid);
$sql->execute();

if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $token = $row['token'];
        ?>
<!DOCTYPE html>
<html>
<head>
    <title>Loading...</title>
    <script type="text/javascript">
        var tz_offset = new Date().getTimezoneOffset();
        tz_offset = tz_offset == 0 ? 0 : -tz_offset;
        console.log(tz_offset);
        window.location.replace("https://infotoast.org/todos/view.php?token=<?php echo $token ?>&tz=" + tz_offset);
    </script>
</head>
<body>
<noscript>JavaScript must be enabled for this function to work!</noscript>
</body>
</html><?php
        die();
    }
}

$token = random_bytes(16);

$sql2 = $conn->prepare("INSERT INTO tokens (user_id, username, token) VALUES (?, ?, SHA2(?, 256));");
$uid2 = $user_id;
$uname = $username;
$sql2->bind_param('iss', $uid2, $uname, $token);
$sql2->execute();

$conn->commit();

$conn->close();

header("Location: " . $_SERVER["REQUEST_URI"]);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Loading...</title>
    <script type="text/javascript">
        var tz_offset = new Date().getTimezoneOffset();
        tz_offset = tz_offset == 0 ? 0 : -tz_offset;
        console.log(tz_offset);
        window.location.replace("https://infotoast.org/todos/view.php?token<?php echo $token ?>&tz=" + tz_offset);
    </script>
</head>
<body>
<noscript>JavaScript must be enabled for this function to work!</noscript>
</body>
</html>

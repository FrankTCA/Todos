<?php
require 'creds.php';
require '../sso/common.php';
$blocked = false;
if (!(isset($_GET['token']))) {
    $blocked = true;
}

if (isset($_GET["tz"])) {
    $tz_name = timezone_name_from_abbr("", $_GET["tz"]*60, false);
} else {
    $tz_name = 'America/New_York';
}

$tz_obj = new DateTimeZone($tz_name);

if (!$blocked) {

    $conn = new mysqli(get_database_host(), get_database_username(), get_database_password(), get_database_db());

    if ($conn->connect_error) {
        die("Error connecting to database! Please contact an admin!");
    }

    require_once "action/cron.php";
    run_cron_jobs($conn);

    $stmt = $conn->prepare("SELECT * FROM `tokens` WHERE `token` LIKE ?;");
    $stmt->bind_param("s", $tok);

    $tok = $_GET['token'];
    $stmt->execute();
    $result = $stmt->get_result();

    $userid = null;
    $username = null;

    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['blocked'] == 1) {
            $blocked = true;
        } else {
            $userid = $row['user_id'];
            $username = $row['username'];
        }
    }

    $theDate = new DateTime("now", $tz_obj);
    $today = $theDate->format('m/d/o');
    $tom = new DateTime($today . ' + 1 day');
    $tomorrow = $tom->format('m/d/o');
    $dates = Array($today, $tomorrow);

    for ($day = 1; $day < 11; $day += 1) {
        $theDay = new DateTime($tomorrow . '+' . $day . ' days');
        $dayText = $theDay->format('m/d/o');
        $dates[] = $dayText;
    }

    $sql = $conn->prepare("INSERT INTO `view_log` (user_id, ip) VALUES (?, ?);");
    $uid = $userid;
    $ip = getUserIP();
    $sql->bind_param('is', $uid, $ip);
    $sql->execute();
    $conn->commit();
}

function get_random_image() {
    $rand = rand(0, 274);
    if ($rand == 98) {
        return get_random_image();
    }
    return $rand;
}

if ($blocked) {
    die("<!DOCTYPE html><html lang='en'><head><title>401 Unauthorized</title><link rel='stylesheet' type='text/css' href='resources/css/view.css'/><style>" .
            ".bg {\n" .
                "background-image: url('https://infotoast.org/images/" . rand(0,168) .
".jpg');\n" .
"background-size: 100% 100%;\n" .
"width: 100%;\n" .
"height: 100%;\n" .
        "text: white;\n" .
"}\n" .
"</style><meta http-equiv=\"refresh\" content=\"3;url=https://infotoast.org/sso/\" /></head><body><div class='bg'><h1>Unauthorized</h1><br><svg width='64px' height='64px' version=\"1.1\" id=\"Layer_1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" viewBox=\"0 0 512 512\" enable-background=\"new 0 0 512 512\" xml:space=\"preserve\"><g><polygon fill=\"#BC1B1B\" points=\"355.8,15 156.2,15 15,156.2 15,355.8 156.2,497 355.8,497 497,355.8 497,156.2 	\"/></g><line fill=\"#FFFFFF\" stroke=\"#FFFFFF\" stroke-width=\"10\" stroke-miterlimit=\"10\" x1=\"113\" y1=\"118\" x2=\"389\" y2=\"381\"/><line fill=\"#FFFFFF\" stroke=\"#FFFFFF\" stroke-width=\"10\" stroke-miterlimit=\"10\" x1=\"389\" y1=\"118\" x2=\"113\" y2=\"381\"/></svg><span>This page is only for premium subscribers.</span><p>Normally, this page would display a beautiful picture like on <a href='https://infotoast.org/homepage/'>Info Toast Homepage</a> with your task list behind it.</p><p>You will be redirected automatically in 3 seconds. If not, click <a href='https://infotoast.org/sso/'>here</a>.</p></div></body></html>");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <meta name="robots" content="noindex,nofollow">
    <?php
    if ($blocked) {
        ?>
        <title>Access denied.</title>
        <link rel="stylesheet" href="resources/global.css">
        <?php
    } else {
        ?>
        <title>Info Toast Assign</title>
        <link rel="stylesheet" href="https://use.typekit.net/avi3kdp.css">
        <link rel="stylesheet" href="resources/css/view.css">
        <style>
            .bg {
                background-image: url("https://infotoast.org/images/<?php
            echo get_random_image();
?>.jpg");
                background-size: 100% 100%;
                width: 100%;
                height: 100%;
            }
        </style>
        <?php
    }
    ?>
</head>
<body>
<?php
if ($blocked) {
    ?>
    <div class="container" id="notallowed">
        <h1 class="center">Access denied.</h1>
        <p class="center">You are not permitted to access the viewing page at this time.</p>
        <p class="center">Have you supplied a token in the query string?</p>
        <p class="center">Or </p>
    </div>
    <?php
} else {
    ?>
    <div class="bg">
        <h1 class="hello">Hello, <?php
            if (!(isset($username))) {
                echo "Big problem!";
            } else {
                echo $username;
            }
            ?></h1>
        <?php
        foreach ($dates as $day) {
            $query = $conn->prepare("SELECT * FROM `tasks` WHERE `user_id` = ? AND `due_date` LIKE ? AND how_complete < 1.0 AND subtask_of = 0;");
            $query->bind_param("is", $uid, $d);

            $uid = $userid;
            $d = $day;
            $query->execute();
            $result2 = $query->get_result();
            $doneyet = false;
            while ($row = mysqli_fetch_assoc($result2)) {
                if (!$doneyet) {
                    if ($day == $today) {
                        echo "<span class=\"day\">TODAY</span><br>";
                    } else if ($day == $tomorrow) {
                        echo "<span class=\"day\">TOMORROW</span><br>";
                    } else {
                        $date2 = new DateTime($day);
                        echo "<span class=\"day\">" . $date2->format('l, F j') . "</span><br>";
                    }
                    $doneyet = true;
                }
                echo "<div class=\"dateBox\"><span class=\"evtName\">" . $row['name'] . "</span><br>";
                if (!is_null($row['description'])) {
                    if ($row['description'] != "") {
                        echo "<span class=\"evtDesc\">" . $row['description'] . "</span>";
                    }
                }
                echo "</div><br>";
            }
        }
        ?>
    </div>
    <?php
}
?>
</body>
</html>

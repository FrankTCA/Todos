<?php
require "include_phpmailer.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function remind_tomorrow($conn, $params, $cron_id) {
    $params_split = explode(",", $params);
    $task_id = $params_split[0];
    $email = $params_split[1];

    $sql = $conn->prepare("SELECT * FROM tasks WHERE id = ?;");
    $sql->bind_param("i", $task_id);
    $sql->execute();

    if ($result = $sql->get_result()) {
        while ($row = $result->fetch_assoc()) {
            $task_name = $row["name"];
            $task_description = $row["description"];
            $email_enabled = $row["email_reminder"];
            $task_complete = $row["how_complete"];
            if ($email_enabled && $task_complete < 1) {
                $mail = new PHPMailer(true);
                try {
                    $mail->SMTPDebug = SMTP::DEBUG_OFF;
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->Username = "no-reply@infotoast.org";
                    $mail->Password = get_noreply_password();

                    $mail->setFrom('no-reply@infotoast.org');
                    $mail->addAddress($email);
                    $mail->addReplyTo('no-reply@infotoast.org');

                    $mail->IsHTML(true);
                    $mail->Subject = "$task_name is due tomorrow!";
                    $mail->Body = "<h1>Info Toast Tools Task Notification</h1><h3><strong>$task_name is due tomorrow!</strong></h3><p>Description: <i>$task_description</i></p><p>Check on your tasks and mark as finished: <a href='https://infotoast.org/todos/'>Info Toast Todos</a></p>";
                    $mail->AltBody = "$task_name is due tomorrow! Description: $task_description. Go to https://infotoast.org/todos to check your tasks and mark as complete!";

                    $mail->send();
                } catch (Exception $ex) {

                }
            }
        }
    }

    $sql2 = $conn->prepare("UPDATE crontab SET completed = 1 WHERE id = ?;");
    $sql2->bind_param("i", $cron_id);
    $sql2->execute();
}

function remind_today($conn, $params, $cron_id) {
    $params_split = explode(",", $params);
    $task_id = $params_split[0];
    $email = $params_split[1];

    $sql = $conn->prepare("SELECT * FROM tasks WHERE id = ?;");
    $sql->bind_param("i", $task_id);
    $sql->execute();

    if ($result = $sql->get_result()) {
        while ($row = $result->fetch_assoc()) {
            $task_name = $row["name"];
            $task_description = $row["description"];
            $email_enabled = $row["email_reminder"];
            $task_complete = $row["how_complete"];
            if ($email_enabled && $task_complete < 1) {
                require "include_phpmailer.php";
                $mail = new PHPMailer(true);
                try {
                    $mail->SMTPDebug = SMTP::DEBUG_OFF;
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->Username = "no-reply@infotoast.org";
                    $mail->Password = get_noreply_password();

                    $mail->setFrom('no-reply@infotoast.org');
                    $mail->addAddress($email);
                    $mail->addReplyTo('no-reply@infotoast.org');

                    $mail->IsHTML(true);
                    $mail->Subject = "$task_name is due today!";
                    $mail->Body = "<h1>Info Toast Tools Task Notification</h1><h3><strong>$task_name is due today!</strong></h3><p>Description: <i>$task_description</i></p><p>Check on your tasks and mark as finished: <a href='https://infotoast.org/todos/'>Info Toast Todos</a></p>";
                    $mail->AltBody = "$task_name is due today! Description: $task_description. Go to https://infotoast.org/todos to check your tasks and mark as complete!";

                    $mail->send();
                } catch (Exception $ex) {

                }
            }
        }
    }

    $sql2 = $conn->prepare("UPDATE crontab SET completed = 1 WHERE id = ?;");
    $sql2->bind_param("i", $cron_id);
    $sql2->execute();
}

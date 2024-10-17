<?php
function validate_date($date, $format = "m/d/Y"): bool {
    $d = DateTime::createFromFormat($format, $date);
    return $d && strtolower($d->format($format)) === strtolower($date);
}

function recurse_surtasks($surtask): void {
    global $completion_method;
    global $conn;
    if ($completion_method == 2 && $surtask != 0) {
        $subtasks_count = 0.0;
        $subtasks_complete = 0.0;
        $sql2 = $conn->prepare("SELECT * FROM tasks WHERE subtask_of = ?;");
        $surtask2 = $surtask;
        $sql2->bind_param('i', $surtask2);
        $sql2->execute();

        if ($result = $sql2->get_result()) {
            while ($row = $result->fetch_assoc()) {
                $subtasks_count += 1.0;
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

        $sql4 = $conn->prepare("SELECT * FROM tasks WHERE id = ?;");
        $surtask3 = $surtask;
        $sql4->bind_param('i', $surtask3);
        $sql4->execute();

        if ($result = $sql4->get_result()) {
            while ($row = $result->fetch_assoc()) {
                recurse_surtasks($row["subtask_of"]);
            }
        }
    }
}

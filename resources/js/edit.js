function edit_task(id) {
    let task = document.getElementById("task_" + id);
    let taskNameObj = task.querySelector(".taskName");
    let taskDescObj = task.querySelector(".taskDesc");
    let taskDueDateObj = task.querySelector(".dueDate");
    let originalTaskName = taskNameObj.innerText;
    let originalTaskDesc = taskDescObj.innerText;
    let originalDueDate = taskDueDateObj.innerText;

    $(taskNameObj).before('<input type="text" class="taskName taskNameEdit taskbtn" value="' + originalTaskName + '">');
    $(taskDescObj).before('<input type="text" class="taskDesc taskDescEdit taskbtn" value="' + originalTaskDesc + '">');
    $(taskDueDateObj).before('<input type="text" class="dueDate dueDateEdit taskbtn" value="' + originalDueDate + '">');

    $(".dueDateEdit").datepicker();

    $(taskNameObj).hide();
    $(taskDescObj).hide();
    $(taskDueDateObj).hide();

    let editBtn = task.querySelector(".editBtn");
    $(editBtn).before('<button class="editBtn taskbtn" onclick="commit_edit(' + id + ');">📝</button>');
    $(editBtn).hide();
}

function commit_edit(id) {
    let task = document.getElementById("task_" + id);
    let taskNameObj = task.querySelector(".taskNameEdit");
    let taskDescObj = task.querySelector(".taskDescEdit");
    let taskDueDateObj = task.querySelector(".dueDateEdit");

    const newName = taskNameObj.value;
    const newDesc = taskDescObj.value;
    const newDueDate = taskDueDateObj.value;

    $.post("action/edit_task.php", {
        taskid: id,
        name: newName,
        description: newDesc,
        due_date: newDueDate,
    }, function(data, status) {
        if (data.startsWith("dberror")) {
            $("#errorMsg").text("Database error! Please contact frank@infotoast.org.");
        } else if (data.startsWith("dateformat")) {
            $("#errorMsg").text("Date is in invalid format. Please use MM/DD/YYYY.");
        } else if (data.startsWith("success")) {
            location.reload();
        } else {
            $("#errorMsg").text(data);
        }
    });
}

var global_task = {
    id: 0,
    name: "global_task",
    desc: "",
    subtasks: [],
    progress: 0,
    due: "12/31/3000"
};

var open_subtasks = [];

var open_tasks = [];

function get_task_from_id(id) {
    for (var i = 0; i < open_tasks.length; i++) {
        if (open_tasks[i].id == id) {
            return open_tasks[i];
        }
    }
    return null;
}

function get_task_list_from_name(name) {
    var taskList = [];
    for (var i = 0; i < open_tasks.length; i++) {
        if (open_tasks[i].name = name) {
            taskList.push(open_tasks[i]);
        }
    }
    return taskList;
}

function get_parent_task_from_id(id) {
    var task = get_task_from_id(id);
    var parentId = task.parent;
    return get_task_from_id(parentId);
}

function get_child_tasks(id) {
    var taskList = [];
    for (var i = 0; i < open_tasks.length; i++) {
        if (open_tasks[i].parent == id) {
            taskList.push(open_tasks[i]);
        }
    }
    return taskList;
}

function remove_from_open_tasks(id) {
    for (var i = 0; i < open_tasks.length; i++) {
        if (open_tasks[i].id == id) {
            open_tasks.splice(i, 1);
        }
    }
}

function display_task(task, id) {
    var task_element = document.getElementById(id + "_subtasks");
    task_element.innerHTML = "";

    for (var i = 0; i < task.subtasks.length; i++) {
        task_element.appendChild(document.createElement("br"));
        var new_li = document.createElement("li");
        new_li.id = id + ":" + i;

        var new_div = document.createElement("div");
        new_div.setAttribute("class", "task");


        if (task.subtasks[i].subtasks.length > 0) {
            var progress = document.createElement("progress");
            progress.value = task.subtasks[i].progress;
            progress.max = 100;
            new_div.appendChild(progress);
        }
        else {
            var completion_checkbox = document.createElement("input");
            completion_checkbox.type = "checkbox";
            completion_checkbox.setAttribute("onclick", "toggle_completion('" + id + "', '" + i + "')");
            completion_checkbox.checked = task.subtasks[i].progress == 100 ? true : false;
            new_div.appendChild(completion_checkbox);
        }

        var name_header = document.createElement("h3");
        if (task.subtasks[i].progress == 100) {
            var strike_through = document.createElement("strike");
            strike_through.innerHTML = task.subtasks[i].name;
            name_header.appendChild(strike_through);
        }
        else {
            name_header.innerHTML = task.subtasks[i].name;
        }
        new_div.appendChild(name_header);
        // Todo: Grey out task when completed.

        var desc_par = document.createElement("p");
        desc_par.innerHTML = task.subtasks[i].desc;
        new_div.appendChild(desc_par);

        var remove_button = document.createElement("input");
        remove_button.type = "button";
        remove_button.value = "Remove";
        remove_button.setAttribute("onclick", "remove_task('" + id + "', '" + i + "')");
        new_div.appendChild(remove_button);

        task_element.appendChild(new_li);

        if (task.subtasks[i].subtasks.length > 0) {
            var new_list = document.createElement("ul");
            new_list.id = new_li.id + "_subtasks";
            new_list.class = "task_list";
            new_div.appendChild(new_list);

            new_li.appendChild(new_div);

            display_task(task.subtasks[i], new_li.id);
        }
        else {
            new_li.appendChild(new_div);
            task_element.appendChild(new_li);
        }
    }
}

function add_task() {
    var name = document.getElementById("add_name").value;
    var desc = document.getElementById("add_desc").value;
    var parent_id_base = document.getElementById("set_parent_task").value;
    var due = document.getElementById("date").value;
    if (Number.isNaN(parent_id_base)) {
        var parent_id = 0;
    } else {
        var parent_id = parent_id_base;
        get_subtasks(parent_id);
    }
    document.getElementById("add_name").value = "";
    document.getElementById("add_desc").value = "";
    document.getElementById("set_parent_task").value = "";
    document.getElementById("date").value = "";
    /*var id = document.getElementById("set_parent_task").value;
    var task = get_task_from_id(id);

    if (task.progress == 100 && task.subtasks.length == 0) {
        task.progress = 0;
    }

    task.subtasks.push({
        name: document.getElementById("add_name").value,
        desc: document.getElementById("add_desc").value,
        subtasks: [],
        progress: 0,
        due: document.getElementById("date").value
    });

    update_progress(global_task);

    display_task(global_task, "global_task");*/
    $("#addTaskButton").click();
    $.post("action/create_todo.php", {
        task_name: name,
        description: desc,
        due: due,
        subtask: parent_id
    }, function(data, status) {
        if (data.startsWith("dberror")) {
            $("#errorMsg").text("Database error! Please contact frank@infotoast.org.");
        } else if (data.startsWith("success")) {
            var data_split = data.split(",");
            print_task(data_split[1], name, desc, due, 0, parent_id);
        } else {
            $("#errorMsg").text(data);
        }
    });
}

function add_task_data(server_id, parent_id, name, desc, due, progress = 0) {
    var task = get_task_from_srv_id(parent_id);
    if (task.progress == null) {
        task.progress = 0;
    }
    if (task.progress == 100 && task.subtasks.length == 0) {
        task.progress = 0;
    }

    task.subtasks.push({
        id: server_id,
        name: name,
        desc: desc,
        subtasks: [],
        progress: progress,
        due: due
    });

    update_progress(global_task);

    display_task(global_task, "global_task");
}

function remove_task(parent_id, i) {
    var task = get_task_from_id(parent_id);
    task.subtasks.splice(i, 1);
    display_task(task, parent_id);
}

function update_progress(task) {
    if (task.subtasks.length > 0) {
        task.progress = 0;
        for (var i = 0; i < task.subtasks.length; i++) {
            update_progress(task.subtasks[i]);
            task.progress += task.subtasks[i].progress;
        }
        task.progress /= task.subtasks.length; // Note(Aaron): My editor doesn't like \/= so I'm adding this comment to escape it./
    }
}

function refresh_progress(id) {
    const progReq = new XMLHttpRequest();
    var progress = 0;
    progReq.addEventListener("load", function() {
        var data = this.responseText;
        if (data.startsWith("success")) {
            let splitData = data.split(",")
            progress = Number(splitData[1])*100;
            if (progress == 100) {
                $("#task_" + id).hide();
                $("#" + id + "_subtasks").hide();
            } else {
                $("#progressBar_" + id).progressbar({
                    value: progress
                });
            }
        }
    });
    progReq.open("GET", "action/progress.php?id=" + id, false);
    progReq.send();
}

function toggle_completion(parent_id, i) {
    var task = get_task_from_id(parent_id);
    task.subtasks[i].progress = 100 - task.subtasks[i].progress;
    update_progress(global_task);

    display_task(global_task, "global_task");
}

function print_task(id, name, description, due_date, progress, subtask_of_id, reminder) {
    if (get_task_from_id(id) != null) {
        return;
    }

    const toAppend = "<div class='task' id='task_" + String(id) + "'><li>"
        + "<div class='taskSeperator'><button onclick='complete_task(" + id + ")' class='taskbtn'>✅</button></div>"
        + "<div class='tasknameDesc taskSeperator'><span class='taskName'>" + name + "</span><br><br>" + "<span class='taskDesc'>" + description + "</span></div>"
        + "<div class='taskSeperator'><span class='dueDate'>" + due_date + "</span></div>"
        +  "<div class='controlButtons taskSeperator'><button onclick='delete_task(" + String(id) + ")' class='taskbtn'>🗑️</button>"
        + "<button onclick='subtasks_click(" + String(id) + ")' class='taskbtn'>"
        + '<svg version="1.1" id="Layer_1" width="10px" height="10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n' +
        '\t viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">\n' +
        '<polyline fill="#FFFFFF" stroke="#000000" stroke-width="30" stroke-miterlimit="10" points="13,13 64,48.6 114.2,13 "/>\n' +
        '<polyline fill="#FFFFFF" stroke="#000000" stroke-width="3" stroke-miterlimit="10" points="10.5,78.5 14.5,82.5 22.5,74.5 "/>\n' +
        '<line fill="#FFFFFF" stroke="#000000" stroke-width="3" stroke-miterlimit="10" x1="33.5" y1="76.5" x2="104.7" y2="76.5"/>\n' +
        '<line fill="#FFFFFF" stroke="#000000" stroke-width="3" stroke-miterlimit="10" x1="33.5" y1="83.8" x2="115.9" y2="83.8"/>\n' +
        '<polyline fill="#FFFFFF" stroke="#000000" stroke-width="3" stroke-miterlimit="10" points="11.4,96.9 15.4,100.9 23.4,92.9 "/>\n' +
        '<line fill="#FFFFFF" stroke="#000000" stroke-width="3" stroke-miterlimit="10" x1="34.4" y1="94.9" x2="105.6" y2="94.9"/>\n' +
        '<line fill="#FFFFFF" stroke="#000000" stroke-width="3" stroke-miterlimit="10" x1="34.4" y1="102.2" x2="116.8" y2="102.2"/>\n' +
        '<circle stroke="#000000" stroke-width="3" stroke-miterlimit="10" cx="16.5" cy="116.5" r="5"/>\n' +
        '<line fill="#FFFFFF" stroke="#000000" stroke-width="3" stroke-miterlimit="10" x1="34.3" y1="112.1" x2="105.5" y2="112.1"/>\n' +
        '<line fill="#FFFFFF" stroke="#000000" stroke-width="3" stroke-miterlimit="10" x1="34.3" y1="119.5" x2="116.7" y2="119.5"/>\n' +
        '</svg>'
        + "</button><button class='taskbtn' onclick='create_subtask(" + id + ")'><svg version=\"1.1\" length=\"10px\" width=\"10px\" id=\"Layer_1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\"\n" +
        "\t viewBox=\"0 0 128 128\" enable-background=\"new 0 0 128 128\" xml:space=\"preserve\">\n" +
        "<line fill=\"#FFFFFF\" stroke=\"#000000\" stroke-width=\"10\" stroke-miterlimit=\"10\" x1=\"64\" y1=\"5.9\" x2=\"63\" y2=\"73\"/>\n" +
        "<line fill=\"#FFFFFF\" stroke=\"#000000\" stroke-width=\"10\" stroke-miterlimit=\"10\" x1=\"33.5\" y1=\"39.5\" x2=\"96.4\" y2=\"39.5\"/>\n" +
        "<polyline fill=\"#FFFFFF\" stroke=\"#000000\" stroke-width=\"10\" stroke-miterlimit=\"10\" points=\"24,82 63,117.1 100.2,82 \"/>\n" +
        "</svg>\n"
        + "</button><label class='switch'><input type='checkbox' onclick='toggle_reminder(" + id + ")' id='reminder_switch_" + id + "'><span class='slider'></span></label>"
        + "</div><div id='progressBar_" + String(id) + "'></div></li></div>"
        + "<div class='task_list' style='margin-left: 2em' id='" + String(id) + "_subtasks'></div>"
    if (subtask_of_id == 0) {
        $("#global_task_subtasks").append(toAppend);
        $("#progressBar_" + String(id)).progressbar({
            value: progress
        });
        $("#" + id + "_subtasks").hide();
    } else {
        $("#" + subtask_of_id + "_subtasks").append(toAppend);
        $("#progressBar_" + String(id)).progressbar({
            value: progress
        });
        $("#" + id + "_subtasks").hide();
    }

    if (moment().startOf('day') > moment(due_date, "MM/DD/YYYY").startOf('day')) {
        $("#task_" + id).css("color", "red");
    }
    document.getElementById("reminder_switch_" + id).checked = reminder;
    open_tasks.push({
        id: id,
        name: name,
        description: description,
        due_date: due_date,
        progress: progress,
        parent: subtask_of_id,
        reminder: reminder
    });
    recurse_parent_progress(id);
}

function toggle_reminder(id) {
    let task = get_task_from_id(id);
    var option;
    if (task.reminder) {
        option = "off";
    } else {
        option = "on";
    }

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "https://infotoast.org/todos/action/toggle_reminder.php?id=" + id + "&mode=" + option);
    xhr.send();
    task.reminder = !task.reminder;
    document.getElementById("reminder_switch_" + id).checked = task.reminder;
}

function complete_task(id) {
    console.log("Marked as completed!");
    const compReq = new XMLHttpRequest();
    compReq.addEventListener("load", function() {
        var data = this.responseText;
        if (data.startsWith("success")) {
            $("#task_" + id).hide();
            $("#" + id + "_subtasks").hide();
        } else {
            $("#errorMsg").text(data);
        }
    });
    compReq.open("GET", "action/complete.php?id=" + id);
    compReq.send();
    recurse_parent_progress(id);
    remove_from_open_tasks(id);
}

function recurse_parent_progress(id) {
    if (id == 0) {
        return;
    }
    refresh_progress(id);
    var task = get_task_from_id(id);
    recurse_parent_progress(task.parent);
}

function get_todos(surtask_id) {
    console.log("Getting todo list!");
    const req = new XMLHttpRequest();
    req.addEventListener("load", function() {
        data = this.responseText;
        console.log("Running success function.");
        console.log(data);
        let jsonData = JSON.parse(data);
        for (var i = 0; i < jsonData.tasks.length; i++) {
            var element = jsonData.tasks[i];
            console.log(element);
            var dueDateRaw = element.due_date;
            var dueDateParts = dueDateRaw.split("/");
            var dueDate = new Date(dueDateParts[2], dueDateParts[0] - 1, dueDateParts[1]);
            if (element.how_complete < 1.0) {
                var reminder;
                if (element.reminder === "1") {
                    reminder = true;
                } else {
                    reminder = false;
                }
                print_task(element.id, element.name, element.description, element.due_date, Math.round(Number(element.how_complete)*100), surtask_id, reminder);
            }
        }
    });
    req.open("GET", "action/list_todos.php?subtask=" + surtask_id);
    req.send();
}

function clear() {
    $("#global_task_subtasks").innerHTML = "";
    open_tasks = [];
}

const destroyFast = container => {
    const el = document.getElementById(container);
    while (el.firstChild) el.removeChild(el.firstChild);
};

function subtasks_click(id) {
    childList = get_child_tasks(id);
    var isOpen = false;
    for (var i = 0; i < childList.length; i++) {
        isOpen = true;
        remove_from_open_tasks(childList[i].id);
    }
    if (isOpen) {
        destroyFast(id + "_subtasks");
        return;
    }
    get_subtasks(id);
}

function get_subtasks(id) {
    get_todos(id);
    open_subtasks.push(id);
    $("#" + id + "_subtasks").show();
}

function delete_task(id) {
    const deleteReq = new XMLHttpRequest();
    deleteReq.addEventListener("load", function() {
        let data = this.responseText;
        $("#task_" + id).hide();
        $("#" + id + "_subtasks").hide();
    });
    deleteReq.open("GET", "action/delete_todo.php?id=" + id, false);
    deleteReq.send();
    recurse_parent_progress(id);
    remove_from_open_tasks(id);
}

function create_subtask(id) {
    $("#set_parent_task").val(String(id));
    $("#addTaskButton").click();
}

$(document).ready(function() {
    /*$("#errorMsg").hide();
    $("#pastDueHeader").hide();
    $("#pastDue").hide();
    $("#currentHeader").hide();
    $("#current").hide();
    $("#doneHeader").hide();
    $("done").hide();
    const currentDate = new Date();*/
    var addTaskActive = false;
    $("#addTaskDropdown").hide();
    $("#addTaskButton").click(function() {
        if (!addTaskActive) {
            $("#addTaskDropdown").show().position({
                my: "right top",
                at: "right bottom",
                of: $("#addTaskButton"),
                collision: "none"
            });
            $("#addTaskButton").text("-");
            $("#date").datepicker();
            addTaskActive = true;
        } else {
            $("#set_parent_task").val("");
            $("#addTaskDropdown").hide();
            $("#addTaskButton").text("+");
            addTaskActive = false;
        }
    });

    $("#date").attr("placeholder", moment().format("MM/DD/YYYY"));

    console.log("Logging!");

    get_todos(0);
});

var global_task = {
    id: 0,
    name: "global_task",
    desc: "",
    subtasks: [],
    progress: 0,
    due: "12/31/3000"
};

function get_task_from_id(id) {
    var indices = id.split(":");
    var task = global_task;
    for (var i = 0; i < indices.length; i++) {
        task = task.subtasks[Number(indices[i])];
    }

    return task;
}

function get_task_from_name(name) {
    var task = global_task;
    return recurse_tasks_scan_name(task, name);
}

function recurse_tasks_scan_name(task, name) {
    for (var i = 0; i < task.subtasks.length; i++) {
        var task2 = task.subtasks[i];
        if (task2.name == name) {
            return task2;
        }
        var result = recurse_tasks_scan_name(task2, name);
        if (result != null) {
            return result;
        }
    }
    return null;
}

function get_task_from_srv_id(id) {
    var task = global_task;
    return recurse_tasks_scan_srv_id(task, id);
}

function recurse_tasks_scan_srv_id(task, id) {
    for (var i = 0; i < task.subtasks.length; i++) {
        var task2 = task.subtasks[i];
        if (task2.id == id) {
            return task2;
        }
        var result = recurse_tasks_scan_srv_id(task, id);
        if (result != null) {
            return result;
        }
    }
    return null;
}

function get_parent_task_from_id(id) {
    var indices = id.split(":");
    var task = global_task;
    for (var i = 1; i < indices.length - 1; i++) {
        task = task.subtasks[Number(indices[i])];
    }

    if (task == global_task) {
        return -1;
    }

    return task;
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

function toggle_completion(parent_id, i) {
    var task = get_task_from_id(parent_id);
    task.subtasks[i].progress = 100 - task.subtasks[i].progress;
    update_progress(global_task);

    display_task(global_task, "global_task");
}

function print_task(id, name, description, due_date, progress, subtask_of_id) {
    if (subtask_of_id == 0) {
        $("#global_task_subtasks").append("<div class='task' id='task_'" + String(id) + "><li>" +
            + "<button onclick='complete_task(" + String(id) + ")' class='taskbtn'>✅</button>"
            + "<div class='tasknameDesc'><span class='taskName'>" + name + "</span><br>" + "<span class='taskDesc'>" + description + "</span></div>"
            + "<span class='dueDate'>" + due_date + "</span>"
            + "<div id='progressBar_" + String(id) + "'></div>"
            + "<div class='controlButtons'><button onclick='delete_task(" + String(id) + ")' class='taskbtn'>🗑️</button>"
            + "<button onclick='get_subtasks(" + String(id) + ")' class='taskButton'>"
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
            + "</button></div></li></div>"
        );
        $("#progressBar_" + String(id)).progressbar({
            value: progress
        });
        $("#global_task_subtasks").append("<div class='task_list id='" + String("id") + "_subtasks");
    }
}

function complete_task(id) {

}

function get_subtasks(id) {

}

function delete_task(id) {

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
            $("#addTaskDropdown").hide();
            $("#addTaskButton").text("+");
            addTaskActive = false;
        }
    });

    console.log("Logging!");

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
                    print_task(element.id, element.name, element.description, element.due_date, Math.round(Number(element.how_complete)*100), surtask_id);
                }
            }
        });
        req.open("GET", "action/list_todos.php?subtask=" + surtask_id);
        req.send();
    }

    get_todos(0);
});

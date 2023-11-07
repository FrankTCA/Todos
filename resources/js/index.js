$(document).ready(function() {
    /*$("#errorMsg").hide();
    $("#pastDueHeader").hide();
    $("#pastDue").hide();
    $("#currentHeader").hide();
    $("#current").hide();
    $("#doneHeader").hide();
    $("done").hide();
    const currentDate = new Date();
    $.get("action/list_todos.php", function (data, status) {
        if (status !== 200) {
            $("#errorMsg").text("There was an error! Error code: " + data + ". Please email this to frank@infotoast.org.").show();
            return;
        }
        $.each(data, function(index, element) {
            var dueDateRaw = element.due_date;
            var dueDateParts = dueDateRaw.split("/");
            var dueDate = new Date(dueDateParts[2], dueDateParts[0] - 1, dueDateParts[1]);
            if (currentDate > dueDate && element.how_complete < 1.0) {
                $("#pastDueHeader").show();
                $("#pastDue").show();
                $("#pastDueTable").append("<tr><td>" + element.name + "</td><td>" + element.description + "</td><td>" + element.due_date + "</td><td><span class='completion'>" + element.how_complete + )
            }
        })
    });*/

    $("#addTaskDropdown").hide();
    $("#addTaskButton").click(function() {
        $("#addTaskDropdown").show().position({
            my: "right top",
            at: "right bottom",
            of: $("#addTaskButton"),
            collision: "none"
        });
    });
});

var global_task = {
    name: "global_task",
    desc: "",
    subtasks: [],
    progress: 0,
};

function get_task_from_id(id) {
    var indices = id.split(":");
    var task = global_task;
    for (var i = 1; i < indices.length; i++) {
        task = task.subtasks[Number(indices[i])];
    }

    return task;
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
    var id = document.getElementById("set_parent_task").value;
    var task = get_task_from_id(id);

    if (task.progress == 100 && task.subtasks.length == 0) {
        task.progress = 0;
    }

    task.subtasks.push({
        name: document.getElementById("add_name").value,
        desc: document.getElementById("add_desc").value,
        subtasks: [],
        progress: 0,
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

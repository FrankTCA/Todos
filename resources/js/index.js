$(document).ready(function() {
    $("#errorMsg").hide();
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
    });
});

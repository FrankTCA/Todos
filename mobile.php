<?php
require_once "../sso/common.php";
validate_token("https://infotoast.org/todos/mobile.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>To-Do List</title>
    <link rel="stylesheet" type="text/css" href="/global-resources/mallory.css">
    <link rel="stylesheet" type="text/css" href="resources/css/jquery-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="/sso/resources/login-box.css">
    <link rel="stylesheet" type="text/css" href="resources/css/global.css"/>
    <link rel="stylesheet" type="text/css" href="resources/css/aaron.css"/>
    <link rel="stylesheet" type="text/css" href="resources/css/index.css"/>
    <link rel="stylesheet" type="text/css" href="resources/css/mobile.css"/>
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="/sso/resources/node_modules/js-cookie/dist/js.cookie.min.js"></script>
    <script type="text/javascript" src="resources/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/sso/resources/login-box.js"></script>
    <script type="text/javascript" src="resources/js/moment.min.js"></script>
    <script type="text/javascript" src="resources/js/mobile.js"></script>
    <script type="text/javascript" src="resources/js/edit.js"></script>
</head>
<body>
<div class="top">
    <div class="topleft">
        <h2>To-Do Lists</h2>
    </div>
    <div class="topright">
            <div class="loginbutton">
            </div>
    </div>
</div>

<div class="theBody">
    <div class="addTaskArea">
        <button class="dropdownparent globalButton" id="addTaskButton">
            +
        </button>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <ul id="global_task_subtasks" class="task_list">
    </ul>
    <p class="errorMsg" id="errorMsg"></p>
    <div class="dropdown" id="addTaskDropdown">
        <input type="text" id="add_name" class="textBox" placeholder="Name"><br>
        <input type="text" id="add_desc" class="textBox" placeholder="Description"><br>
        <input type="text" id="date" name="date" class="textBox" placeholder="<?php echo date("m/d/Y"); ?>"><br>
        <input type="hidden" id="set_parent_task">
        <input type="button" id="add_task" value="Add" onclick="add_task()"><br>
    </div>
</div>
</body>
</html>

<?php
require_once "../sso/common.php";
validate_token("https://infotoast.org/todos/");
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
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="resources/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="resources/js/moment.min.js"></script>
    <script type="text/javascript" src="/sso/resources/login-box.js"></script>
    <script type="text/javascript" src="resources/js/index.js"></script>
    <script type="text/javascript" src="resources/js/edit.js"></script>
    <script type="text/javascript" src="resources/js/detect-mobile.js"></script>
</head>
<body>
    <div class="top">
        <div class="topleft">
            <h1>To-Do Lists</h1>
        </div>
        <div class="topright">
            <div class="loginbutton"></div>
        </div>
    </div>

    <div class="theBody">
        <div class="globalButtons">
                <div class="addTaskArea">
                    <button class="globalButton" onclick="window.location.replace('https://infotoast.org/todos/view-init.php');">
                        <svg width="15px" height="15px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                             viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
<polyline fill="#FFFFFF" stroke="#000000" stroke-miterlimit="10" points="5,104.4 45.5,41.5 62.3,72.9 "/>
                            <polyline fill="#FFFFFF" stroke="#000000" stroke-miterlimit="10" points="48.5,104.5 82.5,23 118.7,104.4 "/>
                            <polyline fill="#FFFFFF" stroke="#000000" stroke-miterlimit="10" points="76,38.6 78.5,45.5 82.3,38.6 85.6,45.5 89.4,38.6 "/>
</svg></button>
                    <button class="dropdownparent globalButton" id="addTaskButton">
                        +
                    </button>
                    <div class="dropdown" id="addTaskDropdown">
                        <input type="text" id="add_name" class="textBox" placeholder="Name"><br>
                        <input type="text" id="add_desc" class="textBox" placeholder="Description"><br>
                        <input type="text" id="date" name="date" class="textBox"><br>
                        <input type="hidden" id="set_parent_task">
                        <input type="button" id="add_task" value="Add" onclick="add_task()"><br>
                    </div>
                </div>
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
    </div>
</body>
</html>

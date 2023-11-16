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
    <link rel="stylesheet" type="text/css" href="resources/css/jquery-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="resources/css/global.css"/>
    <link rel="stylesheet" type="text/css" href="resources/css/aaron.css"/>
    <link rel="stylesheet" type="text/css" href="resources/css/index.css"/>
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="resources/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="resources/js/mobile.js"></script>
</head>
<body>
<div class="top">
    <div class="topleft">
        <h1>To-Do Lists</h1>
    </div>
    <div class="topright">
        <a href="https://infotoast.org/sso/" class="divLink" id="loginButton">
            <div class="loginbutton">
                <svg class="user" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve" width="40px" height="40px"><g><path fill="#FFFFFF" fill-opacity="0" d="M30.6,111.5c5.8-29.9,18.8-49.2,33.2-49.2c14.2,0,27.1,18.9,33,48.2L30.6,111.5z"/><path d="M63.8,62.8c13.9,0,26.5,18.5,32.4,47.2l-65,0.9C37,81.7,49.7,62.8,63.8,62.8 M63.8,61.8C48.6,61.8,35.6,82.5,30,112l67.4-0.9C91.7,82.1,78.8,61.8,63.8,61.8L63.8,61.8z"/></g><line fill="none" stroke="#FFFFFF" stroke-width="0" stroke-linecap="round" stroke-linejoin="round" x1="103.5" y1="68.5" x2="82.4" y2="68.8"/><line fill="none" stroke="#FFFFFF" stroke-width="0" stroke-linecap="round" stroke-linejoin="round" x1="48.5" y1="69.3" x2="31.5" y2="69.5"/><g><path fill="#FFFFFF" fill-opacity="0" d="M64.5,59.5c-7.2,0-13-5.8-13-13v-7c0-7.2,5.8-13,13-13c7.2,0,13,5.8,13,13v7C77.5,53.7,71.7,59.5,64.5,59.5z"/><path d="M64.5,27C71.4,27,77,32.6,77,39.5v7C77,53.4,71.4,59,64.5,59C57.6,59,52,53.4,52,46.5v-7C52,32.6,57.6,27,64.5,27 M64.5,26L64.5,26C57,26,51,32,51,39.5v7C51,54,57,60,64.5,60h0C72,60,78,54,78,46.5v-7C78,32,72,26,64.5,26L64.5,26z"/></g></svg>
                <span id="loginText" class="littleMsg">Hi, <?php echo get_username() ?>!</span>
            </div>
        </a>

    </div>
</div>

<div class="theBody">
    <div class="addTaskArea">
        <button class="dropdownparent" id="addTaskButton">
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

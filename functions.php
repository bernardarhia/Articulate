<?php
function view($path, $args = null)
{
    // check if views folder
    if (!is_dir("views")) mkdir("views");
    // check if file exists
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/views/$path.php")) throw new Exception("File doesn't exist", 1);
    include_once $_SERVER['DOCUMENT_ROOT'] . "/views/$path.php";
}
<?php

if (!isset($_SESSION)) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/projects.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/components.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/blocks.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/interpreter.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/rules.php";

if (isset($_POST['evaluate'])) {
    $projects = array();
    $projects = ProjectHandler::getAllProjects();
    $interpreter = new Interpreter($projects);
    $interpreter->runAll();
    $interpreter->run();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

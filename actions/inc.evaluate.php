<?php

if (!isset($_SESSION)) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/projects.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/components.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/blocks.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/interpreter.php";

if (isset($_POST['evaluate'])) {
    $projects = array();
    foreach($_SESSION['projects'] as $project) {
        $projects[] = unserialize($project);
    }
    $interpreter = new Interpreter($projects);
    try {
        $interpreter->runAll();
    } catch(Exception $e) {
        
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
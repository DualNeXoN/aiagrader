<?php

if (!isset($_SESSION)) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/projects.php";

if (isset($_POST['json'])) {

    $requestData = json_decode($_POST['json'], true);

    if ($requestData) {
        $response = array();
        $projects = ProjectHandler::getAllProjectsByFilename($requestData['request']);

        foreach ($projects as $project) {
            $response[$project->getFileName()] = array(count($project->getBlocks()), count($project->getComponents()));
        }

        echo json_encode($response);
    } else {
        echo "Error: JSON decoding failed.";
    }
} else {
    echo "Error: Incorrect content type.";
}

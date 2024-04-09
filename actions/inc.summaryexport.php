<?php

if (!isset($_SESSION)) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/projects.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/components.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/blocks.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/interpreter.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/rules.php";

if (isset($_POST['export-data'])) {

    $exportData = array();
    $exportData[] = ["Project Name", "Grade [%]", "Components", "Blocks", "Used components"];

    $maxPoints = GradingSystemUtils::getMaxPoints();

    foreach (ProjectHandler::getAllProjects() as $project) {
        $projectDataToExport = array();

        $projectDataToExport[] = $project->getFileName();
        $projectDataToExport[] = GradingSystemUtils::getPercentage(GradingSystemUtils::getAchievedPointsOfProjectByFileName($project->getFileName()), $maxPoints);
        $projectDataToExport[] = count($project->getComponents());
        $projectDataToExport[] = count($project->getBlocks());
        $projectDataToExport[] = getUsedComponents($project);

        $exportData[] = $projectDataToExport;
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="projects_summary.csv"');

    $fp = fopen('php://output', 'wb');

    foreach ($exportData as $row) {
        fputcsv($fp, $row);
    }

    fclose($fp);
}

function getUsedComponents(Project $project) {
    $componentsArray = array();
    foreach ($project->getComponents() as $component) {
        if (!isset($componentsArray[$component->getType()])) $componentsArray[$component->getType()] = 0;
        $componentsArray[$component->getType()]++;
    }

    $components = "";
    $index = -1;
    foreach($componentsArray as $componentName => $componentCount) {
        $index++;
        $components = $components . $componentName . " (" . $componentCount . ")";
        if ($index < count($componentsArray) - 1) {
            $components = $components . ", ";
        }
    }
    return $components;
}

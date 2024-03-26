<?php

require_once "projects.php";
require_once "blocks.php";
require_once "components.php";
require_once "interpreter.php";

echo "<h1>PROJECTS FOUND</h1>";
$discoveredAiaProjects = ProjectHandler::discoverAiaProjects();
print_r($discoveredAiaProjects);
$project = ProjectHandler::loadProject($discoveredAiaProjects[1]);
//$project->info();
//echo "<div style=\"background-color:white; color:black; font-size: 20px; font-family: sans-serif\">";
$interpreter = new Interpreter($project);
$interpreter->run();
//echo "</div>";
$project->info();

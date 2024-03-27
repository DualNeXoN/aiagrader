<?php

require_once "projects.php";
require_once "blocks.php";
require_once "components.php";
require_once "interpreter.php";

echo "<h1>PROJECTS FOUND</h1>";
$discoveredAiaProjects = ProjectHandler::discoverAiaProjects();
print_r($discoveredAiaProjects);
$project = ProjectHandler::loadProject($discoveredAiaProjects[7]);
//$project->info();
$interpreter = new Interpreter($project);
$interpreter->run();
$project->info();

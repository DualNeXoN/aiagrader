<?php

echo "<h1>PROJECTS FOUND</h1>";
$discoveredAiaProjects = ProjectHandler::discoverAiaProjects();
if(count($discoveredAiaProjects) > 0) {
    print_r($discoveredAiaProjects);
    $project = ProjectHandler::loadProject($discoveredAiaProjects[0]);
    $interpreter = new Interpreter(array($project));
    $interpreter->runAll();
    foreach($project->getLogs() as $log) {
        echo $log;
    }
    $project->info();
} else {
    echo "No projects found";
}
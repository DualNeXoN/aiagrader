<?php

echo "<h1>PROJECTS FOUND</h1>";
$discoveredAiaProjects = ProjectHandler::discoverAiaProjects();
if(count($discoveredAiaProjects) > 0) {
    print_r($discoveredAiaProjects);
    $project = ProjectHandler::loadProject($discoveredAiaProjects[0]);
    $interpreter = new Interpreter($project);
    $interpreter->run();
    $project->info();
} else {
    echo "No projects found";
}

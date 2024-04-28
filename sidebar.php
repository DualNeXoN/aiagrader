<!-- Sidebar-->
<div class="border-end bg-dark text-white" id="sidebar-wrapper">
    <div class="sidebar-heading border-bottom bg-dark text-white">MIT App Analyzer</div>
    <div class="list-group list-group-flush">
        <!--<a class="list-group-item list-group-item-action list-group-item-dark p-3" href="?page=dashboard" <?php if (!isset($_GET["page"]) || ($_GET["page"] == "dashboard")) echo ("style=\"font-weight: bold\"") ?>>Dashboard</a>-->
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="?page=projectmanager" <?php if (isset($_GET["page"]) && $_GET["page"] == "projectmanager") echo ("style=\"font-weight: bold\"") ?>>Project Manager (<?= count(ProjectHandler::discoverAiaProjects()) ?>)</a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="?page=gradingrules" <?php if (isset($_GET["page"]) && $_GET["page"] == "gradingrules") echo ("style=\"font-weight: bold\"") ?>>Grading Rules</a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="?page=summary" <?php if (isset($_GET["page"]) && (($_GET["page"] == "summary") || $_GET["page"] == "projectdetails")) echo ("style=\"font-weight: bold\"") ?>>Summary<?php if (isset($_GET["page"]) && $_GET["page"] == "projectdetails") echo "<br>&#x2022; " . $_GET["project"] ?></a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="?page=debug" <?php if (isset($_GET["page"]) && $_GET["page"] == "debug") echo ("style=\"font-weight: bold\"") ?>>Debug</a>
    </div>
</div>
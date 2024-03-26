<!-- Sidebar-->
<div class="border-end bg-dark text-white" id="sidebar-wrapper">
    <div class="sidebar-heading border-bottom bg-dark text-white">MIT App Analyzer</div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="?page=dashboard" <?php if (!isset($_GET["page"]) || ($_GET["page"] == "dashboard")) echo ("style=\"font-weight: bold\"") ?>>Dashboard</a>
        <a class="list-group-item list-group-item-action list-group-item-dark p-3" href="?page=debug" <?php if (isset($_GET["page"]) && $_GET["page"] == "debug") echo ("style=\"font-weight: bold\"") ?>>Debug</a>
    </div>
</div>
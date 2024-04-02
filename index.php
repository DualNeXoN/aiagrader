<!DOCTYPE html>
<html lang="en">

<?php
session_start();
require_once("head.php");
require_once("projects.php");
require_once("blocks.php");
require_once("components.php");
require_once("interpreter.php");
require_once("rules.php");
?>

<body>
    <div class="d-flex bg-dark" id="wrapper">
        <?php require_once('sidebar.php') ?>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-dark border-bottom">
                <div class="container-fluid justify-content-start">
                    <button class="btn btn-primary" id="sidebarToggle" style="margin: 0 5px"><i class="fa fa-bars"></i></button>
                    <?php if(isset($_GET["page"]) && $_GET["page"] == "projectdetails") echo '<a class="btn btn-primary" href="?page=summary" style="margin: 0 5px"><i class="fa fa-arrow-left"></i></a>'?>
                </div>
            </nav>
            <?php
            $pageFilename = 'page.dashboard.php';
            if(!isset($_GET['page'])) {
                require $pageFilename;
            } else {
                $pageFilename = 'page.' . $_GET['page'] . '.php';
                if (file_exists($pageFilename)) {
                    require $pageFilename;
                } else {
                    require('404.php');
                }
            }
            ?>
        </div>
    </div>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
</body>

</html>
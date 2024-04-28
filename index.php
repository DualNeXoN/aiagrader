<!DOCTYPE html>
<html lang="en">

<?php
session_start();
require_once("head.php");
require_once("classes.php");
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
                    <?php if (isset($_GET["page"]) && $_GET["page"] == "projectdetails") echo '<a class="btn btn-primary" href="?page=summary" style="margin: 0 5px"><i class="fa fa-arrow-left"></i></a>' ?>
                </div>
            </nav>
            <?php require_once("contentHandler.php"); ?>
        </div>
    </div>
    <?php require_once("libraries.scripts.php"); ?>
</body>

</html>
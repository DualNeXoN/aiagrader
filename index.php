<!DOCTYPE html>
<html lang="en">

<?php require_once('head.php') ?>

<body>
    <div class="d-flex bg-dark" id="wrapper">
        <?php require_once('sidebar.php') ?>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-dark border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle"><i class="fa fa-bars"></i></button>
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
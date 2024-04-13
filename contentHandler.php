<?php

$pageFilename = 'page.dashboard.php';
if (!isset($_GET['page'])) {
    require $pageFilename;
} else {
    $pageFilename = 'page.' . $_GET['page'] . '.php';
    if (file_exists($pageFilename)) {
        require $pageFilename;
    } else {
        require('404.php');
    }
}

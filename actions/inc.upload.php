<?php

if (!isset($_SESSION)) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/projects.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/components.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/blocks.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/interpreter.php";

if (isset($_POST['upload'])) {
    $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/projectsUpload/" . session_id() . "/";

    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $errors = [];
    $uploadedFiles = 0;

    $fileExtensionsAllowed = ['aia'];
    $files = $_FILES['files'];

    for ($i = 0; $i < count($files['name']); $i++) {
        $fileName = $files['name'][$i];
        $fileSize = $files['size'][$i];
        $fileTmpName  = $files['tmp_name'][$i];
        $temp = explode('.', $fileName);
        $fileExtension = strtolower(end($temp));

        if (!in_array($fileExtension, $fileExtensionsAllowed)) {
            $errors[] = "Súbor $fileName má neplatnú príponu. Sú povolené iba súbory .aia.";
        } else {
            if (move_uploaded_file($fileTmpName, $uploadDirectory . $fileName)) {
                $uploadedFiles++;
            } else {
                $errors[] = "Nastala chyba pri nahrávaní súboru $fileName.";
            }
        }
    }

    if ($uploadedFiles == count($files['name']) && empty($errors)) {
        $filePaths = ProjectHandler::discoverAiaProjects();
        foreach ($files['name'] as $file) {
            foreach ($filePaths as $filePath) {
                if (str_ends_with($filePath, $file)) {
                    $project = ProjectHandler::loadProject($filePath);
                    $_SESSION['projects'][$project->getFileName()] = serialize($project);
                    break;
                }
            }
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger' role='alert'>$error</div>";
        }
    }
} else if (isset($_POST['delete'])) {
    deleteProject($_POST['index']);
    exit;
} else if (isset($_POST['delete-all'])) {
    while (count(ProjectHandler::discoverAiaProjects()) > 0) {
        deleteProject(0);
    }
    exit;
}

function deleteProject($index) {
    $filesFound = ProjectHandler::discoverAiaProjects();
    unset($_SESSION['projects'][pathinfo($filesFound[$index], PATHINFO_FILENAME)]);
    unlink($filesFound[$index]);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

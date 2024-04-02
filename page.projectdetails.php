<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col">
            <?php
            $project = unserialize($_SESSION['projects'][$_GET['project']]);
            $project->info();
            ?>
        </div>
    </div>
</div>
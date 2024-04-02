<!-- Page content-->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col text-center">

        </div>
    </div>
    <div class="row justify-content-start">
        <div class="col-2">
            <button class="btn btn-success w-100" type="button" data-bs-toggle="modal" data-bs-target="#modalUpload" style="margin: 5px"><i class="fa fa-plus"></i> Add projects</button>
        </div>
        <?php
        if (count(ProjectHandler::discoverAiaProjects()) > 0) {
        ?>
            <div class="col-2">
                <form action="actions/inc.upload.php" method="post">
                    <button class="btn btn-danger w-100" style="margin: 5px" type="submit" name="delete-all"><i class="fa fa-trash"></i> Delete all</button>
                </form>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="row justify-content-center">
        <div class="col text-center">
            <table class="table table-striped table-dark table-bordered">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Project Name</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $projectCount = 0;
                    foreach (ProjectHandler::discoverAiaProjects() as $aiaProject) {
                    ?>
                        <tr>
                            <th scope="row"><?= ++$projectCount ?></th>
                            <td><?= (preg_match("/([^\\\\\/]+)\.aia$/", $aiaProject, $matches) ? $matches[1] : "") ?></td>
                            <td>
                                <form action="actions/inc.upload.php" method="post">
                                    <input hidden name="index" value="<?= ($projectCount - 1) ?>"></input>
                                    <button class="btn btn-danger btn-sm" type="submit" name="delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpload" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="actions/inc.upload.php" method="post" enctype="multipart/form-data" class="mb-3">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="color: black">
                <div class="modal-body">
                    <div class="row justify-content-center" style="margin: 20px 0">
                        <div class="col text-center">
                            <div class="form-group">
                                <label for="fileUpload">Choose .aia files for upload:</label>
                                <input type="file" name="files[]" id="fileUpload" multiple="multiple" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="upload" class="btn btn-primary w-100">Upload</button>
                </div>
            </div>
        </div>
    </form>
</div>
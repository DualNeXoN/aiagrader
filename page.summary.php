<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col">
            <button class="btn btn-success" style="margin: 5px"><i class="fa fa-play"></i> Start evaluation</button>
            <button class="btn btn-primary" style="margin: 5px"><i class="fa fa-download"></i> Export data</button>
            <?php
            $count = 0;
            if (isset($_SESSION['projects'])) {
                foreach ($_SESSION['projects'] as $aiaProject) {
                    $project = unserialize($aiaProject);
                    $count++;
            ?>
                    <div class="accordion" id="summaryList" style="margin-top: 10px">
                        <div class="accordion-item" id="summaryItem-<?= $count ?>" style="margin-bottom: 15px">
                            <h2 class="accordion-header" id="panelsStayOpen-heading<?= $count ?>">
                                <div class="d-flex">
                                    <div class="px-2 align-self-center">
                                        <input class="form-check-input" type="checkbox" checked></input>
                                    </div>
                                    <div class="px-2 align-self-center">
                                        <a class="btn btn-primary" href="?page=projectdetails&project=<?= $project->getFileName() ?>"><i class="fa fa-eye"></i></a>
                                    </div>
                                    <div class="flex-grow-1">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?= $count ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?= $count ?>">
                                            <div class="d-flex w-100">
                                                <div><b><?= $project->getFileName() ?></b><br>Components: <?= count($project->getComponents()) ?><br>Blocks: <?= count($project->getBlocks()) ?></div>
                                                <div class="flex-grow-1 px-2"></div>
                                                <div class="text-center align-self-center px-2"><span class="badge rounded-pill bg-success">Compiled</span><br><span class="badge rounded-pill bg-primary">0/0</span></div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </h2>
                            <div id="panelsStayOpen-collapse<?= $count ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?= $count ?>">
                                <div class="accordion-body">
                                    <div class="row justify-content-center">
                                        <div class="col text-center">
                                            <div class="row">
                                                <div class="col-11 text-center">
                                                    <div class="table-responsive-sm">
                                                        <table class="table table-bordered border-primary text-black">
                                                            <thead>
                                                                <tr>
                                                                    <td colspan="2"><b>Components in project</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="col" style="width: 80%">Component</th>
                                                                    <th scope="col">Count</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($project->getComponentsTypeCount() as $componentType => $componentCount) {
                                                                    echo "<tr>";
                                                                    echo "<td>" . $componentType . "</td>";
                                                                    echo "<td>" . $componentCount . "</td>";
                                                                    echo "</tr>";
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <div class="row">
                                                <div class="col-11 text-center">
                                                <div class="table-responsive-sm">
                                                <table class="table table-bordered border-primary text-black">
                                                            <thead>
                                                                <tr>
                                                                    <td colspan="2"><b>Events in project</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="col" style="width: 50%">Instance</th>
                                                                    <th scope="col">Event</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($project->getEvents() as $event) {
                                                                    echo "<tr>";
                                                                    echo "<td>" . $event->getInstanceName() . "</td>";
                                                                    echo "<td>" . $event->getEventName() . "</td>";
                                                                    echo "</tr>";
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                            </div>
                                                    <div class="table-responsive-sm">
                                                        <table class="table table-bordered border-primary text-black">
                                                            <thead>
                                                                <tr>
                                                                    <td colspan="2"><b>Blocks in project</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="col" style="width: 80%">Block</th>
                                                                    <th scope="col">Count</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($project->getBlocksTypeCount() as $blockType => $blockCount) {
                                                                    echo "<tr>";
                                                                    echo "<td>" . $blockType . "</td>";
                                                                    echo "<td>" . $blockCount . "</td>";
                                                                    echo "</tr>";
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</div>
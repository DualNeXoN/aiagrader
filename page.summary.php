<div class="container-fluid">
    <div class="d-flex">
        <div class="px-2 py-2">
            <button class="btn btn-primary"><i class="fa fa-filter"></i> Apply filter</button>
        </div>
        <div class="px-2 py-2">
            <?= blockFilter() ?>
        </div>
        <div class="px-2 py-2 flex-grow-1 text-end">
            <button class="btn btn-primary"><i class="fa fa-download"></i> Export data</button>
        </div>
        <div class="px-2 py-2">
            <form action="actions/inc.evaluate.php" method="post">
                <button class="btn btn-success h-100" type="submit" name="evaluate"><i class="fa fa-play"></i> Start evaluation</button>
            </form>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col">
            <?php
            $count = 0;
            if (isset($_SESSION['projects'])) {
                foreach ($_SESSION['projects'] as $aiaProject) {
                    $project = unserialize($aiaProject);
                    $count++;
            ?>
                    <div class="accordion px-2 py-2" id="summaryList">
                        <div class="accordion-item" id="summaryItem-<?= $count ?>">
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
                                                <div class="text-center align-self-center px-2"><span class="badge rounded-pill bg-<?= $project->isEvaluated() ? ($project->isRunnable() ? "success" : "danger") : "secondary" ?>" style="width: 75px"><?= $project->isEvaluated() ? ($project->isRunnable() ? "OK" : "Errors") : "On hold" ?></span><br><br><span class="badge rounded-pill bg-primary" style="width: 75px">0/0</span></div>
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
                                                                    foreach ($event as $block) {
                                                                        echo "<tr>";
                                                                        echo "<td>" . $block->getInstanceName() . "</td>";
                                                                        echo "<td>" . $block->getEventName() . "</td>";
                                                                        echo "</tr>";
                                                                    }
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

<?php

function blockFilter() {
?>
    <div class="dropdown h-100">
        <button class="btn btn-secondary h-100 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">Block filter</button>
        <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton" style="max-height: 200px; overflow-y: auto;">
            <?php foreach (Block::BLOCK_ALIASES as $index => $option) : ?>
                <li>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="<?php echo htmlspecialchars($option); ?>" id="check<?php echo $index; ?>">
                        <label class="form-check-label" for="check<?php echo $index; ?>">
                            <?php echo htmlspecialchars($option); ?>
                        </label>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
}

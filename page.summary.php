<?php
$projects = ProjectHandler::getAllProjects();
?>

<div class="container-fluid">
    <div class="d-flex">
        <div class="px-1 py-2">
            <button class="btn btn-primary" onclick="markAllListedProjects(true)"><i class="fa fa-check"></i></button>
        </div>
        <div class="px-1 py-2">
            <button class="btn btn-primary" onclick="markAllListedProjects(false)"><i class="fa fa-times"></i></button>
        </div>
        <div class="px-3 py-2">
            <?= filterButton($projects) ?>
        </div>
        <div class="px-2 py-2 flex-grow-1 text-end">
            <button class="btn btn-primary" <?= (isset($_SESSION['projects']) && count($_SESSION['projects']) > 0 ? "" : "disabled") ?>><i class="fa fa-download"></i> Export data</button>
        </div>
        <div class="px-2 py-2">
            <form action="actions/inc.evaluate.php" method="post">
                <button class="btn btn-success h-100" type="submit" <?= (isset($_SESSION['projects']) && count($_SESSION['projects']) > 0 ? "" : "disabled") ?> name="evaluate"><i class="fa fa-play"></i> Start evaluation</button>
            </form>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col">
            <?php
            $count = 0;
            foreach ($projects as $project) {
                $pointsPerProject = GradingSystemUtils::getAchievedPointsOfProjectByFileName($project->getFileName());
                $pointsMax = GradingSystemUtils::getMaxPoints();
                $count++;
            ?>
                <div class="accordion mx-2 my-3" id="summaryList">
                    <div class="accordion-item" id="summaryItem-<?= $count ?>" data-project-name="<?= $project->getFileName() ?>" grade="<?= GradingSystemUtils::getAchievedPointsOfProjectByFileName($project->getFileName()) ?>" data-project-state="<?= $project->isEvaluated() ? ($project->isRunnable() ? 1 : 2)  : 0 ?>" data-blocks="<?= implode(',', array_map(function ($block) {
                                                                                                                                                                                                                                                                                                                                                return htmlspecialchars($block->getAlias());
                                                                                                                                                                                                                                                                                                                                            }, $project->getBlocks())) ?>" data-components="<?= implode(',', array_map(function ($component) {
                                                                                                                                                                                                                                                                                                                                                                                                return htmlspecialchars($component->getType());
                                                                                                                                                                                                                                                                                                                                                                                            }, $project->getComponents())) ?>">
                        <h2 class="accordion-header" id="panelsStayOpen-heading<?= $count ?>">
                            <div class="d-flex">
                                <div class="px-2 align-self-center">
                                    <input class="form-check-input" type="checkbox" id="summaryItemCheckbox-<?= $count ?>" project-name="<?= $project->getFileName() ?>" onchange="fetchDataAndUpdateCharts()" checked></input>
                                </div>
                                <div class="px-2 align-self-center">
                                    <a class="btn btn-primary" href="?page=projectdetails&project=<?= $project->getFileName() ?>"><i class="fa fa-eye"></i></a>
                                </div>
                                <div class="flex-grow-1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?= $count ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?= $count ?>">
                                        <div class="d-flex w-100">
                                            <div><b><?= $project->getFileName() ?></b><br>Components: <?= count($project->getComponents()) ?><br>Blocks: <?= count($project->getBlocks()) ?></div>
                                            <div class="flex-grow-1 px-2"></div>
                                            <div class="text-center align-self-center px-2">
                                                <span class="badge rounded-pill bg-<?= $project->isEvaluated() ? ($project->isRunnable() ? "success" : "danger") : "secondary" ?>" style="width: 90px"><?= $project->isEvaluated() ? ($project->isRunnable() ? "OK <i class='fa fa-check'></i>" : "Errors <i class='fa fa-exclamation'></i>") : "On hold" ?></span>
                                                <br><br>
                                                <span class="badge rounded-pill bg-<?= $project->needRegrade() ? "secondary" : ($pointsPerProject == $pointsMax ? "success" : ($pointsPerProject == 0 ? "danger" : "warning")) ?>" style="width: 90px">
                                                    <?= $project->needRegrade() ? "No grade yet" : GradingSystemUtils::getPercentage($pointsPerProject, $pointsMax) . "%" ?>
                                                </span>
                                            </div>
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
                                    <div class="col text-center">
                                        <div class="row">
                                            <div class="col-11">
                                                <?php if (count($project->getLogs()) > 0) { ?>
                                                    <div class="px-2 text-start border border-primary">
                                                        <div><?php foreach ($project->getLogs() as $log) echo $log ?></div>
                                                    </div>
                                                <?php } ?>
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
            ?>
        </div>
    </div>
    <?= count(ProjectHandler::getAllProjects()) > 0 ? generateCharts() : "" ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/summary.charts.js"></script>
<script src="js/summary.filter.js"></script>

<script>
    function isVisible(elem) {
        return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
    }

    function getAllCheckboxesOfListedProjects() {
        let checkboxes = [];
        document.querySelectorAll('.accordion-item').forEach(item => {
            if (isVisible(item)) {
                let id = item.getAttribute('id');
                let count = id.split('-').pop();
                let checkbox = document.querySelector('#summaryItemCheckbox-' + count);
                if (checkbox) {
                    checkboxes.push(checkbox);
                }
            }
        });
        return checkboxes;
    }

    function markAllListedProjects(bool) {
        let checkboxes = getAllCheckboxesOfListedProjects();
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = bool;
        });
        fetchDataAndUpdateCharts();
    }

    document.addEventListener("DOMContentLoaded", function() {
        function getCheckedProjectNames() {
            var checkedProjectNames = [];
            document.querySelectorAll('.form-check-input:checked').forEach(function(checkbox) {
                var projectName = checkbox.getAttribute('project-name');
                if (projectName) {
                    checkedProjectNames.push(projectName);
                }
            });
            return checkedProjectNames;
        }

        var checkedNames = getCheckedProjectNames();
        console.log(checkedNames);
    });
</script>

<?php
function blockFilter(&$projects) {
?>
    <div class="my-2">
        <div class="d-flex">
            <input class="form-check-input" type="checkbox" id="filter-enable-block"></input>
            <div class="mx-2">Blocks</div>
        </div>
        <div class="d-flex my-2">
            <div class="">
                <button class="btn btn-primary btn-sm" id="filter-block-checkall" disabled><i class="fa fa-check"></i></button>
            </div>
            <div class="mx-1 w-100">
                <input class="form-control" type="text" placeholder="Search block..." id="filter-block-search" disabled></input>
            </div>
        </div>
        <div style="height: 200px; overflow-y: scroll">
            <?php foreach (Block::BLOCK_ALIASES as $index => $option) : ?>
                <?php
                $count = ProjectHandler::getProjectsContainsBlockCount($option, $projects);
                if ($count > 0) :
                ?>
                    <div class="form-check form-check-block">
                        <input class="form-check-input filter-block-check" type="checkbox" value="<?= htmlspecialchars($option) ?>" id="filter-block-check-<?= htmlspecialchars($option) ?>" checked disabled>
                        <label class="form-check-label" for="filter-block-check-<?= htmlspecialchars($option) ?>">
                            <?= htmlspecialchars($option) ?> (<?= $count ?>)
                        </label>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php
}

function componentFilter(&$projects) {
?>
    <div class="my-2">
        <div class="d-flex">
            <input class="form-check-input" type="checkbox" id="filter-enable-component"></input>
            <div class="mx-2">Components</div>
        </div>
        <div class="d-flex my-2">
            <div class="">
                <button class="btn btn-primary btn-sm" id="filter-component-checkall" disabled><i class="fa fa-check"></i></button>
            </div>
            <div class="mx-1 w-100">
                <input class="form-control" type="text" placeholder="Search component..." id="filter-component-search" disabled></input>
            </div>
        </div>
        <div style="height: 200px; overflow-y: scroll">
            <?php
            $array = BaseComponent::COMPONENT_ALIASES;
            sort($array);
            foreach ($array as $option) :
                $count = ProjectHandler::getProjectsContainsComponentCount($option, $projects);
                if ($count > 0) :
            ?>
                    <div class="form-check form-check-component">
                        <input class="form-check-input filter-component-check" type="checkbox" value="<?= htmlspecialchars($option) ?>" id="filter-component-check-<?= htmlspecialchars($option) ?>" checked disabled>
                        <label class="form-check-label" for="filter-component-check-<?= htmlspecialchars($option) ?>">
                            <?= htmlspecialchars($option) ?> (<?= $count ?>)
                        </label>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php
}

function stateFilter() {
?>
    <div class="d-flex">
        <input class="form-check-input" type="checkbox" id="filter-enable-state"></input>
        <div class="mx-2">State</div>
    </div>
    <select class="form-select my-2" id="filter-project-state" disabled>
        <option value="-1" selected>All states</option>
        <option value="0">On hold</option>
        <option value="1">OK</option>
        <option value="2">Errors</option>
    </select>
<?php
}

function nameFilter() {
?>
    <div class="py-2">
        <div class="d-flex">
            <input class="form-check-input" type="checkbox" id="filter-enable-name"></input>
            <div class="mx-2">Name</div>
        </div>
        <input class="form-control my-2" type="text" placeholder="Project name filter..." id="filter-project-name" disabled></input>
    </div>
<?php
}

function filterButton(&$projects) {
?>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal"><i class="fa fa-filter"></i> Filter</button>
    <div class="modal fade text-black" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter window</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <?= nameFilter() ?>
                            <?= stateFilter() ?>
                        </div>
                        <div class="col">
                            <?= componentFilter($projects) ?>
                        </div>
                        <div class="col">
                            <?= blockFilter($projects) ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger" id="filter-reset"><i class="fa fa-filter"></i> Reset</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="filter-apply"><i class="fa fa-filter"></i> Apply</button>
                </div>
            </div>
        </div>
    </div>
<?php
}

function generateCharts() {
?>
    <div class="row mx-2 my-4">
        <div class="col">
            <canvas class="border" id="chart-project-complexity"></canvas>
        </div>
    </div>
<?php
}

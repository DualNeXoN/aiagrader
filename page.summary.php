<div class="container-fluid">
    <div class="d-flex">
        <div class="px-2 py-2">
            <?= filterButton() ?>
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
            if (isset($_SESSION['projects'])) {
                foreach ($_SESSION['projects'] as $aiaProject) {
                    $project = unserialize($aiaProject);
                    $count++;
            ?>
                    <div class="accordion mx-2 my-3" id="summaryList">
                        <div class="accordion-item" id="summaryItem-<?= $count ?>" data-project-name="<?= $project->getFileName() ?>" grade="0" data-project-state="<?= $project->isEvaluated() ? ($project->isRunnable() ? 1 : 2)  : 0 ?>" data-blocks="<?= implode(',', array_map(function ($block) {
                                                                                                                                                                                                                                                                return htmlspecialchars($block->getAlias());
                                                                                                                                                                                                                                                            }, $project->getBlocks())) ?>" data-components="<?= implode(',', array_map(function ($component) {
                                                                                                                                                                                                                                                                                                                return htmlspecialchars($component->getType());
                                                                                                                                                                                                                                                                                                            }, $project->getComponents())) ?>">
                            <h2 class="accordion-header" id="panelsStayOpen-heading<?= $count ?>">
                                <div class="d-flex">
                                    <div class="px-2 align-self-center">
                                        <input class="form-check-input" type="checkbox" id="summaryItemCheckbox-<?= $count ?>" checked></input>
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
                                                    <span class="badge rounded-pill bg-<?= $project->isEvaluated() ? ($project->isRunnable() ? "success" : "danger") : "secondary" ?>" title="<?= $project->isEvaluated() ? ($project->isRunnable() ? "Project compiled without problems" : "Interpreter threw error while compiling the project") : "Project not compiled yet" ?>" style="width: 80px"><?= $project->isEvaluated() ? ($project->isRunnable() ? "OK <i class='fa fa-check'></i>" : "Errors <i class='fa fa-exclamation'></i>") : "On hold" ?></span>
                                                    <br><br>
                                                    <span class="badge rounded-pill bg-secondary" title="Project has no grade yet" style="width: 80px">No grade</span>
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
            }
            ?>
        </div>
    </div>
</div>

<script>
    //filter component window
    document.addEventListener("DOMContentLoaded", function() {
        const componentToggleButton = document.getElementById('filter-component-checkall');
        let allComponentsChecked = true;

        componentToggleButton.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.filter-component-check');

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allComponentsChecked;
            });

            allComponentsChecked = !allComponentsChecked;
            if (allComponentsChecked) {
                componentToggleButton.classList.add("btn-primary");
                componentToggleButton.classList.remove("btn-outline-primary");
            } else {
                componentToggleButton.classList.remove("btn-primary");
                componentToggleButton.classList.add("btn-outline-primary");
            }
        });

        const componentSearchInput = document.getElementById('filter-component-search');

        componentSearchInput.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const checkboxes = document.querySelectorAll('.form-check-component');

            checkboxes.forEach(check => {
                const label = check.querySelector('label').textContent.toLowerCase();
                if (label.includes(searchText)) {
                    check.style.display = '';
                } else {
                    check.style.display = 'none';
                }
            });
        });
    });

    //filter block window
    document.addEventListener("DOMContentLoaded", function() {
        const blockToggleButton = document.getElementById('filter-block-checkall');
        let allBlocksChecked = true;

        blockToggleButton.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.filter-block-check');

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allBlocksChecked;
            });

            allBlocksChecked = !allBlocksChecked;
            if (allBlocksChecked) {
                blockToggleButton.classList.add("btn-primary");
                blockToggleButton.classList.remove("btn-outline-primary");
            } else {
                blockToggleButton.classList.remove("btn-primary");
                blockToggleButton.classList.add("btn-outline-primary");
            }
        });

        const blockSearchInput = document.getElementById('filter-block-search');

        blockSearchInput.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const checkboxes = document.querySelectorAll('.form-check-block');

            checkboxes.forEach(check => {
                const label = check.querySelector('label').textContent.toLowerCase();
                if (label.includes(searchText)) {
                    check.style.display = '';
                } else {
                    check.style.display = 'none';
                }
            });
        });
    });

    //filter reset
    document.addEventListener("DOMContentLoaded", function() {
        const filterResetButton = document.getElementById('filter-reset');

        filterResetButton.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.filter-component-check, .filter-block-check');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });

            const inputs = document.querySelectorAll('#filter-component-search, #filter-block-search, #filter-project-name');
            inputs.forEach(input => {
                input.value = '';
            });

            const checkboxContainers = document.querySelectorAll('.form-check-component, .form-check-block');
            checkboxContainers.forEach(container => {
                container.style.display = '';
            });

            const selects = document.querySelectorAll('.form-select');
            selects.forEach(select => {
                select.value = select.querySelector('option').value;
            });

            let componentToggleButton = document.getElementById('filter-component-checkall');
            let blockToggleButton = document.getElementById('filter-block-checkall');
            blockToggleButton.classList.add("btn-primary");
            blockToggleButton.classList.remove("btn-outline-primary");
            componentToggleButton.classList.add("btn-primary");
            componentToggleButton.classList.remove("btn-outline-primary");
        });
    });

    //filter apply
    document.addEventListener("DOMContentLoaded", function() {
        const filterApplyButton = document.getElementById('filter-apply');

        filterApplyButton.addEventListener('click', function() {
            const needFilterName = document.getElementById('filter-enable-name').checked;
            const needFilterState = document.getElementById('filter-enable-state').checked;
            const needFilterComponents = document.getElementById('filter-enable-component').checked;
            const needFilterBlocks = document.getElementById('filter-enable-block').checked;

            const projectNameFilter = document.getElementById('filter-project-name').value.toLowerCase();
            const projectStateFilter = document.querySelector('.form-select').value;

            const selectedBlocks = Array.from(document.querySelectorAll('.filter-block-check:checked')).map(el => el.value);
            const selectedComponents = Array.from(document.querySelectorAll('.filter-component-check:checked')).map(el => el.value);

            const accordionItems = document.querySelectorAll('.accordion-item');

            accordionItems.forEach(item => {
                let result = true;
                const matchesName = item.getAttribute('data-project-name').toLowerCase().includes(projectNameFilter);
                const matchesState = projectStateFilter === '-1' || item.getAttribute('data-project-state') === projectStateFilter;
                const itemBlocks = item.getAttribute('data-blocks').split(',');
                const itemComponents = item.getAttribute('data-components').split(',');
                const matchesBlocks = selectedBlocks.length > 0 && selectedBlocks.some(block => itemBlocks.includes(block));
                const matchesComponents = selectedComponents.length > 0 && selectedComponents.some(component => itemComponents.includes(component));

                if (result && needFilterName) {
                    result = matchesName;
                }

                if (result && needFilterState) {
                    result = matchesState;
                }

                if (result && needFilterComponents) {
                    result = matchesComponents;
                }

                if (result && needFilterBlocks) {
                    result = matchesBlocks;
                }

                item.style.display = result ? '' : 'none';
            });
        });
    });

    //filter category toggle
    document.addEventListener("DOMContentLoaded", function() {
        const filterEnableName = document.getElementById('filter-enable-name');

        filterEnableName.addEventListener('change', function() {
            document.getElementById('filter-project-name').disabled = !filterEnableName.checked;
        });

        const filterEnableState = document.getElementById('filter-enable-state');

        filterEnableState.addEventListener('change', function() {
            document.getElementById('filter-project-state').disabled = !filterEnableState.checked;
        });

        const filterEnableComponents = document.getElementById('filter-enable-component');

        filterEnableComponents.addEventListener('change', function() {
            document.getElementById('filter-component-checkall').disabled = !filterEnableComponents.checked;
            document.getElementById('filter-component-search').disabled = !filterEnableComponents.checked;
            document.querySelectorAll('.filter-component-check').forEach(item => {
                item.disabled = !filterEnableComponents.checked;
            });
        });

        const filterEnableBlocks = document.getElementById('filter-enable-block');

        filterEnableBlocks.addEventListener('change', function() {
            document.getElementById('filter-block-checkall').disabled = !filterEnableBlocks.checked;
            document.getElementById('filter-block-search').disabled = !filterEnableBlocks.checked;
            document.querySelectorAll('.filter-block-check').forEach(item => {
                item.disabled = !filterEnableBlocks.checked;
            });
        });
    });
</script>


<?php
function blockFilter() {
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
                $count = count(ProjectHandler::getProjectsContainsBlock($option));
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

function componentFilter() {
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
            foreach ($array as $index => $option) :
                $count = count(ProjectHandler::getProjectsContainsComponent($option));
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

function filterButton() {
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
                            <?= componentFilter() ?>
                        </div>
                        <div class="col">
                            <?= blockFilter() ?>
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

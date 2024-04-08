<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col text-center"><b>Grading rules</b></div>
    </div>
    <div class="row justify-content-center">
        <div class="col-1 text-center">
            <form action="actions/inc.rules.php" method="post">
                <button class="btn btn-primary w-100" type="submit" name="add-rule">Add rule</button>
            </form>
        </div>
        <div class="col-1 text-center">
            <form action="actions/inc.rules.php" method="post">
                <button class="btn btn-danger w-100" type="submit" name="rule-delete-all">Reset rules</button>
            </form>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col">
            <?php
            $count = 0;
            if (isset($_SESSION['rules'])) :
                foreach (GradingSystemUtils::getRuleSets() as $ruleSet) :
                    $count++;
            ?>
                    <div class="accordion mx-2 my-3" id="rulesetList">
                        <div class="accordion-item" id="rulesetItem-<?= $ruleSet->getId() ?>" ruleset-id="<?= $ruleSet->getId() ?>">
                            <h2 class="accordion-header" id="panelsStayOpen-heading<?= $count ?>">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?= $count ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?= $count ?>">
                                            <div class="d-flex w-100">
                                                <div><b><?= $ruleSet->getDescription() ?></b></div>
                                                <div class="mx-3">
                                                    <span class="badge rounded-pill bg-<?= count($ruleSet->getActions()) > 0 ? "primary" : "danger" ?>" style="width: auto">Actions: <?= count($ruleSet->getActions()) ?></span>
                                                </div>
                                                <div>
                                                    <span class="badge rounded-pill bg-<?= count($ruleSet->getComponentResults()) > 0 ? "primary" : "danger" ?>" style="width: auto">Exp. results: <?= count($ruleSet->getComponentResults()) ?></span>
                                                </div>
                                                <div class="flex-grow-1 px-2"></div>
                                                <div class="mx-3">
                                                    <span class="badge rounded-pill bg-<?= $ruleSet->getPoints() > 0 ? "success" : "danger" ?>" style="width: auto">Points: <?= $ruleSet->getPoints() ?></span>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="mx-2 my-1">
                                        <?= generateRulesetEditButton($ruleSet->getId(), $ruleSet->getDescription(), $ruleSet->getPoints()) ?>
                                    </div>
                                    <div class="mx-2 my-1">
                                        <form action="actions/inc.rules.php" method="post">
                                            <input type="text" value="<?= $ruleSet->getId() ?>" name="ruleset-id" hidden></input>
                                            <button class="btn btn-danger" name="rule-delete-by-id" type="submit"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </h2>
                            <div id="panelsStayOpen-collapse<?= $count ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?= $count ?>">
                                <div class="accordion-body">
                                    <div class="row justify-content-center">
                                        <div class="col text-center">
                                            <div class="row">
                                                <div class="col text-center">
                                                    <div class="accordion" id="accordionPanelsStayOpen">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="panelsStayOpen-heading-actions-<?= $ruleSet->getId() ?>">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse-actions-<?= $ruleSet->getId() ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse-actions-<?= $ruleSet->getId() ?>"><b>Actions / Inputs</b></button>
                                                            </h2>
                                                            <div id="panelsStayOpen-collapse-actions-<?= $ruleSet->getId() ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading-actions-<?= $ruleSet->getId() ?>">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive-sm">
                                                                        <table class="table table-bordered border-primary text-black">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col" style="width: 40%">Instance</th>
                                                                                    <th scope="col">Event</th>
                                                                                    <th scope="col" style="width: 100px"></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                for ($index = 0; $index < count($ruleSet->getActions()); $index++) :
                                                                                    $action = $ruleSet->getActions()[$index];
                                                                                ?>
                                                                                    <form action="actions/inc.rules.php" method="post">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input class="form-control text-center my-2" type="text" name="action-component-instance" value="<?= $action->getComponentInstance() ?>"></input>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div class="d-flex"><?= generateActionSelect($ruleSet->getId(), $index) ?></div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="text" name="ruleset-id" value="<?= $ruleSet->getId() ?>" hidden></input>
                                                                                                <input type="text" name="action-index" value="<?= $index ?>" hidden></input>
                                                                                                <button class="btn btn-success btn-sm mx-1 my-2" name="action-save"><i class="fa fa-save"></i></button>
                                                                                                <button class="btn btn-danger btn-sm mx-1 my-2" name="action-delete"><i class="fa fa-trash"></i></button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </form>
                                                                                <?php endfor; ?>
                                                                                <tr>
                                                                                    <th class="text-center" colspan="3">
                                                                                        <?= generateAddActionButton($ruleSet->getId()) ?>
                                                                                    </th>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="panelsStayOpen-heading-expected-<?= $ruleSet->getId() ?>">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse-expected-<?= $ruleSet->getId() ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse-expected-<?= $ruleSet->getId() ?>"><b>Expected results</b></button>
                                                            </h2>
                                                            <div id="panelsStayOpen-collapse-expected-<?= $ruleSet->getId() ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading-expected-<?= $ruleSet->getId() ?>">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive-sm">
                                                                        <table class="table table-bordered border-primary text-black">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col" style="width: 25%">Instance</th>
                                                                                    <th scope="col">Property</th>
                                                                                    <th scope="col" style="width: 33%">Expected output</th>
                                                                                    <th scope="col" style="width: 100px"></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                $index = -1;
                                                                                foreach ($ruleSet->getComponentResults() as $componentResult) :
                                                                                    $index++;
                                                                                ?>
                                                                                    <form action="actions/inc.rules.php" method="post">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input class="form-control text-center my-2" type="text" name="result-component-instance" value="<?= $componentResult->getInstanceName() ?>"></input>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div class="d-flex"><?= generatePropertySelect($ruleSet->getId(), $index) ?></div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input class="form-control text-center my-2" type="text" name="result-expectedresult" value="<?= $componentResult->getExpectedResult() ?>"></input>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="text" name="ruleset-id" value="<?= $ruleSet->getId() ?>" hidden></input>
                                                                                                <input type="text" name="result-index" value="<?= $index ?>" hidden></input>
                                                                                                <button class="btn btn-success btn-sm mx-1 my-2" name="result-save"><i class="fa fa-save"></i></button>
                                                                                                <button class="btn btn-danger btn-sm mx-1 my-2" name="result-delete"><i class="fa fa-trash"></i></button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </form>
                                                                                <?php endforeach; ?>
                                                                                <tr>
                                                                                    <th class="text-center" colspan="4">
                                                                                        <?= generateAddExpectedResultButton($ruleSet->getId()) ?>
                                                                                    </th>
                                                                                </tr>
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
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>
</div>

<script>
    function filterActionOptionsById(rulesetId, index) {
        const componentSelect = (index == "-1" ? document.getElementById('select-action-component-' + rulesetId) : document.getElementById('select-action-component-' + rulesetId + "-" + index));
        const eventsSelect = (index == "-1" ? document.getElementById('select-action-event-' + rulesetId) : document.getElementById('select-action-event-' + rulesetId + "-" + index));
        const allEvents = <?php echo json_encode(Action::EVENTS); ?>;

        const selectedComponent = componentSelect.value;
        eventsSelect.innerHTML = '';

        if (allEvents[selectedComponent]) {
            allEvents[selectedComponent].forEach(event => {
                const option = document.createElement('option');
                option.value = event;
                option.text = event;
                eventsSelect.appendChild(option);
            });
        }
    }

    function filterPropertyOptionsById(rulesetId, index) {
        const componentSelect = (index == "-1" ? document.getElementById('select-result-component-' + rulesetId) : document.getElementById('select-result-component-' + rulesetId + "-" + index));
        const propertiesSelect = (index == "-1" ? document.getElementById('select-result-property-' + rulesetId) : document.getElementById('select-result-property-' + rulesetId + "-" + index));
        const allProperties = <?php echo json_encode(ComponentResult::PROPERTIES); ?>;

        const selectedComponent = componentSelect.value;
        propertiesSelect.innerHTML = '';

        if (allProperties[selectedComponent]) {
            allProperties[selectedComponent].forEach(property => {
                const option = document.createElement('option');
                option.value = property;
                option.text = property;
                propertiesSelect.appendChild(option);
            });
        }
    }
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<?php

function generateActionSelect(String $rulesetId, String $index): void {
?>
    <select class="form-select my-2" id="select-action-component-<?= $rulesetId ?>-<?= $index ?>" name="select-action-component-<?= $rulesetId ?>-<?= $index ?>" onchange="filterActionOptionsById('<?= $rulesetId ?>', '<?= $index ?>')">
        <?php foreach (Action::EVENTS as $component => $eventList) : ?>
            <option value="<?= $component ?>" <?= GradingSystemUtils::getRuleSetById($rulesetId)->getActions()[$index]->getEventKey() == $component ? "selected" : "" ?>><?= $component ?></option>
        <?php endforeach; ?>
    </select>
    <select class="form-select my-2" id="select-action-event-<?= $rulesetId ?>-<?= $index ?>" name="select-action-event-<?= $rulesetId ?>-<?= $index ?>">
        <?php foreach (Action::EVENTS as $component => $eventList) :
            if (GradingSystemUtils::getRuleSetById($rulesetId)->getActions()[$index]->getEventKey() != $component) continue;
            foreach ($eventList as $eventName) : ?>
                <option value="<?= $eventName ?>" <?= GradingSystemUtils::getRuleSetById($rulesetId)->getActions()[$index]->getEventName() == $eventName ? "selected" : "" ?>><?= $eventName ?></option>
        <?php endforeach;
        endforeach; ?>
    </select>
<?php
}

function generatePropertySelect(String $rulesetId, String $index): void {
?>
    <select class="form-select my-2" id="select-result-component-<?= $rulesetId ?>-<?= $index ?>" name="select-result-component-<?= $rulesetId ?>-<?= $index ?>" onchange="filterPropertyOptionsById('<?= $rulesetId ?>', '<?= $index ?>')">
        <?php foreach (ComponentResult::PROPERTIES as $component => $propertyList) : ?>
            <option value="<?= $component ?>" <?= GradingSystemUtils::getRuleSetById($rulesetId)->getComponentResults()[$index]->getPropertyKey() == $component ? "selected" : "" ?>><?= $component ?></option>
        <?php endforeach; ?>
    </select>
    <select class="form-select my-2" id="select-result-property-<?= $rulesetId ?>-<?= $index ?>" name="select-result-property-<?= $rulesetId ?>-<?= $index ?>">
        <?php foreach (ComponentResult::PROPERTIES as $component => $propertyList) :
            if (GradingSystemUtils::getRuleSetById($rulesetId)->getComponentResults()[$index]->getPropertyKey() != $component) continue;
            foreach ($propertyList as $property) : ?>
                <option value="<?= $property ?>" <?= GradingSystemUtils::getRuleSetById($rulesetId)->getComponentResults()[$index]->getProperty() == $property ? "selected" : "" ?>><?= $property ?></option>
        <?php endforeach;
        endforeach; ?>
    </select>
<?php
}

function generateAddActionButton(String $rulesetId): void {
?>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalActionAdd-<?= $rulesetId ?>">Add action</button>

    <div class="modal fade" id="modalActionAdd-<?= $rulesetId ?>" tabindex="-1" aria-labelledby="modalActionAdd-<?= $rulesetId ?>Label" aria-hidden="true">
        <form action="actions/inc.rules.php" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalActionAdd-<?= $rulesetId ?>Label">Add action dialog</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input class="form-control text-center my-2" type="text" name="action-component-instance" value="" placeholder="Instance name" required></input>
                        <div class="d-flex">
                            <select class="form-select my-2" id="select-action-component-<?= $rulesetId ?>" name="select-action-component-<?= $rulesetId ?>" onchange="filterActionOptionsById('<?= $rulesetId ?>', '-1')">
                                <?php foreach (Action::EVENTS as $component => $eventList) : ?>
                                    <option value="<?= $component ?>"><?= $component ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select my-2" id="select-action-event-<?= $rulesetId ?>" name="select-action-event-<?= $rulesetId ?>">
                                <?php foreach (Action::EVENTS as $component => $eventList) :
                                    if ($component != "Button") continue;
                                    foreach ($eventList as $eventName) : ?>
                                        <option value="<?= $eventName ?>"><?= $eventName ?></option>
                                <?php endforeach;
                                endforeach; ?>
                            </select>
                        </div>
                        <input type="text" name="ruleset-id" value="<?= $rulesetId ?>" hidden></input>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="action-add">Add</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

<?php
}

function generateAddExpectedResultButton(String $rulesetId): void {
?>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalExpectedResultAdd-<?= $rulesetId ?>">Add expected result</button>

    <div class="modal fade" id="modalExpectedResultAdd-<?= $rulesetId ?>" tabindex="-1" aria-labelledby="modalExpectedResultAdd-<?= $rulesetId ?>Label" aria-hidden="true">
        <form action="actions/inc.rules.php" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalExpectedResultAdd-<?= $rulesetId ?>Label">Add exptected result dialog</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input class="form-control text-center my-2" type="text" name="result-component-instance" value="" placeholder="Instance name" required></input>
                        <div class="d-flex">
                            <select class="form-select my-2" id="select-result-component-<?= $rulesetId ?>" name="select-result-component-<?= $rulesetId ?>" onchange="filterPropertyOptionsById('<?= $rulesetId ?>', '-1')">
                                <?php foreach (ComponentResult::PROPERTIES as $component => $propertyList) : ?>
                                    <option value="<?= $component ?>"><?= $component ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select my-2" id="select-result-property-<?= $rulesetId ?>" name="select-result-property-<?= $rulesetId ?>">
                                <?php foreach (ComponentResult::PROPERTIES as $component => $propertyList) :
                                    if ($component != "Button") continue;
                                    foreach ($propertyList as $property) : ?>
                                        <option value="<?= $property ?>"><?= $property ?></option>
                                <?php endforeach;
                                endforeach; ?>
                            </select>
                        </div>
                        <input class="form-control text-center my-2" type="text" name="result-component-expected" value="" placeholder="Expected property value" required></input>
                        <input type="text" name="ruleset-id" value="<?= $rulesetId ?>" hidden></input>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="result-add">Add</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

<?php
}

function generateRulesetEditButton(String $rulesetId, String $description, int $points): void {
?>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRulesetEdit-<?= $rulesetId ?>"><i class="fa fa-edit"></i></button>

    <div class="modal fade" id="modalRulesetEdit-<?= $rulesetId ?>" tabindex="-1" aria-labelledby="modalRulesetEdit-<?= $rulesetId ?>Label" aria-hidden="true">
        <form action="actions/inc.rules.php" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRulesetEdit-<?= $rulesetId ?>Label">Edit ruleset description and points</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input class="form-control text-center my-2" type="text" name="ruleset-description" value="<?= $description ?>" placeholder="Description" required></input>
                        <input class="form-control text-center my-2" type="number" min="0" name="ruleset-points" value="<?= $points ?>" placeholder="Points" required></input>
                        <input type="text" name="ruleset-id" value="<?= $rulesetId ?>" hidden></input>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="ruleset-save">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

<?php
}

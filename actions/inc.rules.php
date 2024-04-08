<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/projects.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/rules.php";

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_POST['rule-delete-all'])) {
    unset($_SESSION['rules']);
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
} else if (isset($_POST['rule-delete-by-id'])) {
    $ruleSetId = $_POST['ruleset-id'];

    foreach ($_SESSION['rules'] as $key => $ruleSetData) {
        $ruleSet = unserialize($ruleSetData);
        if($ruleSet->getId() == $ruleSetId) {
            unset($_SESSION['rules'][$key]);
            break;
        }
    }
    resetProjectResults();

    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_POST['add-rule'])) {
    $newRule = new RuleSet(array(), array(), 0, "New Rule");
    $newRule->save();
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_POST['action-delete'])) {
    $ruleSetId = $_POST['ruleset-id'];
    $actionIndex = $_POST['action-index'];
    GradingSystemUtils::getRuleSetById($ruleSetId)->removeActionByIndex($actionIndex);
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_POST['action-save'])) {
    $ruleSetId = $_POST['ruleset-id'];
    $actionIndex = $_POST['action-index'];
    $componentInstance = $_POST['action-component-instance'];
    $eventKey = $_POST['select-action-component-' . $ruleSetId . "-" . $actionIndex];
    $eventName = $_POST['select-action-event-' . $ruleSetId . "-" . $actionIndex];
    $ruleset = GradingSystemUtils::getRuleSetById($ruleSetId);
    $action = $ruleset->getActions()[$actionIndex];
    $action->setComponentInstance($componentInstance);
    $action->setEventKey($eventKey);
    $action->setEventName($eventName);
    $ruleset->save();
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_POST['action-add'])) {
    $ruleSetId = $_POST['ruleset-id'];
    $componentInstance = $_POST['action-component-instance'];
    $eventKey = $_POST['select-action-component-' . $ruleSetId];
    $eventName = $_POST['select-action-event-' . $ruleSetId];
    $ruleset = GradingSystemUtils::getRuleSetById($ruleSetId);
    $action = new Action($componentInstance, $eventName, $eventKey);
    $ruleset->addAction($action);
    $ruleset->save();
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_POST['result-delete'])) {
    $ruleSetId = $_POST['ruleset-id'];
    $resultIndex = $_POST['result-index'];
    GradingSystemUtils::getRuleSetById($ruleSetId)->removeResultByIndex($resultIndex);
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_POST['result-add'])) {
    $ruleSetId = $_POST['ruleset-id'];
    $componentInstance = $_POST['result-component-instance'];
    $propertyKey = $_POST['select-result-component-' . $ruleSetId];
    $property = $_POST['select-result-property-' . $ruleSetId];
    $expectedValue = $_POST['result-component-expected'];
    $ruleset = GradingSystemUtils::getRuleSetById($ruleSetId);
    $componentResult = new ComponentResult($componentInstance, $property, $expectedValue, $propertyKey);
    $ruleset->addResult($componentResult);
    $ruleset->save();
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_POST['result-save'])) {
    $ruleSetId = $_POST['ruleset-id'];
    $resultIndex = $_POST['result-index'];
    $componentInstance = $_POST['result-component-instance'];
    $propertyKey = $_POST['select-result-component-' . $ruleSetId . "-" . $resultIndex];
    $property = $_POST['select-result-property-' . $ruleSetId . "-" . $resultIndex];
    $expectedValue = $_POST['result-expectedresult'];
    $ruleset = GradingSystemUtils::getRuleSetById($ruleSetId);
    $componentResult = $ruleset->getComponentResults()[$resultIndex];
    $componentResult->setInstanceName($componentInstance);
    $componentResult->setPropertyKey($propertyKey);
    $componentResult->setProperty($property);
    $componentResult->setExpectedResult($expectedValue);
    $ruleset->save();
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_POST['ruleset-save'])) {
    $ruleSetId = $_POST['ruleset-id'];
    $rulesetDescription = $_POST['ruleset-description'];
    $rulesetPoints = $_POST['ruleset-points'];
    $ruleset = GradingSystemUtils::getRuleSetById($ruleSetId);
    $ruleset->setDescription($rulesetDescription);
    $ruleset->setPoints($rulesetPoints);
    $ruleset->save();
    resetProjectResults();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

//header('Location: ' . $_SERVER['HTTP_REFERER']);

function resetProjectResults() {
    foreach (ProjectHandler::getAllProjects() as $project) {
        $project->resetResults();
        $project->setNeedRegrade(true);
        $project->save();
    }
}

<?php

class Interpreter {

    private array $projects;
    private ?Project $project = null;

    function __construct(array $projects = array()) {
        $this->projects = $projects;
    }

    public function runAll() {
        foreach ($this->projects as $project) {
            $this->project = $project;
            $project->resetComponents();
            $project->resetLogs();
            $project->setEvaluated(true);
            try {
                $this->initialize();
                $this->interpretAll();
                $project->setRunnable(true);
            } catch (Throwable $e) {
                $project->setRunnable(false);
                $project->addLog("<br><br><text style='color:red'>Interpreter error:</text>");
                $project->addLog("<br><text style='color:red'><b>" . $e->getMessage() . "</b></text>");
                $project->addLog("<br><text style='color:red'>Please check project integrity manually. In case project works correctly report bug and provide .aia file for further investigation of the bug.</text>");
                $project->addLog("<br><a class='btn btn-warning my-2' target='_blank' href='https://github.com/DualNeXoN'><b>Report bug</b></a>");
            }
            $project->save();
        }
    }

    public function run() {
        $ruleSets = GradingSystemUtils::getRuleSets();
        foreach ($this->projects as $project) {
            $this->project = $project;
            $this->project->resetResults();
            if (!$this->project->isRunnable()) continue;
            $this->project->resetLogs();
            $this->interpretBasedOnRules($ruleSets);
            $this->project->setNeedRegrade(false);
            $this->project->save();
        }
    }

    private function initialize() {
        try {
            $this->project->addLog("<h2>Evaluating blocks for initialization</h2>");

            //global declarations
            foreach ($this->project->getStartingBlocks() as $startingBlock) {
                if ($startingBlock->getType() === "global_declaration") {
                    $this->project->addLog("<h4>Block</h4>");
                    $startingBlock->evaluate();
                }
            }

            //events for initialization
            foreach ($this->project->getStartingBlocks() as $startingBlock) {
                if ($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") {
                    $this->project->addLog("<h4>Block</h4>");
                    $startingBlock->evaluate();
                }
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function interpretBasedOnRules(array $ruleSets) {
        foreach ($ruleSets as $ruleSet) {

            try {
                $this->project->resetComponents();
                $this->initialize();
                $this->project->resetComponents();
                $this->processInputs($ruleSet);
                $this->project->addLog("<h2>Evaluating blocks based on rules</h2>");

                foreach ($ruleSet->getActions() as $action) {
                    foreach ($this->project->getStartingBlocks() as $startingBlock) {
                        if ($startingBlock->getType() !== "component_event") continue;
                        if (($startingBlock->getInstanceName() === $action->getComponentInstance()) && ($startingBlock->getEventName() === $action->getEventName())) {
                            $this->project->addLog("<h4>Block</h4>");
                            $startingBlock->evaluate();
                        }
                    }
                }

                $passed = true;

                foreach ($ruleSet->getComponentResults() as $result) {
                    if ($this->project->getComponentByName($result->getInstanceName())->getProperty($result->getProperty()) != $result->getExpectedResult()) {
                        $passed = false;
                        break;
                    }
                }

                $this->project->addResult($ruleSet, $passed);
            } catch (Throwable $e) {
                $this->project->addResult($ruleSet, false);
            }
        }
    }

    private function processInputs(RuleSet $ruleSet): void {
        foreach ($ruleSet->getInputs() as $input) {
            $this->project->getComponentByName($input->getComponentInstance())->setProperty($input->getProperty(), $input->getInputValue());
        }
    }

    private function interpretAll() {
        $this->project->addLog("<h2>Evaluating event blocks based on rules</h2>");
        foreach ($this->project->getStartingBlocks() as $startingBlock) {
            if ($startingBlock->getType() === "global_declaration") continue;
            if ($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") continue;
            $this->project->addLog("<h4>Block</h4>");
            try {
                $startingBlock->evaluate();
            } catch (Throwable $e) {
                if ($e->getMessage() != "Division by zero") {
                    throw new Exception($e->getMessage());
                }
            }
        }
    }

    public function getProject(): Project {
        return $this->project;
    }

    public function setProject($project): void {
        $this->project = $project;
    }
}

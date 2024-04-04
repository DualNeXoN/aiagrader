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

    public function run(array $ruleSets = null) {
        $this->project->setInterpreter($this);
        $this->initialize();
        $this->interpretBasedOnRules($ruleSets);
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
        }
    }

    private function interpretAll() {
        try {
            $this->project->addLog("<h2>Evaluating event blocks based on rules</h2>");
            foreach ($this->project->getStartingBlocks() as $startingBlock) {
                if ($startingBlock->getType() === "global_declaration") continue;
                if ($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") continue;
                $this->project->addLog("<h4>Block</h4>");
                $startingBlock->evaluate();
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getProject(): Project {
        return $this->project;
    }

    public function setProject($project): void {
        $this->project = $project;
    }
}

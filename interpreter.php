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
            $project->setEvaluated(true);
            try {
                $this->initialize();
                $this->interpretAll();
                $project->setRunnable(true);
            } catch (Exception $e) {
                $project->setRunnable(false);
            }
            ProjectHandler::saveProject($project);
        }
    }

    public function run(array $ruleSets = null) {
        $this->project->setInterpreter($this);
        $this->initialize();
        $this->interpretBasedOnRules($ruleSets);
    }

    private function initialize() {
        try {
            echo "<h2>Evaluating blocks for initialization</h2>";

            //global declarations
            foreach ($this->project->getStartingBlocks() as $startingBlock) {
                if ($startingBlock->getType() === "global_declaration") {
                    $startingBlock->evaluate();
                }
            }

            //events for initialization
            foreach ($this->project->getStartingBlocks() as $startingBlock) {
                if ($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") {
                    $startingBlock->evaluate();
                }
            }
        } catch (Exception $e) {
            throw new Exception("Evaluation error");
        }
    }

    private function interpretBasedOnRules(array $ruleSets) {
        foreach ($ruleSets as $ruleSet) {
        }
    }

    private function interpretAll() {
        try {
            echo "<h2>Evaluating event blocks based on rules</h2>";
            foreach ($this->project->getStartingBlocks() as $startingBlock) {
                if ($startingBlock->getType() === "global_declaration") continue;
                if ($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") continue;
                $startingBlock->evaluate();
            }
        } catch (Exception $e) {
            throw new Exception("Evaluation error");
        }
    }

    public function getProject(): Project {
        return $this->project;
    }

    public function setProject($project): void {
        $this->project = $project;
    }
}

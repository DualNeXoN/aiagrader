<?php

class Interpreter {

    private ?Project $project;

    function __construct($project = null) {
        $this->project = $project;
    }

    public function run() {
        $this->initialize();
        //$this->interpretBasedOnRules();
        $this->interpretAll();
    }

    private function initialize() {
        echo "<h2>Evaluating blocks for initialization</h2>";

        //global declarations
        foreach ($this->project->getStartingBlocks() as $startingBlock) {
            if($startingBlock->getType() === "global_declaration") {
                try {
                    echo "<h2>Block</h2>";
                    $startingBlock->evaluate();
                } catch (Exception $e) {
                }
            }
        }

        //events for initialization
        foreach ($this->project->getStartingBlocks() as $startingBlock) {
            if($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") {
                try {
                    echo "<h2>Block</h2>";
                    $startingBlock->evaluate();
                } catch (Exception $e) {
                }
            }
        }
    }

    private function interpretBasedOnRules() {
    }

    private function interpretAll() {
        echo "<h2>Evaluating event blocks based on rules</h2>";
        foreach ($this->project->getStartingBlocks() as $startingBlock) {
            if($startingBlock->getType() === "global_declaration") continue;
            if($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") continue;
            try {
                echo "<h2>Block</h2>";
                $startingBlock->evaluate();
            } catch (Exception $e) {
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

<?php

class Interpreter {

    private ?Project $project;

    function __construct($project = null) {
        $this->project = $project;
    }

    public function run(array $ruleSets = null) {
        $this->project->setInterpreter($this);
        $count = 0;
        $this->initialize($count);
        //$this->interpretBasedOnRules($ruleSets);
        $this->interpretAll($count);
    }

    private function initialize(&$count) {
        echo "<h2>Evaluating blocks for initialization</h2>";

        //global declarations
        foreach ($this->project->getStartingBlocks() as $startingBlock) {
            if ($startingBlock->getType() === "global_declaration") {
                try {
                    $count++;
                    echo "<h2>Block [" . $count . "]</h2>";
                    $startingBlock->evaluate();
                } catch (Exception $e) {
                }
            }
        }

        //events for initialization
        foreach ($this->project->getStartingBlocks() as $startingBlock) {
            if ($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") {
                try {
                    $count++;
                    echo "<h2>Block [" . $count . "]</h2>";
                    $startingBlock->evaluate();
                } catch (Exception $e) {
                }
            }
        }
    }

    private function interpretBasedOnRules(array $ruleSets) {
        foreach($ruleSets as $ruleSet) {
            
        }
    }

    private function interpretAll($count) {
        echo "<h2>Evaluating event blocks based on rules</h2>";
        foreach ($this->project->getStartingBlocks() as $startingBlock) {
            if ($startingBlock->getType() === "global_declaration") continue;
            if ($startingBlock->getType() === "component_event" && $startingBlock->getEventName() === "Initialize") continue;
            try {
                $count++;
                echo "<h2>Block [" . $count . "]</h2>";
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

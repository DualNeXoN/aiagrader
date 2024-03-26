<?php

class Interpreter {

    private ?Project $project;

    function __construct($project = null) {
        $this->project = $project;
    }

    public function run() {
        $this->initialize();
        $this->interpret();
    }

    private function initialize() {
    }

    private function interpret() {
    }

    public function getProject(): Project {
        return $this->project;
    }

    public function setProject($project): void {
        $this->project = $project;
    }
}

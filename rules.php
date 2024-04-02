<?php

class RuleSet {

    private array $actions;
    private array $componentResults;

    function __construct(array $actions, array $componentResults) {
        $this->actions = $actions;
        $this->componentResults = $componentResults;
    }

    public function getActions(): array {
        return $this->actions;
    }

    public function getComponentResults(): array {
        return $this->componentResults;
    }

}

class Action {

    public static array $events = [
        ["Button" => "Click"],
        ["Button" => "LongClick"],
        ["Button" => "TouchDown"],
        ["Button" => "TouchUp"],
        ["Button" => "GotFocus"],
        ["Button" => "LostFocus"],
    ];

    private String $instanceName;
    private String $eventName;

    function __construct(String $instanceName, String $eventName) {
        $this->instanceName = $instanceName;
        $this->eventName = $eventName;
    }

    public function getInstanceName(): String {
        return $this->instanceName;
    }

    public function getEventName(): String {
        return $this->eventName;
    }

}

class ComponentResult {

    private String $instanceName;
    private String $property;
    private mixed $expectedResult;

    function __construct(String $instanceName, String $property, mixed $expectedResult) {
        $this->instanceName = $instanceName;
        $this->property = $property;
        $this->expectedResult = $expectedResult;
    }

    public function getInstanceName(): String {
        return $this->instanceName;
    }

    public function getProperty(): String {
        return $this->property;
    }

    public function getExpectedResult(): mixed {
        return $this->expectedResult;
    }

}
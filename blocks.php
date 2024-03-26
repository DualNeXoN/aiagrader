<?php

class Block {

    protected ?Project $project = null;
    protected String $id;
    protected String $type;
    protected ?Block $parent = null;
    protected String $screen;
    protected int $sequence;
    protected array $child;
    protected array $metadata;
    protected String $interpreterText;

    function __construct($data) {
        $this->id = $data['id'];
        $this->type = $data['type'];
        $this->screen = $data['screen'];
        $this->sequence = $data['sequence'];
        $this->child = array();
        if (is_array($data['metadata'])) {
            $this->metadata = $data['metadata'];
        } else {
            $this->metadata = [$data['metadata']];
        }
        $this->interpreterText = "<text style=\"color:red\">Unknown action for: <b>" . $this->type . "</b></text><br>";
    }

    public function getId(): String {
        return $this->id;
    }

    public function getType(): String {
        return $this->type;
    }

    public function getParent(): ?Block {
        return $this->parent;
    }

    public function setParent(Block $parent): void {
        $this->parent = $parent;
    }

    public function getScreen(): String {
        return $this->screen;
    }

    public function getSequence(): int {
        return $this->sequence;
    }

    public function getChild(): array {
        return $this->child;
    }

    public function getChildById(String $id): ?Block {
        foreach ($this->child as $key => $block) {
            if ($block->getId() === $id) {
                return $block;
            }
        }
        return null;
    }

    public function addChild(Block $block): void {
        array_push($this->child, $block);
    }

    public function removeChild(Block $blockToRemove): void {
        $this->removeChildById($blockToRemove->getId());
    }

    public function removeChildById(String $id): void {
        foreach ($this->child as $key => $block) {
            if ($block->getId() === $id) {
                unset($this->child[$key]);
                $this->child = array_values($this->child);
                break;
            }
        }
    }

    public function getProject(): Project {
        return $this->project;
    }

    public function setProject($project): void {
        $this->project = $project;
    }

    public function isStartingBlock(): bool {
        return $this->parent == null;
    }

    public function getMetadata(): array {
        return $this->metadata;
    }

    public function evaluate() {
        echo (strlen($this->interpreterText) > 0 ? $this->interpreterText : "");
    }
}

class BlockComponentEvent extends Block {

    protected String $componentType;
    protected String $instanceName;
    protected String $eventName;

    function __construct($data) {
        parent::__construct($data);
        $this->componentType = $data['component_type'];
        $this->instanceName = $data['instance_name'];
        $this->eventName = $data['event_name'];
        $this->interpreterText = "Event <b>" . $this->eventName . "</b> on instance <b>" . $this->instanceName . "</b> fired.<br>";
    }

    public function getComponentType(): String {
        return $this->componentType;
    }

    public function getInstanceName(): String {
        return $this->instanceName;
    }

    public function getEventName(): String {
        return $this->eventName;
    }

    public function evaluate() {
        parent::evaluate();
        foreach ($this->child as $child) {
            $child->evaluate();
        }
    }
}

class BlockComponentSetGet extends Block {

    protected String $componentType;
    protected String $setOrGet;
    protected String $propertyName;
    protected String $instanceName;

    function __construct($data) {
        parent::__construct($data);
        $this->componentType = $data['component_type'];
        $this->setOrGet = $data['set_or_get'];
        $this->propertyName = $data['property_name'];
        $this->instanceName = $data['instance_name'];
        $this->interpreterText = ($this->setOrGet === "get" ? "Getting property <b>" . $this->propertyName . "</b> of instance <b>" . $this->instanceName . "</b><br>" : "Setting property <b>" . $this->propertyName . "</b> of instance <b>" . $this->instanceName . "</b> to ");
    }

    public function getComponentType(): String {
        return $this->componentType;
    }

    public function isSet(): String {
        return $this->setOrGet == "set";
    }

    public function isGet(): String {
        return $this->setOrGet == "get";
    }

    public function getPropertyName(): String {
        return $this->propertyName;
    }

    public function getInstanceName(): String {
        return $this->instanceName;
    }

    public function evaluate() {
        parent::evaluate();
        if ($this->setOrGet === "get") {
            return $this->project->getComponentByName($this->instanceName)->getProperty($this->propertyName);
        } else {
            $this->project->getComponentByName($this->instanceName)->setProperty($this->propertyName, $this->child[0]->evaluate());
        }
    }
}

class BlockText extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "<b>" . $this->field . "</b> (text)<br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        foreach ($this->child as $child) {
            $child->evaluate();
        }
        return $this->field;
    }
}

class BlockColor extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "<b>" . $this->field . "</b> (color)<br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        foreach ($this->child as $child) {
            $child->evaluate();
        }
        return $this->field;
    }
}

class BlockMathNumber extends Block {

    protected int $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = intval($data['field']);
        $this->interpreterText = "<b>" . $this->field . "</b> (number)<br>";
    }

    public function getField(): int {
        return $this->field;
    }

    public function evaluate(): int {
        parent::evaluate();
        foreach ($this->child as $child) {
            $child->evaluate();
        }
        return $this->field;
    }
}

class BlockMathAdd extends Block {

    function __construct($data) {
        parent::__construct($data);
    }

    public function evaluate(): int {
        $this->interpreterText = "<b>" . $this->child[0]->getField() . " + " . $this->child[1]->getField() . "</b><br>";
        parent::evaluate();
        $result = $this->child[0]->evaluate() + $this->child[1]->evaluate();
        return $result;
    }
}

class BlockMathCompare extends Block {

    protected String $field;
    protected array $map = ['GT' => '>', 'LT' => '<'];

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Comparing <b>" . $this->map[$this->field] . "</b><br>";
    }

    public function evaluate(): bool {
        parent::evaluate();
        $result = false;
        switch ($this->map[$this->field]) {
            case '>':
                $result = $this->child[0]->evaluate() > $this->child[1]->evaluate();
                break;
            case '<':
                $result = $this->child[0]->evaluate() < $this->child[1]->evaluate();
                break;
        }
        return $result;
    }
}

class BlockLogicBoolean extends Block {

    protected bool $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = boolval($data['field']);
        $this->interpreterText = "<b>" . $this->field . "</b> (bool)<br>";
    }

    public function getField(): bool {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        foreach ($this->child as $child) {
            $child->evaluate();
        }
        return $this->field;
    }
}

class BlockLogicNegate extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "<b>!</b> ";
    }

    public function evaluate() {
        parent::evaluate();
        foreach ($this->child as $child) {
            $child->evaluate();
        }
    }
}

class BlockGlobalDeclaration extends Block {

    protected $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Declaring <b>global variable</b> key: <b>" . $this->field . "</b> value: ";
    }

    public function getField() {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        $this->project->setVariable("global " . $this->field, $this->child[0]->evaluate());
    }
}

class BlockLexicalVariableSet extends Block {

    protected $field;
    protected bool $isGlobal;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Setting <b>variable</b> key: <b>" . $this->field . "</b> value: ";
    }

    public function getField() {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        $this->project->setVariable($this->field, $this->child[0]->evaluate());
        echo "Variable <b>" . $this->field . "</b> set to <b>" . $this->project->getVariable($this->field) . "</b><br>";
    }
}

class BlockLexicalVariableGet extends Block {

    protected $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Getting <b>variable</b> key: <b>" . $this->field . "</b> value: ";
    }

    public function getField() {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        echo "<b>" . $this->project->getVariable($this->field) . "</b><br>";
        return $this->project->getVariable($this->field);
    }
}

class BlockControlsIf extends Block {

    protected ?Block $ifCondition = null;
    protected ?Block $elseifCondition = null;
    protected array $code = ['if' => array(), 'elseif' => array(), 'else' => array()];

    protected bool $hasElseif;
    protected bool $hasElse;

    function __construct($data) {
        parent::__construct($data);
        $this->hasElseif = isset($data['elseif']);
        $this->hasElse = isset($data['else']);
        $this->interpreterText = "Evaluating <b>if controls</b><br>";
    }

    public function hasElseif(): bool {
        return $this->hasElseif;
    }

    public function hasElse(): bool {
        return $this->hasElse;
    }

    public function getIfCondition(): ?Block {
        return $this->ifCondition;
    }

    public function getElseifCondition(): ?Block {
        return $this->elseifCondition;
    }

    public function setIfCondition(Block $block) {
        $this->ifCondition = $block;
    }

    public function setElseifCondition(Block $block) {
        $this->elseifCondition = $block;
    }

    public function addCode(String $conditionType, Block $block): void {
        array_push($this->code[$conditionType], $block);
    }

    public function getCode(): array {
        return $this->code;
    }

    public function evaluate() {
        parent::evaluate();
        echo "Controls contains if " . ($this->hasElseif ? "& elseif " : "") . ($this->hasElse ? "& else " : "") . "<br>";
        echo "Testing <b>IF</b> statement<br>";
        $lastResult = false;

        if ($lastResult = $this->ifCondition->evaluate()) {
            echo "Condition <b>IF</b> is <b>TRUE</b>. Evaluating <b>IF</b> code<br>";
            foreach ($this->code['if'] as $child) {
                $child->evaluate();
            }
        } else {
            echo "Condition <b>IF</b> is <b>FALSE</b><br>";
        }

        if ($lastResult == false && $this->hasElseif) {
            echo "Testing <b>ELSE IF</b> statement<br>";
            if ($lastResult = $this->elseifCondition->evaluate()) {
                echo "Condition <b>ELSE IF</b> is <b>TRUE</b>. Evaluating <b>ELSE IF</b> code<br>";
                foreach ($this->code['elseif'] as $child) {
                    $child->evaluate();
                }
            } else {
                echo "Condition <b>ELSE IF</b> is <b>FALSE</b><br>";
            }
        }

        if ($lastResult == false && $this->hasElse) {
            echo "Evaluating <b>ELSE</b> code<br>";
            foreach ($this->code['else'] as $child) {
                $child->evaluate();
            }
        }
    }
}

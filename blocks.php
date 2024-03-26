<?php

class Block {

    protected String $id;
    protected String $type;
    protected ?Block $parent = null;
    protected String $screen;
    protected int $sequence;
    protected array $child;
    protected array $metadata;

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

    public function isStartingBlock(): bool {
        return $this->parent == null;
    }    

    public function getMetadata(): array {
        return $this->metadata;
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

}

class BlockText extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
    }

    public function getField(): String {
        return $this->field;
    }

}

class BlockColor extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
    }

    public function getField(): String {
        return $this->field;
    }

}

class BlockMathNumber extends Block {

    protected int $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = intval($data['field']);
    }

    public function getField(): int {
        return $this->field;
    }

}

class BlockLogicBoolean extends Block {

    protected bool $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = boolval($data['field']);
    }

    public function getField(): bool {
        return $this->field;
    }

}

class BlockGlobalDeclaration extends Block {

    protected $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
    }

    public function getField() {
        return $this->field;
    }

}

class BlockControlsIf extends Block {

    protected ?Block $ifCondition = null;
    protected ?Block $ifCode = null;
    protected ?Block $elseifCondition = null;
    protected ?Block $elseifCode = null;
    protected ?Block $elseCode = null;

    protected bool $hasElseif;
    protected bool $hasElse;

    function __construct($data) {
        parent::__construct($data);
        $this->hasElseif = isset($data['elseif']);
        $this->hasElse = isset($data['else']);
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

    public function getIfCode(): ?Block {
        return $this->ifCode;
    }

    public function getElseifCondition(): ?Block {
        return $this->elseifCondition;
    }

    public function getElseifCode(): ?Block {
        return $this->elseifCode;
    }

    public function getElseCode(): ?Block {
        return $this->elseCode;
    }

    public function setIfCondition(Block $block) {
        $this->ifCondition = $block;
    }

    public function setElseifCondition(Block $block) {
        $this->elseifCondition = $block;
    }

    public function setIfCode(Block $block) {
        $this->ifCode = $block;
    }

    public function setElseifCode(Block $block) {
        $this->elseifCode = $block;
    }

    public function setElseCode(Block $block) {
        $this->elseCode = $block;
    }

}
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

    protected function evaluateEcho(String $str) {
        echo $str;
    }

    public function evaluate() {
        $this->evaluateEcho($this->interpreterText);
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

class BlockComponentMethod extends Block {

    protected String $componentType;
    protected String $methodName;
    protected String $instanceName;

    function __construct($data) {
        parent::__construct($data);
        $this->componentType = $data['component_type'];
        $this->methodName = $data['method_name'];
        $this->instanceName = $data['instance_name'];
        $this->interpreterText = "Method <b>" . $this->methodName . "</b> of instance <b>" . $this->instanceName . "</b> fired<br>";
    }

    public function getComponentType(): String {
        return $this->componentType;
    }

    public function getMethodName(): String {
        return $this->methodName;
    }

    public function getInstanceName(): String {
        return $this->instanceName;
    }

    public function evaluate() {
        parent::evaluate();
        $args = null;
        foreach ($this->child as $child) {
            $args[] = $child->evaluate();
        }
        $component = $this->project->getComponentByName($this->instanceName);
        if (method_exists($component, $this->methodName)) {
            call_user_func_array(array($component, $this->methodName), $args);
        } else {
            $this->evaluateEcho("<text style=\"color: yellow\">Unsupported method <b>" . $this->methodName . "</b> of component <b>" . $this->componentType . "</b></text><br>");
        }
    }
}

class BlockProceduresDefnoreturn extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Starting defined function <b>" . $this->field . "</b><br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        foreach ($this->child as $child) {
            $child->evaluate();
        }
    }
}

class BlockProceduresDefreturn extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Starting defined function <b>" . $this->field . "</b><br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate(): mixed {
        parent::evaluate();
        return $this->child[0]->evaluate();
    }
}

class BlockProceduresCallnoreturn extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Calling defined function <b>" . $this->field . "</b><br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        return $this->project->getDefinedFunction($this->field)->evaluate();
    }
}

class BlockProceduresCallreturn extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Calling defined function <b>" . $this->field . "</b><br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        return $this->project->getDefinedFunction($this->field)->evaluate();
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

class BlockComponentComponentBlock extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "<b>" . $this->field . "</b> (component instance)<br>";
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

class BlockText extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = (!is_array($data['field']) ? $data['field'] : "");
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

class BlockTextJoin extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "Joining text<br>";
    }

    public function evaluate() {
        parent::evaluate();
        return $this->child[0]->evaluate() . $this->child[1]->evaluate();
    }
}

class BlockTextLength extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "Text length<br>";
    }

    public function evaluate() {
        parent::evaluate();
        return strlen($this->child[0]->evaluate());
    }
}

class BlockTextStartsAt extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "Text starts at<br>";
    }

    public function evaluate() {
        parent::evaluate();
        $position = strpos($this->child[0]->evaluate(), $this->child[1]->evaluate());
        if ($position === false) {
            $position = 0;
        } else {
            $position += 1;
        }

        return $position;
    }
}

class BlockTextContains extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Text contains<br>";
    }

    public function evaluate() {
        parent::evaluate();
        if($this->field == "CONTAINS") return str_contains($this->child[0]->evaluate(), $this->child[1]->evaluate());
        $this->evaluateEcho("<div style=\"color:yellow\">BlockTextContains unimplemented mode (" . $this->field . "). Returning false</div>");
        return false;
    }
}

class BlockTextReplaceAll extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "Text replace all<br>";
    }

    public function evaluate() {
        parent::evaluate();
        return str_replace($this->child[1]->evaluate(), $this->child[2]->evaluate(), $this->child[0]->evaluate());
    }
}

class BlockTextReverse extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "Text reverse<br>";
    }

    public function evaluate() {
        parent::evaluate();
        return strrev($this->child[0]->evaluate());
    }
}

class BlockTextChangeCase extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Text change case to " . $this->field . "<br>";
    }

    public function evaluate() {
        parent::evaluate();
        if ($this->field == "UPCASE") return strtoupper($this->child[0]->evaluate());
        else return strtolower($this->child[0]->evaluate());
    }
}

class BlockTextCompare extends Block {

    protected String $field;
    protected array $map = ['GT' => '>', 'LT' => '<', 'EQUAL' => '=', 'NEQ' => '!='];

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Text compare " . $this->field . "<br>";
    }

    public function evaluate(): int {
        parent::evaluate();
        switch ($this->map[$this->field]) {
            case '<':
                return strnatcmp($this->child[0]->evaluate(), $this->child[1]->evaluate()) == -1;
            case '>':
                return strnatcmp($this->child[0]->evaluate(), $this->child[1]->evaluate()) == 1;
            case '=':
                return strnatcmp($this->child[0]->evaluate(), $this->child[1]->evaluate()) == 0;
            case '!=':
                return strnatcmp($this->child[0]->evaluate(), $this->child[1]->evaluate()) != 0;
        }
        return false;
    }
}

class BlockTextIsEmpty extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "Is text empty?<br>";
    }

    public function evaluate() {
        parent::evaluate();
        return empty($this->child[0]->evaluate());
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

    protected mixed $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "<b>" . $this->field . "</b> (number)<br>";
    }

    public function getField(): int {
        return $this->field;
    }

    public function evaluate(): mixed {
        parent::evaluate();
        return $this->field;
    }
}

class BlockMathAdd extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "<b>Math addition</b>:<br>";
    }

    public function evaluate(): int {
        parent::evaluate();
        $result = $this->child[0]->evaluate() + $this->child[1]->evaluate();
        return $result;
    }
}

class BlockMathSubtract extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "<b>Math subtract</b>:<br>";
    }

    public function evaluate(): int {
        parent::evaluate();
        $result = $this->child[0]->evaluate() - $this->child[1]->evaluate();
        return $result;
    }
}

class BlockMathMultiply extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "<b>Math multiply</b>:<br>";
    }

    public function evaluate(): int {
        parent::evaluate();
        $result = $this->child[0]->evaluate() * $this->child[1]->evaluate();
        return $result;
    }
}

class BlockMathPower extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "<b>Math power</b>:<br>";
    }

    public function evaluate(): int {
        parent::evaluate();
        $result = pow($this->child[0]->evaluate(), $this->child[1]->evaluate());
        return $result;
    }
}

class BlockMathBitwise extends Block {

    protected String $field;
    protected array $map = ['BITAND' => '&', 'BITIOR' => '|', 'BITXOR' => '^'];

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Math bitwise <b>" . $this->map[$this->field] . "</b><br>";
    }

    public function evaluate(): int {
        parent::evaluate();
        switch ($this->map[$this->field]) {
            case '&':
                return $this->child[0]->evaluate() & $this->child[1]->evaluate();
            case '|':
                return $this->child[0]->evaluate() | $this->child[1]->evaluate();
            case '^':
                return $this->child[0]->evaluate() ^ $this->child[1]->evaluate();
        }
        return null;
    }
}

class BlockMathCompare extends Block {

    protected String $field;
    protected array $map = ['GT' => '>', 'LT' => '<', 'LTE' => '<=', 'GTE' => '>=', 'EQ' => '=', 'NEQ' => '!='];

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Comparing <b>" . $this->map[$this->field] . "</b><br>";
    }

    public function evaluate(): bool {
        parent::evaluate();
        switch ($this->map[$this->field]) {
            case '>':
                return $this->child[0]->evaluate() > $this->child[1]->evaluate();
            case '<':
                return $this->child[0]->evaluate() < $this->child[1]->evaluate();
            case '=':
                return $this->child[0]->evaluate() == $this->child[1]->evaluate();
            case '!=':
                return $this->child[0]->evaluate() != $this->child[1]->evaluate();
            case '<=':
                return $this->child[0]->evaluate() <= $this->child[1]->evaluate();
            case '>=':
                return $this->child[0]->evaluate() >= $this->child[1]->evaluate();
        }
        return false;
    }
}

class BlockMathSingle extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        switch ($this->field) {
            case 'ROOT':
                return sqrt($this->child[0]->evaluate());
            case 'ABS':
                return abs($this->child[0]->evaluate());
            case 'NEG':
                return -$this->child[0]->evaluate();
            case 'LN':
                return log($this->child[0]->evaluate());
            case 'EXP':
                return exp($this->child[0]->evaluate());
            case 'ROUND':
                return round($this->child[0]->evaluate());
            case 'CEILING':
                return ceil($this->child[0]->evaluate());
            case 'FLOOR':
                return floor($this->child[0]->evaluate());
        }
        return null;
    }
}

class BlockMathDivision extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText =  "Math division:<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        return $this->child[0]->evaluate() / $this->child[1]->evaluate();
    }
}

class BlockMathDivide extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        switch ($this->field) {
            case 'MODULO':
            case 'REMAINDER':
                return $this->child[0]->evaluate() % $this->child[1]->evaluate();
            case 'QUOTIENT':
                return intdiv($this->child[0]->evaluate(), $this->child[1]->evaluate());
        }
        return null;
    }
}

class BlockMathConvertAngles extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        switch ($this->field) {
            case 'RADIANS_TO_DEGREES':
                return rad2deg($this->child[0]->evaluate());
            case 'DEGREES_TO_RADIANS':
                return deg2rad($this->child[0]->evaluate());
        }
        return null;
    }
}

class BlockMathIsNumber extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText =  "Is " . $this->field . "?<br>";
    }

    public function evaluate(): bool {
        parent::evaluate();
        switch ($this->field) {
            case 'NUMBER':
            case 'BASE10':
                return is_numeric($this->child[0]->evaluate());
            case 'HEXADECIMAL':
                return preg_match('/^[0-9a-fA-F]+$/', $this->child[0]->evaluate()) === 1;
            case 'BINARY':
                return preg_match('/^[01]+$/', $this->child[0]->evaluate()) === 1;
        }
        return false;
    }
}

class BlockMathConvertNumber extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText =  $this->field . "<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        switch ($this->field) {
            case 'DEC_TO_HEX':
                return dechex($this->child[0]->evaluate());
            case 'HEX_TO_DEC':
                return hexdec($this->child[0]->evaluate());
            case 'DEC_TO_BIN':
                return decbin($this->child[0]->evaluate());
            case 'BIN_TO_DEC':
                return bindec($this->child[0]->evaluate());
        }
        return null;
    }
}

class BlockMathNumberRadix extends Block {

    protected String $fieldType;
    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->fieldType = $data['field'][0];
        $this->field = $data['field'][1];
        $this->interpreterText =  $this->field . " (" . $this->fieldType . ")<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        return $this->field;
    }
}

class BlockMathFormatAsDecimal extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText =  "Format as decimal:<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        return number_format($this->child[0]->evaluate(), $this->child[1]->evaluate(), '.', '');;
    }
}

class BlockMathAtan2 extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "ATAN2<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        return atan2($this->child[0]->evaluate(), $this->child[1]->evaluate());
    }
}

class BlockMathTrig extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        switch ($this->field) {
            case 'SIN':
                return sin($this->child[0]->evaluate());
            case 'COS':
                return cos($this->child[0]->evaluate());
            case 'TAN':
                return tan($this->child[0]->evaluate());
            case 'ASIN':
                return asin($this->child[0]->evaluate());
            case 'ACOS':
                return acos($this->child[0]->evaluate());
            case 'ATAN':
                return atan($this->child[0]->evaluate());
        }
        return null;
    }
}

class BlockMathOnList extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        parent::evaluate();
        switch ($this->field) {
            case 'MIN':
                return min($this->child[0]->evaluate(), $this->child[1]->evaluate());
            case 'MAX':
                return max($this->child[0]->evaluate(), $this->child[1]->evaluate());
        }
        return null;
    }
}

class BlockMathRandomInt extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "<b>Random int</b>:<br>";
    }

    public function evaluate(): int {
        parent::evaluate();
        $result = random_int($this->child[0]->evaluate(), $this->child[1]->evaluate());
        $this->evaluateEcho("Generated number: <b>" . $result . "</b><br>");
        return $result;
    }
}

class BlockMathRandomFloat extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "<b>Random float</b>:<br>";
    }

    public function evaluate(): float {
        parent::evaluate();
        $result = mt_rand() / mt_getrandmax();
        $this->evaluateEcho("Generated number: <b>" . $result . "</b><br>");
        return $result;
    }
}

class BlockLogicBoolean extends Block {

    protected bool $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = ($data['field'] == "FALSE" ? false : true);
        $this->interpreterText = "<b>" . $this->field . "</b> (bool)<br>";
    }

    public function getField(): bool {
        return $this->field;
    }

    public function evaluate() {
        parent::evaluate();
        return $this->field;
    }
}

class BlockLogicFalse extends Block {

    protected bool $field;

    function __construct($data) {
        parent::__construct($data);
        $this->field = ($data['field'] == "FALSE" ? false : true);
        $this->interpreterText = "<b>" . $this->field . "</b> (bool)<br>";
    }

    public function getField(): bool {
        return $this->field;
    }

    public function evaluate(): bool {
        parent::evaluate();
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
        return !$this->child[0]->evaluate();
    }
}

class BlockLogicOr extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "<b>||</b><br>";
    }

    public function evaluate() {
        parent::evaluate();
        return $this->child[0]->evaluate() || $this->child[1]->evaluate();
    }
}

class BlockLogicCompare extends Block {

    protected String $field;
    protected array $map = ['EQ' => '=', 'NEQ' => '!='];

    function __construct($data) {
        parent::__construct($data);
        $this->field = $data['field'];
        $this->interpreterText = "Comparing <b>" . $this->map[$this->field] . "</b><br>";
    }

    public function evaluate(): bool {
        parent::evaluate();
        switch ($this->map[$this->field]) {
            case '=':
                return $this->child[0]->evaluate() == $this->child[1]->evaluate();
            case '!=':
                return $this->child[0]->evaluate() != $this->child[1]->evaluate();
        }
        return false;
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
        $this->evaluateEcho("Variable <b>" . $this->field . "</b> set to <b>" . $this->project->getVariable($this->field) . "</b><br>");
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
        $this->evaluateEcho("<b>" . $this->project->getVariable($this->field) . "</b><br>");
        return $this->project->getVariable($this->field);
    }
}

class BlockLocalDeclarationStatement extends Block {

    protected $field;

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
        $this->evaluateEcho("Variable <b>" . $this->field . "</b> set to <b>" . $this->project->getVariable($this->field) . "</b><br>");
        if(count($this->child) > 1) {
            for($i = 1; $i < count($this->child); $i++) {
                $this->child[$i]->evaluate();
            }
        }
        $this->project->removeVariable($this->field);
        $this->evaluateEcho("Variable <b>" . $this->field . "</b> cleared<br>");
    }
}

class BlockLocalDeclarationExpression extends Block {

    protected $field;

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
        $this->evaluateEcho("Variable <b>" . $this->field . "</b> set to <b>" . $this->project->getVariable($this->field) . "</b><br>");
        $result = $this->child[1]->evaluate();
        $this->project->removeVariable($this->field);
        $this->evaluateEcho("Variable <b>" . $this->field . "</b> cleared<br>");
        return $result;
    }
}

class BlockControlsDoThenReturn extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "Evaluating <b>do then return</b><br>";
    }

    public function evaluate() {
        parent::evaluate();
        for($i = 1; $i < count($this->child); $i++) {
            $this->child[$i]->evaluate();
        }
        $this->evaluateEcho("Evaluation <b>do then return</b> returning ");
        $result = $this->child[0]->evaluate();
        $this->evaluateEcho("Evaluation <b>do then return</b> done<br>");
        return $result;
    }

}

class BlockControlsForRange extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->interpreterText = "Evaluating <b>foreach</b><br>";
    }

    public function evaluate() {
        parent::evaluate();
        $start = $this->child[0]->evaluate();
        $end = $this->child[1]->evaluate();
        $step = $this->child[2]->evaluate();
        for ($index = $start; $index <= $end; $index = $index + $step) {
            if(count($this->child) <= 3) break;
            for($i = 3; $i < count($this->child); $i++) {
                $this->child[$i]->evaluate();
            }
        }
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
        $this->evaluateEcho("Controls contains if " . ($this->hasElseif ? "& elseif " : "") . ($this->hasElse ? "& else " : "") . "<br>");
        $this->evaluateEcho("Testing <b>IF</b> statement<br>");
        $lastResult = false;

        if ($lastResult = $this->ifCondition->evaluate()) {
            $this->evaluateEcho("Condition <b>IF</b> is <b>TRUE</b>. Evaluating <b>IF</b> code<br>");
            foreach ($this->code['if'] as $child) {
                $child->evaluate();
            }
        } else {
            $this->evaluateEcho("Condition <b>IF</b> is <b>FALSE</b><br>");
        }

        if ($lastResult == false && $this->hasElseif) {
            $this->evaluateEcho("Testing <b>ELSE IF</b> statement<br>");
            if ($lastResult = $this->elseifCondition->evaluate()) {
                $this->evaluateEcho("Condition <b>ELSE IF</b> is <b>TRUE</b>. Evaluating <b>ELSE IF</b> code<br>");
                foreach ($this->code['elseif'] as $child) {
                    $child->evaluate();
                }
            } else {
                $this->evaluateEcho("Condition <b>ELSE IF</b> is <b>FALSE</b><br>");
            }
        }

        if ($lastResult == false && $this->hasElse) {
            $this->evaluateEcho("Evaluating <b>ELSE</b> code<br>");
            foreach ($this->code['else'] as $child) {
                $child->evaluate();
            }
        }
    }
}

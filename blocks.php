<?php

class Block {

    const BLOCK_ALIASES = [
        "Undefined block",
        "Event",
        "Method",
        "Void procedure definition",
        "No-void procedure definition",
        "Call void procedure",
        "Call no-void procedure",
        "Setter/Getter",
        "Component field",
        "Text",
        "Text join",
        "Text length",
        "Text starts at",
        "Text contains",
        "Text replace all",
        "Text reverse",
        "Text change case",
        "Text compare",
        "Text is empty",
        "Color",
        "Make color",
        "Number",
        "Math add",
        "Math subtract",
        "Math multiply",
        "Math power",
        "Math bitwise",
        "Math compare",
        "Math function (ROOT/ABS/NEG/LN/...)",
        "Math division (classic)",
        "Math division (modulo/remainder/quotient)",
        "Math angle convert",
        "Math is number",
        "Math convert number",
        "Math radix",
        "Math format to decimal",
        "Math atan2",
        "Math trigonometry",
        "Math min/max",
        "Math random int",
        "Math random float",
        "Logic boolean",
        "Logic operation",
        "Logic false",
        "Logic negate",
        "Logic or",
        "Logic compare",
        "Variable global declaration",
        "Variable set",
        "Variable get",
        "Variable local declaration (statement)",
        "Variable local declaration (expression)",
        "Controls do then return",
        "Controls for range",
        "Controls if",
        "List create",
        "List add items",
    ];

    protected ?Project $project = null;
    protected String $id;
    protected String $type;
    protected ?Block $parent = null;
    protected String $screen;
    protected int $sequence;
    protected array $child;
    protected array $metadata;
    protected String $interpreterText;
    protected String $alias = "Undefined block";

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

    public function getAlias(): String {
        return $this->alias;
    }

    protected function evaluateEcho(String $str) {
        $this->project->addLog($str);
        //echo $str;
    }

    public function evaluate() {
        try {
            $this->evaluateEcho($this->interpreterText);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockComponentEvent extends Block {

    protected String $componentType;
    protected String $instanceName;
    protected String $eventName;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Event";
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
        try {
            parent::evaluate();
            foreach ($this->child as $child) {
                $child->evaluate();
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockComponentMethod extends Block {

    protected String $componentType;
    protected String $methodName;
    protected String $instanceName;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Method";
        $this->componentType = $data['component_type'];
        $this->methodName = $data['method_name'];
        $this->instanceName = isset($data['instance_name']) ? $data['instance_name'] : "";
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
        try {
            parent::evaluate();
            $args = null;
            foreach ($this->child as $child) {
                $args[] = $child->evaluate();
            }
            $component = $this->project->getComponentByName($this->instanceName);
            if (method_exists($component, $this->methodName)) {
                call_user_func_array(array($component, $this->methodName), $args);
            } else {
                $this->evaluateEcho("<text style=\"color: red\">Unsupported method <b>" . $this->methodName . "</b> of component <b>" . $this->componentType . "</b></text><br>");
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockProceduresDefnoreturn extends Block {

    protected String $field;
    protected array $vars;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Void procedure definition";
        $this->field = !is_array($data['field']) ? $data['field'] : $data['field'][0];
        $this->vars = !is_array($data['field']) ? array() : $this->fillVars($data['field']);
        $this->interpreterText = "Starting defined function <b>" . $this->field . "</b><br>";
    }

    private function fillVars($data) {
        unset($data[0]);
        return array_values($data);
    }

    private function hasMoreVariablesThanOne(): bool {
        return count($this->vars) > 1;
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate($args = array()) {
        try {
            parent::evaluate();

            if (count($this->vars) > 0) {
                $this->evaluateEcho("Declaring local variable" . ($this->hasMoreVariablesThanOne() ? "s" : "") . "<br>");
                for ($i = 0; $i < count($this->vars); $i++) {
                    $this->project->setVariable($this->vars[$i], $args[$i]);
                    $this->evaluateEcho("Local variable <b>" . $this->vars[$i] . "</b> set to <b>" . (is_array($this->project->getVariable($this->vars[$i])) ? "[" . implode(' ', $this->project->getVariable($this->vars[$i])) . "]" : $this->project->getVariable($this->vars[$i])) . "</b><br>");
                }
            }

            foreach ($this->child as $child) {
                $child->evaluate();
            }

            for ($i = 0; $i < count($this->vars); $i++) {
                $this->project->removeVariable($this->vars[$i]);
                $this->evaluateEcho("Variable <b>" . $this->vars[$i] . "</b> cleared<br>");
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockProceduresDefreturn extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "No-void procedure definition";
        $this->field = $data['field'];
        $this->interpreterText = "Starting defined function <b>" . $this->field . "</b><br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            return $this->child[0]->evaluate();
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockProceduresCallnoreturn extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Call void procedure";
        $this->field = $data['field'];
        $this->interpreterText = "Calling defined function <b>" . $this->field . "</b><br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $args = array();
            foreach ($this->child as $arg) {
                $args[] = $arg->evaluate();
            }
            $this->project->getDefinedFunction($this->field)->evaluate($args);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockProceduresCallreturn extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Call no-void procedure";
        $this->field = $data['field'];
        $this->interpreterText = "Calling defined function <b>" . $this->field . "</b><br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            return $this->project->getDefinedFunction($this->field)->evaluate();
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockComponentSetGet extends Block {

    protected String $componentType;
    protected String $setOrGet;
    protected ?String $propertyName;
    protected ?String $instanceName;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Setter/Getter";
        $this->componentType = $data['component_type'];
        $this->setOrGet = $data['set_or_get'];
        $this->propertyName = isset($data['property_name']) ? $data['property_name'] : null;
        $this->instanceName = isset($data['instance_name']) ? $data['instance_name'] : null;
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

    public function getPropertyName(): ?String {
        return $this->propertyName;
    }

    public function getInstanceName(): ?String {
        return $this->instanceName;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            if ($this->setOrGet === "get") {
                return $this->project->getComponentByName($this->instanceName)->getProperty($this->propertyName);
            } else {
                $this->project->getComponentByName($this->instanceName)->setProperty($this->propertyName, $this->child[0]->evaluate());
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockComponentComponentBlock extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Component field";
        $this->field = $data['field'];
        $this->interpreterText = "<b>" . $this->field . "</b> (component instance)<br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            foreach ($this->child as $child) {
                $child->evaluate();
            }
            return $this->field;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockText extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text";
        $this->field = (!is_array($data['field']) ? $data['field'] : "");
        $this->interpreterText = "[GET] <b>" . $this->field . "</b> (text)<br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            foreach ($this->child as $child) {
                $child->evaluate();
            }
            return $this->field;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextJoin extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text join";
        $this->interpreterText = "Joining text<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $result = "";
            foreach ($this->child as $child) {
                $result = $result . $child->evaluate();
            }
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextLength extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text length";
        $this->interpreterText = "Text length<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            return strlen($this->child[0]->evaluate());
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextStartsAt extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text starts at";
        $this->interpreterText = "Text starts at<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $position = strpos($this->child[0]->evaluate(), $this->child[1]->evaluate());
            if ($position === false) {
                $position = 0;
            } else {
                $position += 1;
            }

            return $position;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextContains extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text contains";
        $this->field = $data['field'];
        $this->interpreterText = "Text contains<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            if ($this->field == "CONTAINS") return str_contains($this->child[0]->evaluate(), $this->child[1]->evaluate());
            $this->evaluateEcho("<div style=\"color:yellow\">BlockTextContains unimplemented mode (" . $this->field . "). Returning false</div>");
            return false;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextReplaceAll extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text replace all";
        $this->interpreterText = "Text replace all<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            return str_replace($this->child[1]->evaluate(), $this->child[2]->evaluate(), $this->child[0]->evaluate());
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextReverse extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text reverse";
        $this->interpreterText = "Text reverse<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            return strrev($this->child[0]->evaluate());
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextChangeCase extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text change case";
        $this->field = $data['field'];
        $this->interpreterText = "Text change case to " . $this->field . "<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            if ($this->field == "UPCASE") return strtoupper($this->child[0]->evaluate());
            else return strtolower($this->child[0]->evaluate());
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextCompare extends Block {

    protected String $field;
    protected array $map = ['GT' => '>', 'LT' => '<', 'EQUAL' => '=', 'NEQ' => '!='];

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text compare";
        $this->field = $data['field'];
        $this->interpreterText = "Text compare " . $this->field . "<br>";
    }

    public function evaluate(): int {
        try {
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
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockTextIsEmpty extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Text is empty";
        $this->interpreterText = "Is text empty?<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            return empty($this->child[0]->evaluate());
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockColor extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Color";
        $this->field = $data['field'];
        $this->interpreterText = "<b>" . $this->field . "</b> (color)<br>";
    }

    public function getField(): String {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            foreach ($this->child as $child) {
                $child->evaluate();
            }
            return $this->field;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockColorMakeColor extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Make color";
        $this->interpreterText = "Making color<br>";
    }

    private function rgbToHex($red, $green, $blue) {
        return sprintf("#%02x%02x%02x", $red, $green, $blue);
    }

    private function rgbaToHex($red, $green, $blue, $alpha) {
        $alphaHex = dechex((int) round($alpha * 255));
        return sprintf("#%02x%02x%02x%02x", $red, $green, $blue, $alphaHex);
    }


    public function evaluate() {
        try {
            parent::evaluate();
            $list = $this->child[0]->evaluate();
            if (count($list) == 3) {
                return $this->rgbToHex($list[0], $list[1], $list[2]);
            } else if (count($list) == 4) {
                return $this->rgbaToHex($list[0], $list[1], $list[2], $list[3]);
            }
            return null;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathNumber extends Block {

    protected mixed $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Number";
        $this->field = $data['field'];
        $this->interpreterText = "[GET] <b>" . $this->field . "</b> (number)<br>";
    }

    public function getField(): int {
        return $this->field;
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            return $this->field;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathAdd extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math add";
        $this->interpreterText = "<b>Math addition</b>:<br>";
    }

    public function evaluate(): int {
        try {
            parent::evaluate();
            $result = $this->child[0]->evaluate();
            for ($i = 1; $i < count($this->child); $i++) {
                $result = $result + $this->child[$i]->evaluate();
            }
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathSubtract extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math subtract";
        $this->interpreterText = "<b>Math subtract</b>:<br>";
    }

    public function evaluate(): int {
        try {
            parent::evaluate();
            $result = $this->child[0]->evaluate() - $this->child[1]->evaluate();
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathMultiply extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math multiply";
        $this->interpreterText = "<b>Math multiply</b>:<br>";
    }

    public function evaluate(): int {
        try {
            parent::evaluate();
            $result = $this->child[0]->evaluate();
            for ($i = 1; $i < count($this->child); $i++) {
                $result = $result * $this->child[$i]->evaluate();
            }
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathPower extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math power";
        $this->interpreterText = "<b>Math power</b>:<br>";
    }

    public function evaluate(): int {
        try {
            parent::evaluate();
            $result = pow($this->child[0]->evaluate(), $this->child[1]->evaluate());
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathBitwise extends Block {

    protected String $field;
    protected array $map = ['BITAND' => '&', 'BITIOR' => '|', 'BITXOR' => '^'];

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math bitwise";
        $this->field = $data['field'];
        $this->interpreterText = "Math bitwise <b>" . $this->map[$this->field] . "</b><br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $result = $this->child[0]->evaluate();
            for ($i = 1; $i < count($this->child); $i++) {
                switch ($this->map[$this->field]) {
                    case '&':
                        $result &= $this->child[$i]->evaluate();
                        break;
                    case '|':
                        $result |= $this->child[$i]->evaluate();
                        break;
                    case '^':
                        $result ^= $this->child[$i]->evaluate();
                        break;
                }
            }
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathCompare extends Block {

    protected String $field;
    protected array $map = ['GT' => '>', 'LT' => '<', 'LTE' => '<=', 'GTE' => '>=', 'EQ' => '=', 'NEQ' => '!='];

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math compare";
        $this->field = $data['field'];
        $this->interpreterText = "Comparing <b>" . $this->map[$this->field] . "</b><br>";
    }

    public function evaluate(): bool {
        try {
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
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathSingle extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math function (ROOT/ABS/NEG/LN/...)";
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        try {
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
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathDivision extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math division (classic)";
        $this->interpreterText =  "Math division:<br>";
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            return $this->child[0]->evaluate() / $this->child[1]->evaluate();
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathDivide extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math division (modulo/quotient)";
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            switch ($this->field) {
                case 'MODULO':
                case 'REMAINDER':
                    return $this->child[0]->evaluate() % $this->child[1]->evaluate();
                case 'QUOTIENT':
                    return intdiv($this->child[0]->evaluate(), $this->child[1]->evaluate());
            }
            return null;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathConvertAngles extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math angle convert";
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            switch ($this->field) {
                case 'RADIANS_TO_DEGREES':
                    return rad2deg($this->child[0]->evaluate());
                case 'DEGREES_TO_RADIANS':
                    return deg2rad($this->child[0]->evaluate());
            }
            return null;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathIsNumber extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math is number";
        $this->field = $data['field'];
        $this->interpreterText =  "Is " . $this->field . "?<br>";
    }

    public function evaluate(): bool {
        try {
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
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathConvertNumber extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math convert number";
        $this->field = $data['field'];
        $this->interpreterText =  $this->field . "<br>";
    }

    public function evaluate(): mixed {
        try {
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
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathNumberRadix extends Block {

    protected String $fieldType;
    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math radix";
        $this->fieldType = $data['field'][0];
        $this->field = $data['field'][1];
        $this->interpreterText =  $this->field . " (" . $this->fieldType . ")<br>";
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            return $this->field;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathFormatAsDecimal extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math format to decimal";
        $this->interpreterText =  "Format as decimal:<br>";
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            return number_format($this->child[0]->evaluate(), $this->child[1]->evaluate(), '.', '');
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathAtan2 extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math atan2";
        $this->interpreterText = "ATAN2<br>";
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            return atan2($this->child[0]->evaluate(), $this->child[1]->evaluate());
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathTrig extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math trigonometry";
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        try {
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
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathOnList extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math min/max";
        $this->field = $data['field'];
        $this->interpreterText = $this->field . "<br>";
    }

    public function evaluate(): mixed {
        try {
            parent::evaluate();
            $array = array();
            foreach ($this->child as $child) {
                $array[] = $child->evaluate();
            }
            switch ($this->field) {
                case 'MIN':
                    return min($array);
                case 'MAX':
                    return max($array);
            }
            return null;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathRandomInt extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math random int";
        $this->interpreterText = "<b>Random int</b>:<br>";
    }

    public function evaluate(): int {
        try {
            parent::evaluate();
            $result = random_int($this->child[0]->evaluate(), $this->child[1]->evaluate());
            $this->evaluateEcho("Generated number: <b>" . $result . "</b><br>");
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockMathRandomFloat extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Math random float";
        $this->interpreterText = "<b>Random float</b>:<br>";
    }

    public function evaluate(): float {
        try {
            parent::evaluate();
            $result = mt_rand() / mt_getrandmax();
            $this->evaluateEcho("Generated number: <b>" . $result . "</b><br>");
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLogicBoolean extends Block {

    protected bool $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Logic boolean";
        $this->field = ($data['field'] == "FALSE" ? false : true);
        $this->interpreterText = "[GET] <b>" . $this->field . "</b> (bool)<br>";
    }

    public function getField(): bool {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            return $this->field;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLogicOperation extends Block {

    protected String $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Logic operation";
        $this->field = $data['field'];
        $this->interpreterText = "Logic operation <b>" . $this->field . "</b><br>";
    }

    public function getField(): bool {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $result = $this->child[0]->evaluate();
            for ($i = 1; $i < count($this->child); $i++) {
                if ($this->field == "AND") $result = $result && $this->child[$i]->evaluate();
                else $result = $result || $this->child[$i]->evaluate();
            }
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLogicFalse extends Block {

    protected bool $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Logic false";
        $this->field = ($data['field'] == "FALSE" ? false : true);
        $this->interpreterText = "<b>" . $this->field . "</b> (bool)<br>";
    }

    public function getField(): bool {
        return $this->field;
    }

    public function evaluate(): bool {
        try {
            parent::evaluate();
            return $this->field;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLogicNegate extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Logic negate";
        $this->interpreterText = "<b>!</b> ";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            return !$this->child[0]->evaluate();
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLogicOr extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Logic or";
        $this->interpreterText = "<b>||</b><br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            return $this->child[0]->evaluate() || $this->child[1]->evaluate();
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLogicCompare extends Block {

    protected String $field;
    protected array $map = ['EQ' => '=', 'NEQ' => '!='];

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Logic compare";
        $this->field = $data['field'];
        $this->interpreterText = "Comparing <b>" . $this->map[$this->field] . "</b><br>";
    }

    public function evaluate(): bool {
        try {
            parent::evaluate();
            switch ($this->map[$this->field]) {
                case '=':
                    return $this->child[0]->evaluate() == $this->child[1]->evaluate();
                case '!=':
                    return $this->child[0]->evaluate() != $this->child[1]->evaluate();
            }
            return false;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockGlobalDeclaration extends Block {

    protected $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Variable global declaration";
        $this->field = $data['field'];
        $this->interpreterText = "Declaring <b>global variable</b> key: <b>" . $this->field . "</b> value: ";
    }

    public function getField() {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $this->project->setVariable("global " . $this->field, $this->child[0]->evaluate());
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLexicalVariableSet extends Block {

    protected $field;
    protected bool $isGlobal;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Variable set";
        $this->field = $data['field'];
        $this->interpreterText = "Setting <b>variable</b> key: <b>" . $this->field . "</b> value: ";
    }

    public function getField() {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $this->project->setVariable($this->field, $this->child[0]->evaluate());
            $this->evaluateEcho("Variable <b>" . $this->field . "</b> set to <b>" . (is_array($this->project->getVariable($this->field)) ? "[" . implode(' ', $this->project->getVariable($this->field)) . "]" : $this->project->getVariable($this->field)) . "</b><br>");
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLexicalVariableGet extends Block {

    protected $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Variable get";
        $this->field = $data['field'];
        $this->interpreterText = "Getting <b>variable</b> key: <b>" . $this->field . "</b> value: ";
    }

    public function getField() {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $this->evaluateEcho("<b>" . (is_array($this->project->getVariable($this->field)) ? "[" . implode(' ', $this->project->getVariable($this->field)) . "]" : $this->project->getVariable($this->field)) . "</b><br>");
            return $this->project->getVariable($this->field);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLocalDeclarationStatement extends Block {

    protected array $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Variable local declaration (statement)";
        $this->field = is_array($data['field']) ? $data['field'] : array($data['field']);
        $this->interpreterText = "Declaring local variable" . ($this->hasMoreVariablesThanOne() ? "s" : "") . "<br>";
    }

    public function getField(): array {
        return $this->field;
    }

    public function hasMoreVariablesThanOne(): bool {
        return count($this->field) > 1;
    }

    public function evaluate() {
        try {
            parent::evaluate();

            for ($i = 0; $i < count($this->field); $i++) {
                $this->project->setVariable($this->field[$i], $this->child[$i]->evaluate());
                $this->evaluateEcho("Variable <b>" . $this->field[$i] . "</b> set to <b>" . (is_array($this->project->getVariable($this->field[$i])) ? "[" . implode(' ', $this->project->getVariable($this->field[$i])) . "]" : $this->project->getVariable($this->field[$i])) . "</b><br>");
            }

            for ($i = count($this->field); $i < count($this->child); $i++) {
                $this->child[$i]->evaluate();
            }

            for ($i = 0; $i < count($this->field); $i++) {
                $this->project->removeVariable($this->field[$i]);
                $this->evaluateEcho("Variable <b>" . $this->field[$i] . "</b> cleared<br>");
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockLocalDeclarationExpression extends Block {

    protected array $field;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Variable local declaration (expression)";
        $this->field = is_array($data['field']) ? $data['field'] : array($data['field']);
        $this->interpreterText = "Declaring local variable" . ($this->hasMoreVariablesThanOne() ? "s" : "") . "<br>";
    }

    public function hasMoreVariablesThanOne(): bool {
        return count($this->field) > 1;
    }

    public function getField() {
        return $this->field;
    }

    public function evaluate() {
        try {
            parent::evaluate();

            for ($i = 0; $i < count($this->field); $i++) {
                $this->project->setVariable($this->field[$i], $this->child[$i]->evaluate());
                $this->evaluateEcho("Variable <b>" . $this->field[$i] . "</b> set to <b>" . $this->project->getVariable($this->field[$i]) . "</b><br>");
            }

            $result = $this->child[count($this->field)]->evaluate();

            for ($i = 0; $i < count($this->field); $i++) {
                $this->project->removeVariable($this->field[$i]);
                $this->evaluateEcho("Variable <b>" . $this->field[$i] . "</b> cleared<br>");
            }

            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockControlsDoThenReturn extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Controls do then return";
        $this->interpreterText = "Evaluating <b>do then return</b><br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            for ($i = 1; $i < count($this->child); $i++) {
                $this->child[$i]->evaluate();
            }
            $this->evaluateEcho("Evaluation <b>do then return</b> returning ");
            $result = $this->child[0]->evaluate();
            $this->evaluateEcho("Evaluation <b>do then return</b> done<br>");
            return $result;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockControlsForRange extends Block {

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Controls for range";
        $this->interpreterText = "Evaluating <b>foreach</b><br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $start = $this->child[0]->evaluate();
            $end = $this->child[1]->evaluate();
            $step = $this->child[2]->evaluate();
            for ($index = $start; $index <= $end; $index = $index + $step) {
                if (count($this->child) <= 3) break;
                for ($i = 3; $i < count($this->child); $i++) {
                    $this->child[$i]->evaluate();
                }
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockControlsIf extends Block {

    protected int $elseifCount = 0;
    protected int $elseCount = 0;
    protected array $conditionBlocks = array();
    protected array $codeBlocks = array();

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "Controls if";
        $this->elseifCount = isset($data['elseif']) ? $data['elseif'] : 0;
        $this->elseCount = isset($data['else']) ? $data['else'] : 0;
        $this->interpreterText = "Evaluating <b>if controls</b><br>";
    }

    public function getElseifCount(): int {
        return $this->elseifCount;
    }

    public function getElseCount(): int {
        return $this->elseCount;
    }

    public function addCondition(?Block $block): void {
        $this->conditionBlocks[] = $block;
    }

    public function getConditionBlocks(): array {
        return $this->conditionBlocks;
    }

    public function addCode(int $index, Block $block): void {
        if (!isset($this->codeBlocks[$index])) {
            $this->codeBlocks[$index] = array();
        }
        $this->codeBlocks[$index][] = $block;
    }

    public function getCodeBlocks(): array {
        return $this->codeBlocks;
    }

    public function evaluate() {
        try {
            parent::evaluate();

            $this->evaluateEcho("This control has 1x if, " . $this->elseifCount . "x elseif, " . $this->elseCount . "x else<br>");

            $lastResult = false;
            for ($i = 0; $i < count($this->conditionBlocks); $i++) {
                if ($lastResult == false) {
                    if ($this->conditionBlocks[$i] != null) {
                        $this->evaluateEcho("Testing <b>" . ($i == 0 ? "IF" : "ELSEIF") . "</b> condition<br>");
                        if ($lastResult = $this->conditionBlocks[$i]->evaluate()) {
                            $this->evaluateEcho("Condition is TRUE. Evaluating inner code<br>");
                            foreach ($this->codeBlocks[$i] as $child) {
                                $child->evaluate();
                            }
                        } else {
                            $this->evaluateEcho("Condition is FALSE<br>");
                        }
                    } else {
                        $this->evaluateEcho("Evaluating <b>ELSE</b> inner code<br>");
                        foreach ($this->codeBlocks[$i] as $child) {
                            $child->evaluate();
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockListsCreate extends Block {

    protected int $items;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "List create";
        $this->items = $data['items'];
        $this->interpreterText = "Creating list<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $list = array();
            foreach ($this->child as $child) {
                $list[] = $child->evaluate();
            }
            return $list;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

class BlockListsAddItems extends Block {

    protected int $items;

    function __construct($data) {
        parent::__construct($data);
        $this->alias = "List add items";
        $this->items = $data['items'];
        $this->interpreterText = "Adding items to list<br>";
    }

    public function evaluate() {
        try {
            parent::evaluate();
            $list = $this->child[0]->evaluate();
            if (count($this->child) > 1) {
                for ($i = 1; $i < count($this->child); $i++) {
                    $list[] = $this->child[$i]->evaluate();
                }
            }

            if (str_starts_with($this->child[0]->getType(), "lexical_variable")) {
                $this->project->setVariable($this->child[0]->getField(), $list);
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}

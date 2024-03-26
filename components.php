<?php

enum ComponentType: String {

    case BaseComponent = "Base";
    case Button = "Button";
    case Image = "Image";
    case Label = "Label";
    case CheckBox = "CheckBox";
    case Switch = "Switch";
    case TextBox = "TextBox";
    case Slider = "Slider";
    case PasswordTextBox = "PasswordTextBox";

    static function getComponentTypeByString($typeName) {
        foreach (ComponentType::cases() as $case) {
            if ($case->value === $typeName) {
                return $case;
            }
        }
        return ComponentType::BaseComponent;
    }
}

interface IEnableable {
    public function isEnabled();
}

interface IClickable {
    public function isClickable();
}

class BaseComponent {

    protected String $name;
    //protected ComponentType $type;
    protected String $type;
    protected String $version;
    protected String $uuid;
    protected bool $visible;
    protected String $screen;

    function __construct($data) {
        $this->name = $data['$Name'];
        $this->type = $data['$Type']; //ComponentType::getComponentTypeByString($data['$Type']);
        $this->version = $data['$Version'];
        $this->uuid = $data['Uuid'];
        $this->visible = !isset($data['Visible']);
        $this->screen = $data['screen'];
    }

    public function getName(): String {
        return $this->name;
    }

    public function getType(): String {
        return $this->type;
    }

    /*
    public function getType(): ComponentType {
        return $this->type;
    }*/

    public function getVersion(): String {
        return $this->version;
    }

    public function getUuid(): String {
        return $this->uuid;
    }

    public function isVisible(): bool {
        return $this->visible;
    }

    public function getScreen(): String {
        return $this->screen;
    }
}

class ComponentButton extends BaseComponent implements IEnableable {

    protected ?String $text;
    protected bool $enabled;

    function __construct($data) {
        parent::__construct($data);
        $this->text = isset($data['Text']) ? $data['Text'] : null;
        $this->enabled = !isset($data['Enabled']);
    }

    public function getText(): String {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
}

class ComponentImage extends BaseComponent implements IEnableable, IClickable {

    protected ?String $picture;
    protected bool $enabled;
    protected bool $clickable;

    function __construct($data) {
        parent::__construct($data);
        $this->picture = isset($data['Picture']) ? $data['Picture'] : null;
        $this->enabled = !isset($data['Enabled']);
        $this->clickable = isset($data['Clickable']);
    }

    public function getPicture(): String {
        return $this->picture;
    }

    public function setPicture($picture) {
        $this->picture;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    public function isClickable(): bool {
        return $this->clickable;
    }

    public function setClickable($clickable) {
        $this->clickable = $clickable;
    }
}

class ComponentLabel extends BaseComponent {

    protected ?String $text;

    function __construct($data) {
        parent::__construct($data);
        $this->text = isset($data['Text']) ? $data['Text'] : null;
    }

    public function getText(): String {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }
}

class ComponentCheckBox extends BaseComponent implements IEnableable {

    protected bool $checked;
    protected bool $enabled;

    function __construct($data) {
        parent::__construct($data);
        $this->checked = isset($data['Checked']);
        $this->enabled = !isset($data['Enabled']);
    }

    public function isChecked(): bool {
        return $this->checked;
    }

    public function setChecked($checked) {
        $this->checked = $checked;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
}

class ComponentSwitch extends BaseComponent implements IEnableable {

    protected bool $on;
    protected bool $enabled;

    function __construct($data) {
        parent::__construct($data);
        $this->on = isset($data['On']);
        $this->enabled = !isset($data['Enabled']);
    }

    public function isOn(): bool {
        return $this->on;
    }

    public function setOn($on) {
        $this->on = $on;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
}

class ComponentTextBox extends BaseComponent implements IEnableable {

    protected ?String $text;
    protected bool $multiline;
    protected bool $numbersonly;
    protected bool $readonly;
    protected bool $enabled;

    function __construct($data) {
        parent::__construct($data);
        $this->text = isset($data['Text']) ? $data['Text'] : null;
        $this->multiline = isset($data['MultiLine']);
        $this->numbersonly = isset($data['NumbersOnly']);
        $this->readonly = isset($data['ReadOnly']);
        $this->enabled = !isset($data['Enabled']);
    }

    public function getText(): String {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function isMultiLine(): bool {
        return $this->multiline;
    }

    public function setMultiLine($multiline) {
        $this->multiline = $multiline;
    }

    public function isNumbersOnly(): bool {
        return $this->numbersonly;
    }

    public function setNumbersOnly($numbersonly) {
        $this->numbersonly = $numbersonly;
    }

    public function isReadOnly(): bool {
        return $this->readonly;
    }

    public function setReadOnly($readonly) {
        $this->readonly = $readonly;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
}

class ComponentSlider extends BaseComponent {

    protected float $maxValue;
    protected float $minValue;
    protected float $thumbPosition;
    protected bool $thumbEnabled;

    function __construct($data) {
        parent::__construct($data);
        $this->maxValue = isset($data['MaxValue']) ? floatval($data['MaxValue']) : 50.0;
        $this->minValue = isset($data['MinValue']) ? floatval($data['MinValue']) : 10.0;
        $this->thumbPosition = isset($data['ThumbPosition']) ? floatval($data['ThumbPosition']) : 30.0;
        $this->thumbEnabled = isset($data['ThumbEnabled']);
    }

    public function getMaxValue(): float {
        return $this->maxValue;
    }

    public function setMaxValue($maxValue) {
        $this->maxValue = $maxValue;
    }

    public function getMinValue(): float {
        return $this->minValue;
    }

    public function setMinValue($minValue) {
        $this->minValue = $minValue;
    }

    public function getThumbPosition(): float {
        return $this->thumbPosition;
    }

    public function setThumbPosition($thumbPosition) {
        $this->thumbPosition = $thumbPosition;
    }

    public function isThumbEnabled(): bool {
        return $this->thumbEnabled;
    }

    public function setThumbEnabled($thumbEnabled) {
        $this->thumbEnabled = $thumbEnabled;
    }
}

class ComponentPasswordTextBox extends BaseComponent implements IEnableable {

    protected ?String $text;
    protected bool $numbersonly;
    protected bool $enabled;

    function __construct($data) {
        parent::__construct($data);
        $this->text = isset($data['Text']) ? $data['Text'] : null;
        $this->numbersonly = isset($data['NumbersOnly']);
        $this->enabled = !isset($data['Enabled']);
    }

    public function getText(): String {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function isNumbersOnly(): bool {
        return $this->numbersonly;
    }

    public function setNumbersOnly($numbersonly) {
        $this->numbersonly = $numbersonly;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
}

class ComponentImageSprite extends BaseComponent implements IEnableable {

    protected int $heading;
    protected int $height;
    protected int $width;
    protected int $interval;
    protected ?String $picture;
    protected bool $rotates;
    protected float $speed;
    protected int $x;
    protected int $y;
    protected bool $enabled;

    function __construct($data) {
        parent::__construct($data);
        $this->heading = (isset($data['Heading']) ? intval($data['Heading']) : 0);
        $this->height = intval($data['Height']);
        $this->width = intval($data['Width']);
        $this->interval = (isset($data['Interval']) ? intval($data['Interval']) : 100);
        $this->picture = (isset($data['Picture']) ? $data['Picture'] : null);
        $this->rotates = (isset($data['Rotates']) ? boolval($data['Rotates']) : true);
        $this->speed = (isset($data['Speed']) ? floatval($data['Speed']) : 0);
        $this->x = intval($data['X']);
        $this->y = intval($data['Y']);
        $this->enabled = !isset($data['Enabled']);
    }

    public function getHeading(): int {
        return $this->heading;
    }

    public function getHeight(): int {
        return $this->height;
    }

    public function getWidth(): int {
        return $this->width;
    }

    public function getInterval(): int {
        return $this->interval;
    }

    public function getPicture(): ?string {
        return $this->picture;
    }

    public function getRotates(): bool {
        return $this->rotates;
    }

    public function getSpeed(): float {
        return $this->speed;
    }

    public function getX(): int {
        return $this->x;
    }

    public function getY(): int {
        return $this->y;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    public function setHeading(int $heading): void {
        $this->heading = $heading;
    }

    public function setHeight(int $height): void {
        $this->height = $height;
    }

    public function setWidth(int $width): void {
        $this->width = $width;
    }

    public function setInterval(int $interval): void {
        $this->interval = $interval;
    }

    public function setPicture(?string $picture): void {
        $this->picture = $picture;
    }

    public function setRotates(bool $rotates): void {
        $this->rotates = $rotates;
    }

    public function setSpeed(int $speed): void {
        $this->speed = $speed;
    }

    public function setX(int $x): void {
        $this->x = $x;
    }

    public function setY(int $y): void {
        $this->y = $y;
    }
}

class ComponentCanvas extends BaseComponent {
    protected String $backgroundColor;
    protected ?String $backgroundImage;
    protected int $height;
    protected int $width;

    function __construct($data) {
        parent::__construct($data);
        $this->backgroundColor = (isset($data['BackgroundColor']) ? $data['BackgroundColor'] : "#ffffff");
        $this->backgroundImage = isset($data['BackgroundImage']) ? $data['BackgroundImage'] : null;
        $this->height = intval($data['Height']);
        $this->width = intval($data['Width']);
    }

    public function getBackgroundColor(): String {
        return $this->backgroundColor;
    }

    public function getBackgroundImage(): ?String {
        return $this->backgroundImage;
    }

    public function getHeight(): int {
        return $this->height;
    }

    public function getWidth(): int {
        return $this->width;
    }
}

class ComponentNotifier extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }
}

class ComponentSound extends BaseComponent {
    protected int $minimumInterval;
    protected String $source;

    function __construct($data) {
        parent::__construct($data);
        $this->minimumInterval = (isset($data['MinimumInterval']) ? intval($data['MinimumInterval']) : 500);
        $this->source = $data['Source'];
    }

    public function getMinimumInterval(): int {
        return $this->minimumInterval;
    }

    public function getSource(): String {
        return $this->source;
    }
}

class ComponentPlayer extends BaseComponent {
    protected String $source;

    function __construct($data) {
        parent::__construct($data);
        $this->source = $data['Source'];
    }

    public function getSource(): String {
        return $this->source;
    }
}

class ComponentAccelerometerSensor extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }
}

class ComponentClock extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }
}

class ComponentHorizontalArrangement extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }
}

class ComponentVerticalArrangement extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }
}

class ComponentHorizontalScrollArrangement extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }
}

class ComponentListView extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }
}

class ComponentBarcodeScanner extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }
}

class ComponentHorizontalScrollHandler extends BaseComponent {
    protected bool $scrollBarEnabled;
    protected bool $userControl;
    protected bool $fadingEdgeEnabled;
    protected float $overScrollMode;

    function __construct($data) {
        parent::__construct($data);
        $this->scrollBarEnabled = (isset($data['ScrollBarEnabled']) ? boolval($data['ScrollBarEnabled']) : true);
        $this->userControl = (isset($data['UserControl']) ? boolval($data['UserControl']) : true);
        $this->fadingEdgeEnabled = (isset($data['FadingEdgeEnabled']) ? boolval($data['FadingEdgeEnabled']) : true);
        $this->overScrollMode = (isset($data['OverScrollMode']) ? floatval($data['OverScrollMode']) : 0);
    }

    public function isScrollBarEnabled(): bool {
        return $this->scrollBarEnabled;
    }

    public function setScrollBarEnabled(bool $scrollBarEnabled): void {
        $this->scrollBarEnabled = $scrollBarEnabled;
    }

    public function isUserControl(): bool {
        return $this->userControl;
    }

    public function setUserControl(bool $userControl): void {
        $this->userControl = $userControl;
    }

    public function isFadingEdgeEnabled(): bool {
        return $this->fadingEdgeEnabled;
    }

    public function setFadingEdgeEnabled(bool $fadingEdgeEnabled): void {
        $this->fadingEdgeEnabled = $fadingEdgeEnabled;
    }

    public function getOverScrollMode(): float {
        return $this->overScrollMode;
    }

    public function setOverScrollMode(float $overScrollMode): void {
        $this->overScrollMode = $overScrollMode;
    }
}

class ComponentVerticalScrollHandler extends BaseComponent {
    protected bool $scrollBarEnabled;
    protected bool $userControl;
    protected bool $fadingEdgeEnabled;
    protected float $overScrollMode;

    function __construct($data) {
        parent::__construct($data);
        $this->scrollBarEnabled = (isset($data['ScrollBarEnabled']) ? boolval($data['ScrollBarEnabled']) : true);
        $this->userControl = (isset($data['UserControl']) ? boolval($data['UserControl']) : true);
        $this->fadingEdgeEnabled = (isset($data['FadingEdgeEnabled']) ? boolval($data['FadingEdgeEnabled']) : true);
        $this->overScrollMode = (isset($data['OverScrollMode']) ? floatval($data['OverScrollMode']) : 0);
    }

    public function isScrollBarEnabled(): bool {
        return $this->scrollBarEnabled;
    }

    public function setScrollBarEnabled(bool $scrollBarEnabled): void {
        $this->scrollBarEnabled = $scrollBarEnabled;
    }

    public function isUserControl(): bool {
        return $this->userControl;
    }

    public function setUserControl(bool $userControl): void {
        $this->userControl = $userControl;
    }

    public function isFadingEdgeEnabled(): bool {
        return $this->fadingEdgeEnabled;
    }

    public function setFadingEdgeEnabled(bool $fadingEdgeEnabled): void {
        $this->fadingEdgeEnabled = $fadingEdgeEnabled;
    }

    public function getOverScrollMode(): float {
        return $this->overScrollMode;
    }

    public function setOverScrollMode(float $overScrollMode): void {
        $this->overScrollMode = $overScrollMode;
    }
}

class ComponentSpinner extends BaseComponent {

    protected array $elements;
    protected ?String $selection;

    function __construct($data) {
        parent::__construct($data);
        $this->elements = $elements = explode(', ', $data['ElementsFromString']);
        $this->selection = (isset($data['Selection']) ? $data['Selection'] : null);
    }

    public function getElements(): array {
        return $this->elements;
    }

    public function setElements($elements): void {
        $this->elements = $elements;
    }

    public function addElement($element): void {
        if (!in_array($element, $this->elements)) {
            $this->elements[] = $element;
        }
    }
    
    public function removeElement($element): void {
        if (($key = array_search($element, $this->elements)) !== false) {
            unset($this->elements[$key]);
            $this->elements = array_values($this->elements);
            if ($this->selection === $element) {
                $this->selection = null;
            }
        }
    }
    

    public function getSelection(): ?String {
        return $this->selection;
    }

    public function setSelection($selection): void {
        $this->selection = $selection;
    }
}
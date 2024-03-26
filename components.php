<?php

class BaseComponent {

    protected String $name;
    protected String $type;
    protected String $version;
    protected String $uuid;
    protected String $screen;
    protected array $properties = array();

    function __construct($data) {
        $this->name = $data['$Name'];
        $this->type = $data['$Type'];
        $this->version = $data['$Version'];
        $this->uuid = $data['Uuid'];
        $this->screen = $data['screen'];
        $this->properties['Visible'] = !isset($data['Visible']);
    }

    public function getName(): String {
        return $this->name;
    }

    public function getType(): String {
        return $this->type;
    }

    public function getVersion(): String {
        return $this->version;
    }

    public function getUuid(): String {
        return $this->uuid;
    }

    public function getScreen(): String {
        return $this->screen;
    }

    public function getProperty($property): mixed {
        return $this->properties[$property];
    }

    public function setProperty($property, $value): void {
        $this->properties[$property] = $value;
    }

    public function getProperties(): array {
        return $this->properties;
    }
}

class ComponentButton extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['Enabled'] = !isset($data['Enabled']);
        $this->properties['Text'] = isset($data['Text']) ? $data['Text'] : null;
    }
}

class ComponentImage extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['Picture'] = isset($data['Picture']) ? $data['Picture'] : null;
        $this->properties['Enabled'] = !isset($data['Enabled']);
        $this->properties['Clickable'] = isset($data['Clickable']);
    }
}

class ComponentLabel extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['Text'] = isset($data['Text']) ? $data['Text'] : null;
    }
}

class ComponentCheckBox extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['Checked'] = isset($data['Checked']);
        $this->properties['Enabled'] = !isset($data['Enabled']);
    }
}

class ComponentSwitch extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['On'] = isset($data['On']);
        $this->properties['Enabled'] = !isset($data['Enabled']);
    }
}

class ComponentTextBox extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['Text'] = isset($data['Text']) ? $data['Text'] : null;
        $this->properties['MultiLine'] = isset($data['MultiLine']);
        $this->properties['NumbersOnly'] = isset($data['NumbersOnly']);
        $this->properties['ReadOnly'] = isset($data['ReadOnly']);
        $this->properties['Enabled'] = !isset($data['Enabled']);
    }
}

class ComponentSlider extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['MaxValue'] = isset($data['MaxValue']) ? floatval($data['MaxValue']) : 50.0;
        $this->properties['MinValue'] = isset($data['MinValue']) ? floatval($data['MinValue']) : 10.0;
        $this->properties['ThumbPosition'] = isset($data['ThumbPosition']) ? floatval($data['ThumbPosition']) : 30.0;
        $this->properties['ThumbEnabled'] = isset($data['ThumbEnabled']);
    }
}

class ComponentPasswordTextBox extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['Text'] = isset($data['Text']) ? $data['Text'] : null;
        $this->properties['NumbersOnly'] = isset($data['NumbersOnly']);
        $this->properties['Enabled'] = !isset($data['Enabled']);
    }
}

class ComponentImageSprite extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['Heading'] = (isset($data['Heading']) ? intval($data['Heading']) : 0);
        $this->properties['Height'] = intval($data['Height']);
        $this->properties['Width'] = intval($data['Width']);
        $this->properties['Interval'] = (isset($data['Interval']) ? intval($data['Interval']) : 100);
        $this->properties['Picture'] = (isset($data['Picture']) ? $data['Picture'] : null);
        $this->properties['Rotates'] = (isset($data['Rotates']) ? boolval($data['Rotates']) : true);
        $this->properties['Speed'] = (isset($data['Speed']) ? floatval($data['Speed']) : 0);
        $this->properties['X'] = intval($data['X']);
        $this->properties['Y'] = intval($data['Y']);
        $this->properties['Enabled'] = !isset($data['Enabled']);
    }

    public function MoveTo($x, $y) {
        $this->setProperty("X", $x);
        $this->setProperty("Y", $y);
    }
}

class ComponentCanvas extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['BackgroundColor'] = (isset($data['BackgroundColor']) ? $data['BackgroundColor'] : "#ffffff");
        $this->properties['BackgroundImage'] = isset($data['BackgroundImage']) ? $data['BackgroundImage'] : null;
        $this->properties['Height'] = intval($data['Height']);
        $this->properties['Width'] = intval($data['Width']);
    }
}

class ComponentNotifier extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
    }

    /*public function ShowMessageDialog() {

    }*/
}

class ComponentSound extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['MinimumInterval'] = (isset($data['MinimumInterval']) ? intval($data['MinimumInterval']) : 500);
        $this->properties['Source'] = $data['Source'];
    }
}

class ComponentPlayer extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['Source'] = $data['Source'];
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
    function __construct($data) {
        parent::__construct($data);
        $this->properties['ScrollBarEnabled'] = (isset($data['ScrollBarEnabled']) ? boolval($data['ScrollBarEnabled']) : true);
        $this->properties['UserControl'] = (isset($data['UserControl']) ? boolval($data['UserControl']) : true);
        $this->properties['FadingEdgeEnabled'] = (isset($data['FadingEdgeEnabled']) ? boolval($data['FadingEdgeEnabled']) : true);
        $this->properties['OverScrollMode'] = (isset($data['OverScrollMode']) ? floatval($data['OverScrollMode']) : 0);
    }
}

class ComponentVerticalScrollHandler extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['ScrollBarEnabled'] = (isset($data['ScrollBarEnabled']) ? boolval($data['ScrollBarEnabled']) : true);
        $this->properties['UserControl'] = (isset($data['UserControl']) ? boolval($data['UserControl']) : true);
        $this->properties['FadingEdgeEnabled'] = (isset($data['FadingEdgeEnabled']) ? boolval($data['FadingEdgeEnabled']) : true);
        $this->properties['OverScrollMode'] = (isset($data['OverScrollMode']) ? floatval($data['OverScrollMode']) : 0);
    }
}

class ComponentSpinner extends BaseComponent {
    function __construct($data) {
        parent::__construct($data);
        $this->properties['ElementsFromString'] = $elements = explode(', ', $data['ElementsFromString']);
        $this->properties['Selection'] = (isset($data['Selection']) ? $data['Selection'] : null);
    }
}

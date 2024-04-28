<?php

class RuleSet {

    private String $id;
    private array $inputs;
    private array $actions;
    private array $componentResults;
    private String $description;
    private int $points;

    function __construct(array $inputs, array $actions, array $componentResults, int $points = 0, String $description = "RuleSet") {
        $this->id = uniqid();
        $this->inputs = $inputs;
        $this->actions = $actions;
        $this->componentResults = $componentResults;
        $this->points = $points;
        $this->description = $description;
    }

    public function getId(): String {
        return $this->id;
    }

    public function getPoints(): int {
        return $this->points;
    }

    public function setPoints(int $points): void {
        $this->points = $points;
    }

    public function getDescription(): String {
        return $this->description;
    }

    public function setDescription(String $description): void {
        $this->description = $description;
    }

    public function getInputs() {
        return $this->inputs;
    }

    public function removeInputByIndex(int $index): void {
        unset($this->inputs[$index]);
        $this->inputs = array_values($this->inputs);
        $this->save();
    }

    public function addInput(Input $input): void {
        $this->inputs[] = $input;
    }

    public function getActions(): array {
        return $this->actions;
    }

    public function removeActionByIndex(int $index): void {
        unset($this->actions[$index]);
        $this->actions = array_values($this->actions);
        $this->save();
    }

    public function addAction(Action $action): void {
        $this->actions[] = $action;
    }

    public function getComponentResults(): array {
        return $this->componentResults;
    }

    public function removeResultByIndex(int $index): void {
        unset($this->componentResults[$index]);
        $this->componentResults = array_values($this->componentResults);
        $this->save();
    }

    public function addResult(ComponentResult $result): void {
        $this->componentResults[] = $result;
    }

    public function save(): void {
        $_SESSION['rules'][$this->id] = serialize($this);
        foreach ($_SESSION['projects'] as $projectData) {
            unserialize($projectData)->setNeedRegrade(true);
        }
    }
}

class Input {

    const PROPERTIES = [
        "Button" => ["Text", "Image", "Width", "Height", "BackgroundColor", "TextColor", "Enabled"],
        "Label" => ["Text", "TextColor", "FontSize", "FontTypeface", "HasMargins"],
        "TextBox" => ["Text", "Hint", "Width", "Height", "BackgroundColor", "TextColor", "Enabled", "MultiLine"],
        "Slider" => ["MinValue", "MaxValue", "ThumbPosition", "Visible"],
        "CheckBox" => ["Text", "Checked", "Enabled"],
        "ListView" => ["ElementsFromString", "BackgroundColor", "TextColor", "SelectionColor"],
        "Spinner" => ["Elements", "Prompt", "Selection"],
        "Image" => ["Picture", "Width", "Height", "ScalePictureToFit"],
        "VideoPlayer" => ["Source", "FullScreen", "Volume"],
        "WebViewer" => ["HomeUrl", "CurrentUrl", "CanGoBack", "CanGoForward"],
        "Camera" => ["ImageFile", "UseFront", "Flash"],
        "AccelerometerSensor" => ["Enabled", "Sensitivity"],
        "LocationSensor" => ["Latitude", "Longitude", "ProviderName", "Accuracy", "Enabled"],
        "OrientationSensor" => ["Angle", "Magnitude", "Pitch", "Roll", "Azimuth"],
        "GyroscopeSensor" => ["XAngularVelocity", "YAngularVelocity", "ZAngularVelocity", "Enabled"],
        "ProximitySensor" => ["Distance", "Available", "Enabled"],
        "LightSensor" => ["LightLevel", "Enabled"],
        "Clock" => ["TimerInterval", "TimerEnabled", "TimeZone"],
        "MediaPlayer" => ["Source", "Loop", "Volume"],
        "Sound" => ["Source", "Volume"],
        "BluetoothClient" => ["AddressesAndNames", "Secure", "Enabled"],
        "BluetoothServer" => ["IsAccepting", "Secure", "Enabled"],
        "SpeechRecognizer" => ["Language", "Result"],
        "TextToSpeech" => ["Language", "Pitch", "SpeechRate"],
        "Web" => ["Url", "RequestHeaders", "ResponseFileName"],
        "FirebaseDB" => ["FirebaseURL", "ProjectBucket", "FirebaseToken"],
        "TinyDB" => ["Namespace"],
    ];

    private String $componentInstance;
    private String $property;
    private String $inputValue;
    private mixed $propertyKey;

    function __construct(String $componentInstance, String $property, String $inputValue, $propertyKey = "Button") {
        $this->componentInstance = $componentInstance;
        $this->property = $property;
        $this->inputValue = $inputValue;
        $this->propertyKey = $propertyKey;
    }

    public function getComponentInstance(): String {
        return $this->componentInstance;
    }

    public function setComponentInstance(String $componentInstance): void {
        $this->componentInstance = $componentInstance;
    }

    public function getProperty(): String {
        return $this->property;
    }

    public function setProperty(String $property): void {
        $this->property = $property;
    }

    public function getPropertyKey(): String {
        return $this->propertyKey;
    }

    public function setPropertyKey(String $propertyKey): void {
        $this->propertyKey = $propertyKey;
    }

    public function getInputValue(): String {
        return $this->inputValue;
    }

    public function setInputValue(String $inputValue): void {
        $this->inputValue = $inputValue;
    }
}

class Action {

    const EVENTS = [
        "Button" => ["Click", "LongClick", "TouchDown", "TouchUp", "GotFocus", "LostFocus"],
        "TextBox" => ["GotFocus", "LostFocus", "AfterTextChanged", "BeforeTextChanged", "OnTextChanged"],
        "Slider" => ["PositionChanged"],
        "CheckBox" => ["Changed"],
        "ListView" => ["AfterPicking", "BeforePicking"],
        "Spinner" => ["AfterSelecting", "BeforeSelecting"],
        "VideoPlayer" => ["Started", "Paused", "Completed", "Error"],
        "WebViewer" => ["PageLoaded", "ErrorOccurred", "BeforePageLoad"],
        "Camera" => ["AfterPictureTaken", "BeforePictureTaken", "PictureTakenError"],
        "AccelerometerSensor" => ["Shaking", "AccelerationChanged"],
        "LocationSensor" => ["LocationChanged", "StatusChanged", "ProviderDisabled", "ProviderEnabled"],
        "OrientationSensor" => ["OrientationChanged"],
        "GyroscopeSensor" => ["GyroscopeChanged"],
        "ProximitySensor" => ["ProximityChanged"],
        "LightSensor" => ["LightChanged"],
        "Clock" => ["Timer"],
        "MediaPlayer" => ["Completed", "Error"],
        "Sound" => ["Completed"],
        "BluetoothClient" => ["Connected", "ConnectionFailed"],
        "BluetoothServer" => ["ConnectionAccepted"],
        "SpeechRecognizer" => ["AfterGettingText", "BeforeGettingText"],
        "TextToSpeech" => ["AfterSpeaking"],
        "Web" => ["GotText", "GotFile", "PostText", "PostFile", "WebError"],
        "FirebaseDB" => ["DataChanged", "GotValue", "TagList"],
        "File" => ["GotText", "AfterFileSaved", "FileError"],
    ];

    private String $componentInstance;
    private String $eventName;
    private String $eventKey;

    function __construct(String $componentInstance, String $eventName, String $eventKey = "Button") {
        $this->componentInstance = $componentInstance;
        $this->eventName = $eventName;
        $this->eventKey = $eventKey;
    }

    public function getComponentInstance(): String {
        return $this->componentInstance;
    }

    public function setComponentInstance(String $componentInstance): void {
        $this->componentInstance = $componentInstance;
    }

    public function getEventName(): String {
        return $this->eventName;
    }

    public function setEventName(String $eventName): void {
        $this->eventName = $eventName;
    }

    public function getEventKey(): String {
        return $this->eventKey;
    }

    public function setEventKey(String $eventKey): void {
        $this->eventKey = $eventKey;
    }
}

class ComponentResult {

    const PROPERTIES = [
        "Button" => ["Text", "Image", "Width", "Height", "BackgroundColor", "TextColor", "Enabled"],
        "Label" => ["Text", "TextColor", "FontSize", "FontTypeface", "HasMargins"],
        "TextBox" => ["Text", "Hint", "Width", "Height", "BackgroundColor", "TextColor", "Enabled", "MultiLine"],
        "Slider" => ["MinValue", "MaxValue", "ThumbPosition", "Visible"],
        "CheckBox" => ["Text", "Checked", "Enabled"],
        "ListView" => ["ElementsFromString", "BackgroundColor", "TextColor", "SelectionColor"],
        "Spinner" => ["Elements", "Prompt", "Selection"],
        "Image" => ["Picture", "Width", "Height", "ScalePictureToFit"],
        "VideoPlayer" => ["Source", "FullScreen", "Volume"],
        "WebViewer" => ["HomeUrl", "CurrentUrl", "CanGoBack", "CanGoForward"],
        "Camera" => ["ImageFile", "UseFront", "Flash"],
        "AccelerometerSensor" => ["Enabled", "Sensitivity"],
        "LocationSensor" => ["Latitude", "Longitude", "ProviderName", "Accuracy", "Enabled"],
        "OrientationSensor" => ["Angle", "Magnitude", "Pitch", "Roll", "Azimuth"],
        "GyroscopeSensor" => ["XAngularVelocity", "YAngularVelocity", "ZAngularVelocity", "Enabled"],
        "ProximitySensor" => ["Distance", "Available", "Enabled"],
        "LightSensor" => ["LightLevel", "Enabled"],
        "Clock" => ["TimerInterval", "TimerEnabled", "TimeZone"],
        "MediaPlayer" => ["Source", "Loop", "Volume"],
        "Sound" => ["Source", "Volume"],
        "BluetoothClient" => ["AddressesAndNames", "Secure", "Enabled"],
        "BluetoothServer" => ["IsAccepting", "Secure", "Enabled"],
        "Notifier" => [],
        "SpeechRecognizer" => ["Language", "Result"],
        "TextToSpeech" => ["Language", "Pitch", "SpeechRate"],
        "Web" => ["Url", "RequestHeaders", "ResponseFileName"],
        "FirebaseDB" => ["FirebaseURL", "ProjectBucket", "FirebaseToken"],
        "TinyDB" => ["Namespace"],
        "File" => [],
    ];


    private String $instanceName;
    private String $property;
    private mixed $expectedResult;
    private mixed $propertyKey;

    function __construct(String $instanceName, String $property, mixed $expectedResult, String $propertyKey = "Button") {
        $this->instanceName = $instanceName;
        $this->property = $property;
        $this->expectedResult = $expectedResult;
        $this->propertyKey = $propertyKey;
    }

    public function getInstanceName(): String {
        return $this->instanceName;
    }

    public function setInstanceName(String $instanceName): void {
        $this->instanceName = $instanceName;
    }

    public function getProperty(): String {
        return $this->property;
    }

    public function setProperty(String $property): void {
        $this->property = $property;
    }

    public function getPropertyKey(): String {
        return $this->propertyKey;
    }

    public function setPropertyKey(String $propertyKey): void {
        $this->propertyKey = $propertyKey;
    }

    public function getExpectedResult(): mixed {
        return $this->expectedResult;
    }

    public function setExpectedResult(mixed $expectedResult): void {
        $this->expectedResult = $expectedResult;
    }
}

abstract class GradingSystemUtils {

    public static function getMaxPoints(): int {
        if (!isset($_SESSION['rules'])) return -1;
        $points = 0;
        foreach (GradingSystemUtils::getRuleSets() as $ruleSet) {
            $points += $ruleSet->getPoints();
        }
        return $points;
    }

    public static function getAchievedPointsOfProjectByFileName(String $fileName): int {
        $points = 0;
        $results = unserialize($_SESSION['projects'][$fileName])->getResults();
        foreach (GradingSystemUtils::getRuleSets() as $ruleSet) {
            if (!isset($results[$ruleSet->getId()])) continue;
            if ($results[$ruleSet->getId()]) $points += $ruleSet->getPoints();
        }
        return $points;
    }

    public static function getRuleSets(): array {
        $array = array();
        if (!isset($_SESSION['rules'])) return $array;
        foreach ($_SESSION['rules'] as $ruleSetData) {
            $array[] = unserialize($ruleSetData);
        }
        return $array;
    }

    public static function getRuleSetById(String $id): ?RuleSet {
        foreach (GradingSystemUtils::getRuleSets() as $ruleSet) {
            if ($ruleSet->getId() == $id) return $ruleSet;
        }
        return null;
    }

    public static function getPercentage(int $pointsPerProject, int $pointsMax): mixed {
        try {
            return $percentage = round(((float)$pointsPerProject) / $pointsMax * 100);
        } catch (Throwable $e) {
            return "100";
        }
    }
}

<?php

class Project {

    private ?Interpreter $interpreter = null;
    private ?String $fileName;
    private ?String $projectName;
    private array $components;
    private array $blocks;
    private array $variables;

    function __construct($fileName, $projectName, $components, $blocks, $variables = array()) {
        $this->fileName = $fileName;
        $this->projectName = $projectName;
        $this->components = $components;
        $this->blocks = $blocks;
        $this->variables = $variables;
        $this->addProjectReferenceToChildren();
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function getProjectName() {
        return $this->projectName;
    }

    public function getComponents(): array {
        return $this->components;
    }

    public function getBlocks(): array {
        return $this->blocks;
    }

    public function getVariable($key): mixed {
        if (!isset($this->variables[$key])) {
            if (!str_starts_with($key, "global ")) {
                $this->setVariable($key, 0);
            }
        }
        return $this->variables[$key];
    }

    public function getVariables(): array {
        return $this->variables;
    }

    public function setVariable($key, $value): void {
        $this->variables[$key] = $value;
    }

    public function removeVariable(String $key): void {
        if (!isset($this->variables[$key])) return;
        unset($this->variables[$key]);
    }

    private function addProjectReferenceToChildren(): void {
        foreach ($this->blocks as $block) {
            $block->setProject($this);
        }
        foreach ($this->components as $component) {
            $component->setProject($this);
        }
    }

    public function getComponentByName($componentName): mixed {
        foreach ($this->components as $component) {
            if ($component->getName() === $componentName) return $component;
        }
        return null;
    }

    public function getEvents(): array {
        $array = array();
        foreach($this->blocks as $block) {
            if($block->getType() == "component_event") {
                $array[] = $block;
            }
        }
        ksort($array);
        return $array;
    }

    public function getStartingBlocks(): array {
        $startingBlocks = array();
        foreach ($this->blocks as $block) {
            if (!$block->isStartingBlock()) continue;
            $startingBlocks[] = $block;
        }
        return $startingBlocks;
    }

    public function getDefinedFunction($functionName): Block {
        foreach ($this->blocks as $block) {
            if ($block->getType() != "procedures_defnoreturn" && $block->getType() != "procedures_defreturn") continue;
            if ($block->getField() == $functionName) return $block;
        }
        return null;
    }

    public function getInterpreter(): Interpreter {
        return $this->interpreter;
    }

    public function setInterpreter($interpreter): void {
        $this->interpreter = $interpreter;
    }

    public function getComponentsTypeCount(): array {
        $count = array();
        foreach($this->components as $component) {
            if(isset($count[$component->getType()])) {
                $count[$component->getType()]++;
            } else {
                $count[$component->getType()] = 1;
            }
        }
        ksort($count);
        return $count;
    }

    public function getBlocksTypeCount(): array {
        $count = array();
        foreach($this->blocks as $block) {
            if(isset($count[$block->getAlias()])) {
                $count[$block->getAlias()]++;
            } else {
                $count[$block->getAlias()] = 1;
            }
        }
        ksort($count);
        return $count;
    }

    public function info(): void {
        echo "<h1>Project info</h1>";
        echo "Project name: " . ($this->projectName !== null ? $this->projectName : "null") . "<br>";
        echo "Components count: " . count($this->components) . "<br>";
        echo "Blocks count: " . count($this->blocks) . "<br>";
        echo "Variables:<br>";
        print_r($this->variables);
        echo "<h5>Component list:</h5>";
        $this->info_componentList();
        //echo "<h5>Block list:</h5>";
        //$this->info_blockList();
    }

    public function info_componentList($spacing = 11): void {
        $index = 0;
        foreach ($this->components as $component) {
            echo "[" . $index . "]<br>";
            echo str_pad("Class:", $spacing) . get_class($component) . "<br>";
            echo str_pad("Type:", $spacing) . $component->getType() . "<br>";
            echo str_pad("Name:", $spacing) . $component->getName() . "<br>";
            echo str_pad("Screen:", $spacing) . $component->getScreen() . "<br>";
            echo str_pad("Uuid:", $spacing) . $component->getUuid() . "<br>";
            echo "Props:<br>";
            print_r($component->getProperties());
            echo "<br>";
            $index++;
        }
    }

    public function info_blockList($spacing = 11): void {
        $index = 0;
        foreach ($this->blocks as $block) {
            echo "[" . $index . "]<br>";
            echo str_pad("Class:", $spacing) . get_class($block) . "<br>";
            echo str_pad("Type:", $spacing) . $block->getType() . "<br>";
            echo str_pad("ID:", $spacing) . $block->getId() . "<br>";
            echo str_pad("Screen:", $spacing) . $block->getScreen() . "<br>";
            echo str_pad("Parent:", $spacing) . ($block->getParent() !== null ? $block->getParent()->getId() : "no parent") . "<br>";
            echo str_pad("Sequence:", $spacing) . $block->getSequence() . "<br>";
            echo "<br>";
            $index++;
        }
    }

    public function info_children_class($clazz = Block::class, $spacing = 11): void {
        $index = 0;
        foreach ($this->blocks as $block) {
            if (get_class($block) !== $clazz) continue;
            echo "[" . $index . "]<br>";
            echo str_pad("Class:", $spacing) . get_class($block) . "<br>";
            echo str_pad("ID:", $spacing) . $block->getId() . "<br>";
            echo "Children:<br>";
            $indexChild = 0;
            foreach ($block->getChild() as $child) {
                echo "[" . $index . "-" . $indexChild . "]<br>";
                echo str_pad("Class:", $spacing) . get_class($child) . "<br>";
                echo str_pad("ID:", $spacing) . $child->getID() . "<br>";
                echo str_pad("Seq:", $spacing) . $child->getSequence() . "<br>";
                $indexChild++;
            }
            $index++;
        }
    }

    public function info_controls_if($spacing = 11): void {
        $index = 0;
        foreach ($this->blocks as $block) {
            if (get_class($block) !== BlockControlsIf::class) continue;
            echo "[" . $index . "] ID: " . $block->getId() . "<br>";
            echo str_pad("HasElseIf:", $spacing) . $block->hasElseIf() . "<br>";
            echo ($block->hasElseIf() ? str_pad("ElseIf ID:", $spacing) . $block->getElseifCondition()->getId() . "<br>" : "");
            echo str_pad("HasElse:", $spacing) . $block->hasElse() . "<br>";
            echo "Children:<br>";
            foreach ($block->getCode() as $key => $children) {
                foreach ($children as $child) {
                    echo  str_pad($key, 8) . "-> Class: " . get_class($child) . "(" . $child->getType() . ") ID: " . $child->getId() . " Seq: " . $child->getSequence() . "<br>";
                }
            }
            $index++;
        }
    }
}

abstract class ProjectHandler {

    static function discoverAiaProjects(): array {
        try {
            return ProjectHandler::searchFilesByExtension($_SERVER['DOCUMENT_ROOT'] . "/projectsUpload/" . session_id() . "/", "aia");
        } catch(Exception $e) {
            return array();
        }
    }

    static function loadProject($aiaProjectPath): Project {
        ProjectHandler::unzipProject($aiaProjectPath);
        $loadedProjectFiles = ProjectHandler::loadProjectFiles();
        $projectName = ProjectHandler::extractProjectMetadata($loadedProjectFiles);
        $components = ProjectHandler::processComponents($loadedProjectFiles);
        $blocks = ProjectHandler::processBlocks($loadedProjectFiles['bky']);
        return new Project(pathinfo($aiaProjectPath, PATHINFO_FILENAME), $projectName, $components, $blocks);
    }

    static function extractProjectMetadata($loadedProjectFiles): ?String {
        foreach ($loadedProjectFiles['scm'] as $screen) {
            if (isset($screen['Properties']['AppName'])) {
                $appName = $screen['Properties']['AppName'];
                return $appName;
            }
        }
        return null;
    }

    static function processBlocks($loadedProjectFilesBky) {
        $blocksRaw = array();
        foreach ($loadedProjectFilesBky as $screenName => $screenData) {
            if (isset($screenData['block'])) {
                if (isset($screenData['block']['@attributes'])) {
                    $tmpArray = $screenData['block'];
                    array_splice($screenData['block'], 0);
                    $screenData['block'][0] = $tmpArray;
                }
                ProjectHandler::pushBlocksToArray($screenData['block'], $blocksRaw, $screenName, null);
            }
        }
        print_r($blocksRaw);
        $blocks = ProjectHandler::createInstancesOfBlocks($blocksRaw);
        return $blocks;
    }

    static function pushBlocksToArray($blocksData, &$blocks, $screen, $parent, $metadata = array(), $sequence = 1) {
        foreach ($blocksData as $block) {
            if (isset($block['@attributes'])) {
                $actualBlockId = $block['@attributes']['id'];
                $blocksObjToSave = $block['@attributes'];
                $blocksObjToSave['screen'] = $screen;
                $blocksObjToSave['sequence'] = $sequence;
                $blocksObjToSave['parent'] = $parent;
                $blocksObjToSave['metadata'] = $metadata;

                if (isset($block['mutation']['@attributes'])) {
                    $blocksObjToSave = array_merge($blocksObjToSave, $block['mutation']['@attributes']);
                }

                if (isset($block['field'])) {
                    $blocksObjToSave['field'] = $block['field'];
                }

                $blocks[] = $blocksObjToSave;
            }

            if (isset($block['value'])) {
                if (isset($block['value']['block'])) {
                    ProjectHandler::pushBlocksToArray($block['value'], $blocks, $screen, $actualBlockId, $block['value']['@attributes']);
                } else if (is_array($block['value']) && isset($block['value'][0]['block'])) {
                    for ($index = 0; $index < count($block['value']); $index++) {
                        ProjectHandler::pushBlocksToArray($block['value'][$index], $blocks, $screen, $actualBlockId, $block['value'][$index]['@attributes']);
                    }
                }
            }

            if (isset($block['statement'])) {
                if (isset($block['statement']['block'])) {
                    ProjectHandler::pushBlocksToArray($block['statement'], $blocks, $screen, $actualBlockId, $block['statement']['@attributes']);
                } else if (is_array($block['statement']) && isset($block['statement'][0]['block'])) {
                    for ($index = 0; $index < count($block['statement']); $index++) {
                        ProjectHandler::pushBlocksToArray($block['statement'][$index], $blocks, $screen, $actualBlockId, $block['statement'][$index]['@attributes']);
                    }
                }
            }

            if (isset($block['next']['block'])) {
                ProjectHandler::pushBlocksToArray($block['next'], $blocks, $screen, $parent, array(), $sequence + 1);
            }
        }
    }

    static function createInstancesOfBlocks($blocksRawData) {
        $blocks = array();

        //create instances
        foreach ($blocksRawData as $blockData) {
            $newBlock = ProjectHandler::createBlockByType($blockData);
            $blocks[] = $newBlock;
        }

        //add parent/child
        foreach ($blocksRawData as $blockRaw) {
            $blockChild = ProjectHandler::getBlockById($blocks, $blockRaw['id']);
            $blockParent = ProjectHandler::getBlockById($blocks, $blockRaw['parent']);
            if ($blockParent != null) {
                $blockParent->addChild($blockChild);
                $blockChild->setParent($blockParent);
            }
        }

        for ($i = 0; $i < count($blocks); $i++) {
            if (get_class($blocks[$i]) !== BlockControlsIf::class) continue;
            $controlsBlock = $blocks[$i];
            $conditionsCount = 1 + $controlsBlock->getElseifCount();
            $conditionsProcessed = 0;
            $codeArraySorter = -1;
            for ($v = $i; $v < count($blocks); $v++) {
                $nextBlock = $blocks[$v];
                if($nextBlock->getParent() == $controlsBlock) {
                    if($conditionsProcessed < $conditionsCount) {
                        $controlsBlock->addCondition($nextBlock);
                        $conditionsProcessed++;
                    } else {
                        if(isset($nextBlock->getMetadata()['name']) && (str_starts_with($nextBlock->getMetadata()['name'], "DO") || $nextBlock->getMetadata()['name'] == "ELSE")) {
                            $codeArraySorter++;
                        }
                        $controlsBlock->addCode($codeArraySorter, $nextBlock);
                    }
                }
            }
            if($controlsBlock->getElseCount() > 0) {
                $controlsBlock->addCondition(null);
            }
        }

        return $blocks;
    }

    static function getBlockById($blocks, $id): ?Block {
        if ($id == null) return null;
        foreach ($blocks as $block) {
            if ($block->getId() == $id) {
                return $block;
            }
        }
        return null;
    }

    static function createBlockByType($blockData): Block {

        switch ($blockData['type']) {
            case "component_event":
                return new BlockComponentEvent($blockData);
            case "procedures_defnoreturn":
                return new BlockProceduresDefnoreturn($blockData);
            case "procedures_defreturn":
                return new BlockProceduresDefreturn($blockData);
            case "procedures_callnoreturn":
                return new BlockProceduresCallnoreturn($blockData);
            case "procedures_callreturn":
                return new BlockProceduresCallreturn($blockData);
            case "component_method":
                return new BlockComponentMethod($blockData);
            case "component_set_get":
                return new BlockComponentSetGet($blockData);
            case "component_component_block":
                return new BlockComponentComponentBlock($blockData);
            case "text":
                return new BlockText($blockData);
            case "text_join":
                return new BlockTextJoin($blockData);
            case "text_length":
                return new BlockTextLength($blockData);
            case "text_compare":
                return new BlockTextCompare($blockData);
            case "text_starts_at":
                return new BlockTextStartsAt($blockData);
            case "text_contains":
                return new BlockTextContains($blockData);
            case "text_replace_all":
                return new BlockTextReplaceAll($blockData);
            case "text_reverse":
                return new BlockTextReverse($blockData);
            case "text_changeCase":
                return new BlockTextChangeCase($blockData);
            case "text_isEmpty":
                return new BlockTextIsEmpty($blockData);
            case "math_number":
                return new BlockMathNumber($blockData);
            case "math_add":
                return new BlockMathAdd($blockData);
            case "math_subtract":
                return new BlockMathSubtract($blockData);
            case "math_multiply":
                return new BlockMathMultiply($blockData);
            case "math_division":
                return new BlockMathDivision($blockData);
            case "math_is_a_number":
                return new BlockMathIsNumber($blockData);
            case "math_convert_number":
                return new BlockMathConvertNumber($blockData);
            case "math_number_radix":
                return new BlockMathNumberRadix($blockData);
            case "math_format_as_decimal":
                return new BlockMathFormatAsDecimal($blockData);
            case "math_convert_angles":
                return new BlockMathConvertAngles($blockData);
            case "math_atan2":
                return new BlockMathAtan2($blockData);
            case "math_trig":
                return new BlockMathTrig($blockData);
            case "math_divide":
                return new BlockMathDivide($blockData);
            case "math_power":
                return new BlockMathPower($blockData);
            case "math_bitwise":
                return new BlockMathBitwise($blockData);
            case "math_random_int":
                return new BlockMathRandomInt($blockData);
            case "math_random_float":
                return new BlockMathRandomFloat($blockData);
            case "math_on_list":
                return new BlockMathOnList($blockData);
            case "math_single":
                return new BlockMathSingle($blockData);
            case "math_compare":
                return new BlockMathCompare($blockData);
            case "logic_boolean":
                return new BlockLogicBoolean($blockData);
            case "logic_false":
                return new BlockLogicFalse($blockData);
            case "logic_compare":
                return new BlockLogicCompare($blockData);
            case "logic_or":
                return new BlockLogicOr($blockData);
            case "logic_operation":
                return new BlockLogicOperation($blockData);
            case "global_declaration":
                return new BlockGlobalDeclaration($blockData);
            case "controls_if":
                return new BlockControlsIf($blockData);
            case "controls_forRange":
                return new BlockControlsForRange($blockData);
            case "controls_do_then_return":
                return new BlockControlsDoThenReturn($blockData);
            case "logic_negate":
                return new BlockLogicNegate($blockData);
            case "lexical_variable_set":
                return new BlockLexicalVariableSet($blockData);
            case "lexical_variable_get":
                return new BlockLexicalVariableGet($blockData);
            case "local_declaration_statement":
                return new BlockLocalDeclarationStatement($blockData);
            case "local_declaration_expression":
                return new BlockLocalDeclarationExpression($blockData);
            case "lists_create_with":
                return new BlockListsCreate($blockData);
            case "lists_add_items":
                return new BlockListsAddItems($blockData);
            case "color_make_color":
                return new BlockColorMakeColor($blockData);                
        }

        if (str_starts_with($blockData['type'], "color_")) {
            return new BlockColor($blockData);
        }

        return new Block($blockData);
    }

    static function processComponents($loadedProjectFiles) {
        $components = array();
        $screens = ProjectHandler::discoverScreens($loadedProjectFiles);
        foreach ($screens as $screen) {
            ProjectHandler::pushComponentsToArray($loadedProjectFiles['scm'][$screen]['Properties']['$Components'], $components, $screen);
        }
        return $components;
    }

    static function discoverScreens($loadedProjectFiles) {
        $screens = array();
        foreach ($loadedProjectFiles['scm'] as $screenName => $screenData) {
            $screens[] = $screenName;
        }
        return $screens;
    }

    static function pushComponentsToArray($componentsInProject, &$components, $screen) {
        //print_r($componentsInProject);
        foreach ($componentsInProject as $component) {
            $component['screen'] = $screen;
            $instance = null;
            switch ($component['$Type']) {
                case "Button":
                    $instance = new ComponentButton($component);
                    break;
                case "Image":
                    $instance = new ComponentImage($component);
                    break;
                case "Label":
                    $instance = new ComponentLabel($component);
                    break;
                case "CheckBox":
                    $instance = new ComponentCheckBox($component);
                    break;
                case "Switch":
                    $instance = new ComponentSwitch($component);
                    break;
                case "TextBox":
                    $instance = new ComponentTextBox($component);
                    break;
                case "Slider":
                    $instance = new ComponentSlider($component);
                    break;
                case "PasswordTextBox":
                    $instance = new ComponentPasswordTextBox($component);
                    break;
                case "HorizontalArrangement":
                    $instance = new ComponentHorizontalArrangement($component);
                    break;
                case "VerticalArrangement":
                    $instance = new ComponentVerticalArrangement($component);
                    break;
                case "Canvas":
                    $instance = new ComponentCanvas($component);
                    break;
                case "Notifier":
                    $instance = new ComponentNotifier($component);
                    break;
                case "Sound":
                    $instance = new ComponentSound($component);
                    break;
                case "Player":
                    $instance = new ComponentPlayer($component);
                    break;
                case "AccelerometerSensor":
                    $instance = new ComponentAccelerometerSensor($component);
                    break;
                case "Clock":
                    $instance = new ComponentClock($component);
                    break;
                case "ImageSprite":
                    $instance = new ComponentImageSprite($component);
                    break;
                case "HorizontalScrollArrangement":
                    $instance = new ComponentHorizontalScrollArrangement($component);
                    break;
                case "HorizontalScrollHandler":
                    $instance = new ComponentHorizontalScrollHandler($component);
                    break;
                case "VerticalScrollHandler":
                    $instance = new ComponentVerticalScrollHandler($component);
                    break;
                case "ListView":
                    $instance = new ComponentListView($component);
                    break;
                case "BarcodeScanner":
                    $instance = new ComponentBarcodeScanner($component);
                    break;
                case "Spinner":
                    $instance = new ComponentSpinner($component);
                    break;
                default:
                    $instance = new BaseComponent($component);
            }
            $components[] = $instance;
            if (isset($component['$Components']) && is_array($component['$Components'])) {
                ProjectHandler::pushComponentsToArray($component['$Components'], $components, $screen);
            }
        }
    }

    //Clear tmp folder
    static function clearTmp(): void {
        ProjectHandler::rrmdir($_SERVER['DOCUMENT_ROOT'] . "/tmp/" . session_id());
    }

    //Delete specific folder
    static function rrmdir($dir): void {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        ProjectHandler::rrmdir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    //Lookup files by extension in specific folder
    static function searchFilesByExtension($dir, $extension): array {
        $fileList = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        foreach ($iterator as $path => $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === $extension) {
                $fileList[] = $path;
            }
        }

        return $fileList;
    }

    static function getFilenameWithoutExtension($filePath) {
        $filenameWithExtension = basename($filePath);
        $filenameWithoutExtension = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
        
        return $filenameWithoutExtension;
    }

    //Unzip specific project to selected folder
    static function unzipProject(String $filePath): void {
        $filename = ProjectHandler::getFilenameWithoutExtension($filePath);
        $pathTo = $_SERVER['DOCUMENT_ROOT'] . "/tmp/" . session_id();
        ProjectHandler::clearTmp();
        $zip = new ZipArchive;
        $zip->open($filePath);
        $zip->extractTo($pathTo);
    }

    static function loadProjectFiles(): array {
        $projectFiles = array();
        $projectFiles['scm'] = ProjectHandler::loadProjectFilesScm();
        $projectFiles['bky'] = ProjectHandler::loadProjectFilesBky();
        return $projectFiles;
    }

    static function loadProjectFilesScm(): array {

        $projectFiles = array();

        $filesFound = ProjectHandler::searchFilesByExtension($_SERVER['DOCUMENT_ROOT'] . "/tmp", "scm");
        foreach ($filesFound as $file) {
            $handle = fopen($file, "r");
            $readText = "";
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $readText = $readText . $line;
                }
                fclose($handle);
            }
            $projectFiles[basename($file, ".scm")] = json_decode(ProjectHandler::extractJsonFromFileScm($readText), true);
        }

        return $projectFiles;
    }

    static function loadProjectFilesBky(): array {

        $projectFiles = array();

        $filesFound = ProjectHandler::searchFilesByExtension($_SERVER['DOCUMENT_ROOT'] . "/tmp", "bky");
        foreach ($filesFound as $file) {
            $handle = fopen($file, "r");
            $readText = "";
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $readText = $readText . $line;
                }
                fclose($handle);
            }
            $xml = simplexml_load_string($readText);
            echo $xml;
            $jsonString = json_encode($xml);
            $arrayData = json_decode($jsonString, true);
            $projectFiles[basename($file, ".bky")] = $arrayData;
        }

        return $projectFiles;
    }

    static function extractJsonFromFileScm($text) {
        preg_match('/\{.*\}/s', $text, $extractedJsonText);
        return $extractedJsonText[0];
    }
}

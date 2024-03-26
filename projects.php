<?php

class Project {

    private ?String $projectName;
    private array $components;
    private array $blocks;
    private array $globalVariables;

    function __construct($projectName, $components, $blocks, $globalVariables = array()) {
        $this->projectName = $projectName;
        $this->components = $components;
        $this->blocks = $blocks;
        $this->globalVariables = $globalVariables;
    }

    public function getComponents(): array {
        return $this->components;
    }

    public function getBlocks(): array {
        return $this->blocks;
    }

    public function getGlobalVariables(): array {
        return $this->globalVariables;
    }

    public function info(): void {
        echo "<h1>Project info</h1>";
        echo "Project name: " . ($this->projectName !== null ? $this->projectName : "null") . "<br>";
        echo "Components count: " . count($this->components) . "<br>";
        echo "Blocks count: " . count($this->blocks) . "<br>";
        echo "<h5>Component list:</h5>";
        $this->info_componentList();
        echo "<h5>Block list:</h5>";
        $this->info_blockList();
    }

    public function info_componentList($spacing = 11): void {
        $index = 0;
        foreach($this->components as $component) {
            echo "[" . $index . "]<br>";
            echo str_pad("Class:", $spacing) . get_class($component) . "<br>";
            echo str_pad("Type:", $spacing) . $component->getType() . "<br>";
            echo str_pad("Name:", $spacing) . $component->getName() . "<br>";
            echo str_pad("Screen:", $spacing) . $component->getScreen() . "<br>";
            echo str_pad("Uuid:", $spacing) . $component->getUuid() . "<br>";
            echo "<br>";
            $index++;
        }
    }

    public function info_blockList($spacing = 11): void {
        $index = 0;
        foreach($this->blocks as $block) {
            echo "[" . $index . "]<br>";
            echo str_pad("Class:", $spacing) . get_class($block) . "<br>";
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
        foreach($this->blocks as $block) {
            if(get_class($block) !== $clazz) continue;
            echo "[" . $index . "]<br>";
            echo str_pad("Class:", $spacing) . get_class($block) . "<br>";
            echo str_pad("ID:", $spacing) . $block->getId() . "<br>";
            echo "Children:<br>";
            $indexChild = 0;
            foreach($block->getChild() as $child) {
                echo "[" . $index . "-" . $indexChild . "]<br>";
                echo str_pad("Class:", $spacing) . get_class($child) . "<br>";
                echo str_pad("ID:", $spacing) . $child->getID() . "<br>";
                echo str_pad("Seq:", $spacing) . $child->getSequence() . "<br>";
                $indexChild++;
            }
            $index++;
        }
    }

}

abstract class ProjectHandler {

    static function discoverAiaProjects(): array {
        return ProjectHandler::searchFilesByExtension("./projects/", "aia");
    }

    static function loadProject($aiaProjectPath): Project {
        ProjectHandler::unzipProject($aiaProjectPath);
        $loadedProjectFiles = ProjectHandler::loadProjectFiles();
        $projectName = ProjectHandler::extractProjectMetadata($loadedProjectFiles);
        $components = ProjectHandler::processComponents($loadedProjectFiles);
        $blocks = ProjectHandler::processBlocks($loadedProjectFiles['bky']);
        return new Project($projectName, $components, $blocks);
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
        //print_r($blocksRaw);
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
                    for($index = 0; $index < count($block['value']); $index++) {
                        ProjectHandler::pushBlocksToArray($block['value'][$index], $blocks, $screen, $actualBlockId, $block['value'][$index]['@attributes']);
                    }
                }
            }
    
            if (isset($block['statement'])) {
                if (isset($block['statement']['block'])) {
                    ProjectHandler::pushBlocksToArray($block['statement'], $blocks, $screen, $actualBlockId, $block['statement']['@attributes']);
                } else if (is_array($block['statement']) && isset($block['statement'][0]['block'])) {
                    for($index = 0; $index < count($block['statement']); $index++) {
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
        print_r($blocksRawData);
        $blocks = array();
    
        //create instances
        foreach ($blocksRawData as $blockData) {
            $newBlock = ProjectHandler::createBlockByType($blockData);
            $blocks[] = $newBlock;
        }
    
        //add paremt/child
        foreach ($blocksRawData as $blockRaw) {
            $blockChild = ProjectHandler::getBlockById($blocks, $blockRaw['id']);
            $blockParent = ProjectHandler::getBlockById($blocks, $blockRaw['parent']);
            if ($blockParent != null) {
                $blockParent->addChild($blockChild);
                $blockChild->setParent($blockParent);
            }
        }
    
        //add conditions to controls_if
        foreach ($blocks as $block) {
            if(get_class($block) == BlockControlsIf::class) {
                foreach($blocks as $searchedBlock) {
                    if(($searchedBlock->getParent()) != null && ($searchedBlock->getParent()->getId() != $block->getId())) continue;
                    if(!isset($searchedBlock->getMetadata()['name'])) continue;
                    if($searchedBlock->getMetadata()['name'] == "IF0")
                        $block->setIfCondition($searchedBlock);
                    if($searchedBlock->getMetadata()['name'] == "IF1")
                        $block->setElseifCondition($searchedBlock);
                    if($searchedBlock->getMetadata()['name'] == "DO0")
                        $block->setIfCode($searchedBlock);
                    if($searchedBlock->getMetadata()['name'] == "DO1")
                        $block->setElseifCode($searchedBlock);
                    if($searchedBlock->getMetadata()['name'] == "ELSE")
                        $block->setElseCode($searchedBlock);
                }
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
    
        if (str_starts_with($blockData['type'], "color_")) {
            return new BlockColor($blockData);
        }
    
        switch ($blockData['type']) {
            case "component_event":
                return new BlockComponentEvent($blockData);
            case "component_set_get":
                return new BlockComponentSetGet($blockData);
            case "text":
                return new BlockText($blockData);
            case "math_number":
                return new BlockMathNumber($blockData);
            case "logic_boolean":
                return new BlockLogicBoolean($blockData);
            case "global_declaration":
                return new BlockGlobalDeclaration($blockData);
            case "controls_if":
                return new BlockControlsIf($blockData);
            default:
                return new Block($blockData);
        }
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
        ProjectHandler::rrmdir('tmp');
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
    
    //Unzip specific project to selected folder
    static function unzipProject(String $filename, String $pathTo = './tmp'): void {
        ProjectHandler::clearTmp();
        $zip = new ZipArchive;
        $zip->open($filename);
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
    
        $filesFound = ProjectHandler::searchFilesByExtension("./tmp", "scm");
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
    
        $filesFound = ProjectHandler::searchFilesByExtension("./tmp", "bky");
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
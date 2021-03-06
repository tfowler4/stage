<?php 

include 'script.php';

class PushStageToLive extends Script {
    protected static $_gameName;
    protected static $_destination;
    protected static $_newFileLocation;
    protected static $_destinationFolder;
    protected static $_currentFileLocation;
    protected static $_numberOfFiles = 0;
    protected static $_exemptFolders = array('docs', 'images', 'temp', 'data', '.git');

    const SOURCE_FOLDER = ABSOLUTE_PATH;

    public static function init() {
        Logger::log('INFO', 'Starting Push Stage To Live...', 'dev');

        $folders       = explode("/", self::SOURCE_FOLDER);
        $remove_folder = array_pop($folders);
        array_push($folders, 'site-');

        self::$_destinationFolder = implode("/", $folders);

        if ( isset($_GET['gameName']) ) {
            self::$_gameName    = strtolower($_GET['gameName']);
            self::$_destination = self::$_destinationFolder . self::$_gameName;
        }

        if ( file_exists(self::$_destination) ) {
            Logger::log('INFO', 'Starting Search Directory in ' . self::$_gameName . '...', 'dev');

            self::searchDirectory(self::SOURCE_FOLDER);
            
            Logger::log('INFO', 'Search Directory in ' . self::$_gameName . ' Completed!', 'dev');
            Logger::log('INFO', 'Total Number of Files Updated: ' . self::$_numberOfFiles, 'dev');
        }
        Logger::log('INFO', 'Push Stage To Live Completed!', 'dev');
    }
    
    public static function searchDirectory($currentFolder) {
        $liveFiles     = array();
        $stageFiles    = array();
        $modifiedFiles = array();

        $handle = opendir($currentFolder);
        
        while ( false !== ($file = readdir($handle)) ) {
            self::$_currentFileLocation = $currentFolder . '/' . $file;

            if ( (isset(self::$_gameName)) ) {
                self::$_newFileLocation = str_replace(self::SOURCE_FOLDER, self::$_destination, self::$_currentFileLocation);
            }

            if ( self::checkExemptions(self::$_currentFileLocation) == FALSE) {
                if ( ($file != '.') && ($file != '..') ) {
                    if ( is_dir(self::$_currentFileLocation) ) {
                        self::checkDirectory(self::$_currentFileLocation);
                        self::searchDirectory(self::$_currentFileLocation);
                    } else if ( !is_dir(self::$_currentFileLocation) ) {
                        // To get files from stage and live before the copy
                        $stageFiles = self::getCurrentFiles(self::$_currentFileLocation);
                        $liveFiles  = self::getCurrentFiles(self::$_newFileLocation);
                        // To compare modified files with live files and prepare for logging
                        $modifiedFiles = self::compareModifiedFiles($stageFiles, $liveFiles);
                        if ( !empty($modifiedFiles) ) {
                            self::logModifiedFiles($modifiedFiles);
                        }
                        // To copy files from stage to live
                        self::moveFiles(self::$_currentFileLocation, self::$_newFileLocation);
                    }
                }
            }
        }
        closedir($handle);
   }
    
    public static function checkDirectory($file) {

        if ( (isset(self::$_gameName)) ) {
            $directory = str_replace(self::SOURCE_FOLDER, self::$_destination, $file);
        } 

        if( !is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
    
    public static function moveFiles($oldFile, $newFile) {
        copy($oldFile, $newFile);
    }

    public static function checkExemptions($folderName) {
        $folders = explode('/', $folderName);

        if ( (isset(self::$_gameName)) ) {
            if (self::$_gameName == 'rift') {
                array_push(self::$_exemptFolders, 'wildstar');
            } else if (self::$_gameName == 'wildstar') {
                array_push(self::$_exemptFolders, 'rift');
            }
            
        } 

        foreach (self::$_exemptFolders as $exemption) {
            if ( in_array($exemption, $folders) ) {
                return true;
            }
        }
    }

    public static function getCurrentFiles($file) {
        $files = array();
        if ( is_file($file) ) {
            $modifiedTime = filemtime($file);
            $folders      = explode('/', $file);
            $currentFile  = array_pop($folders);
            $files[]      = array('fileName' => $currentFile, 'modifiedTime' => $modifiedTime);
        }
        return $files;
    }

    public static function compareModifiedFiles($stageFiles, $liveFiles) {
        $modifiedFiles = array();

        if ( is_array($stageFiles) && is_array($liveFiles) ) {
            foreach ($stageFiles as $stageFile) {
                $modified = true;

                foreach ($liveFiles as $liveFile) {
                    if ( ($stageFile['fileName'] == $liveFile['fileName']) && ($stageFile['modifiedTime'] < $liveFile['modifiedTime']) ) {
                        $modified = false;
                    }
                }

                if ($modified == true) {
                    $modifiedFiles[] = $stageFile;
                }
                return $modifiedFiles;
            }
        } 
    }

    public static function logModifiedFiles($files) {
        self::$_numberOfFiles += count($files);

        if ( is_array($files) ) {
            foreach ($files as $file) {
                $fileInfo = 'File: ' . $file['fileName'] . ' Last Modified: ' . date ("F d Y H:i:s", $file['modifiedTime']);
                Logger::log('INFO', $fileInfo, 'dev');
            }
        }
    }
}

PushStageToLive::init();

?>

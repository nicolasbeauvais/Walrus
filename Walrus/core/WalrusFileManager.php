<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 19:36 28/01/14
 */

namespace Walrus\core;

use Exception;

/**
 * Class WalrusFileManager
 * @package Walrus\core
 */
class WalrusFileManager
{

    /**
     * Contain the work directory for the file manager
     *
     * @var string
     */
    private $root;

    /**
     * The current element used by the file manager.
     *
     * The current element is relative to the root path.
     *
     * @var string
     */
    private $currentElem;

    /**
     * An array with all action donne by the file manager.
     *
     * @var array
     */
    private $logs;

    /**
     * Constructor of the filemanager.
     *
     * __construct set the private $root variable, and set the current element to it.
     *
     * @param string $root
     *
     * @throws Exception in case the root path isn't valid
     */
    public function __construct ($root)
    {
        if (!is_dir($root)) {
            throw new Exception('"' . $root . '" isn\'t a valid folder path');
        }

        if ($root[strlen($root) - 1] !== '/' && $root[strlen($root) - 1] !== '\\') {
            $root .= DIRECTORY_SEPARATOR;
        }

        $this->root = $root;
        $this->currentElem = $this->makePath('');
        $this->addLog('Filemanager as been initialized with the root: ' . $root);
    }

    /**
     * Format a given path with several parameters.
     *
     * @param string $path
     * @param string $type
     * @param bool   $needToExist
     *
     * @return string
     * @throws Exception in case the path isn't a valid element
     */
    private function makePath ($path, $type = 'root', $needToExist = true)
    {
        if (!empty($path) && ($path[0] == '/' || $path[0] == '\\')) {
            $path = substr($path, 1, strlen($path));
        }

        if (!empty($path) && ($path[strlen($path) - 1] !== '/' || $path[strlen($path) - 1] !== '\\')
            && is_dir($this->root . $path)) {
            $path .= DIRECTORY_SEPARATOR;
        }

        if ($type === 'root') {
            $path = $this->root . $path;
        } elseif ($type === 'current') {
            $path = $this->currentElem . $path;
        }

        if ($needToExist && !file_exists($path)) {
            throw new Exception('"' . $path . '" isn\'t a valid element');
        }

        return $path;
    }


    /**
     * Set the current elem.
     * This need to be an existing element, all filemanager action are relative to the current element,
     * by default the current element have the $root path for value.
     *
     * @param string $elemPathRelativeToRoot a valid file path
     *
     * @return string the path of the current element
     */
    public function setCurrentElem ($elemPathRelativeToRoot)
    {
        $this->currentElem = $this->makePath($elemPathRelativeToRoot);
        $this->addLog('Current item as been set: ' . $this->currentElem);

        return $this->currentElem;
    }

    /**
     * Return the current element path.
     *
     * This function should be used for debug only, as the other function never required a full path.
     */
    public function getCurrentElem ()
    {
        return $this->currentElem;
    }

    /**
     * Return an array with the info of the current file.
     * Infos are:
     * filesize -> in date format
     * name -> basename of the file
     * path -> path of the current elem, relative to $root
     * lastEdit -> last date the file was edited
     *
     * @return array
     */
    public function fileDetails ()
    {
        clearstatcache(true, $this->currentElem);

        $fileInfo['fileSize'] = $this->fmFilesize($this->currentElem);
        $fileInfo['name'] = $this->fmBasename($this->currentElem);
        $fileInfo['path'] = $this->currentElem;
        $fileInfo['lastEdit'] = date('Y-m-d H:i:s', $this->fmFilemtime($this->currentElem));

        $this->addLog('Current item infos as been required');
        return $fileInfo;
    }

    /**
     * Delete the current file.
     *
     * @throws Exception
     */
    public function deleteCurrent ()
    {
        if (is_dir($this->currentElem)) {
            $directoryStream = $this->fmOpendir($this->currentElem);
            while ($file = $this->fmReaddir($directoryStream)) {
                if ($file != "." && $file != "..") {
                    throw new Exception('"' . $this->currentElem . '" must be empty to delete it');
                }
            }

            $this->fmRmdir($this->currentElem);
        } else {
            $this->fmUnlink($this->currentElem);
        }

        $this->setCurrentElem('');

        $this->addLog('Current item as been deleted');
    }

    /**
     * Rename the current file.
     *
     * $newName must be a valid file name.
     *
     * @param $newName
     *
     * @return string
     * @throws Exception if $newName isn't a valid file name
     */
    public function renameCurrent ($newName)
    {
        if (strpbrk($newName, "\\/?%*:|\"<>")) {
            throw new Exception('"' . $newName . '" isn\'t a valid file name');
        }

        $oldPath = $this->currentElem;
        $newPath = $this->makePath($newName, 'root', false);

        $this->fmRename($oldPath, $newPath);
        $this->setCurrentElem($newName);

        $this->addLog('Current item as been renamed from: ' . $oldPath . ' to:' . $newPath);
        return $newPath;
    }

    /**
     * Move the current file to the specified path.
     *
     * @param $newPath
     *
     * @return string
     * @throws Exception if the new path isn't valid
     */
    public function moveCurrent ($newPath)
    {

        if (!empty($newPath) && ($newPath[strlen($newPath)- 1] !== '/' || $newPath[strlen($newPath)- 1] !== '\\')) {
            $newPath .= DIRECTORY_SEPARATOR;
        }

        $fileDetails = $this->fileDetails();
        $fileName = $fileDetails['name'];

        if (!is_dir($this->root . $newPath)) {
            throw new Exception('"' . $this->root . $newPath . '" isn\'t a valid folder for move');
        }

        if (file_exists($newPath . $fileName)) {
            throw new Exception('"' . $newPath . $fileName . '" already exist');
        }

        $filePath = $newPath . $fileName;
        $oldPath = $this->currentElem;
        $newPath = $this->makePath($filePath, 'root', false);

        $this->fmRename($oldPath, $newPath);
        $this->setCurrentElem($filePath);

        $this->addLog('Current item as been moved from: ' . $oldPath . ' to:' . $newPath);
        return $newPath;
    }

    /**
     * Return an array of the elements in currentItem.
     *
     * Can be recursive, currentItem must be a folder.
     *
     * @param bool $recursive
     *
     * @return array
     * @throws Exception
     */
    public function getElements ($recursive = false)
    {
        if (!is_dir($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a folder');
        }

        $elements = $this->getElementsRecursivly($this->currentElem, $recursive);

        $this->addLog('Current folder items as been requested');
        return $elements;
    }

    /**
     * Return an array of all directory in currentItem.
     *
     * Always recursive, similar to getElements but fr directories only.
     *
     * @return array
     * @throws Exception
     */
    public function getFolderTree ()
    {
        if (!is_dir($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a folder');
        }

        $elements = $this->getElementsRecursivly($this->currentElem, true, true);

        $this->addLog('Current folder folderTree as been requested');
        return $elements;
    }

    /**
     * Recursive function to explore folder tree.
     *
     * @param      $path
     * @param      $recursive
     * @param bool $dirOnly
     *
     * @return array
     */
    private function getElementsRecursivly ($path, $recursive, $dirOnly = false)
    {
        $folderStream = $this->fmOpendir($path);
        $elements = array();

        while ($file = $this->fmReaddir($folderStream)) {

            if ($file == "." || $file == "..") {
                continue;
            }

            if (is_file($path . $file) && !$dirOnly) {
                $elements[] = $file;
            } elseif (is_dir($path . $file)) {
                if ($recursive) {
                    $elements[$file] = $this->getElementsRecursivly($path . $file . '/', $recursive, $dirOnly);
                } else {
                    $elements[] = $file;
                }
            }
        }
        $this->fmClosedir($folderStream);

        return $elements;
    }

    /**
     * Empty the currentItem if it's a folder.
     *
     * @throws Exception if current elem isn't a directory
     */
    public function emptyFolder ()
    {
        if (!is_dir($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a folder');
        }

        $elements = $this->getElements(true);

        foreach ($elements as $key => $value) {
            if (is_array($value) && !empty($value)) {
                throw new Exception('"' . $this->currentElem . $key . '" must be empty');
            }

            if (is_dir($this->currentElem . $key)) {
                $this->fmRmdir($this->currentElem . $key);
            } else {
                $this->fmUnlink($this->currentElem . $value);
            }
        }

        $this->setCurrentElem('');
        $this->addLog('Current folder as been emptied');
    }

    /**
     * Create a new folder in the currentElement if it is a directory.
     *
     * The directory to create need to have a valid directory name.
     *
     * @param string $folderName created directory name
     * @param int    $chmod
     *
     * @throws Exception in case the $folderName isn't valid
     * @return string
     */
    public function folderCreate ($folderName, $chmod = 0700)
    {
        if (strpbrk($folderName, "\\/?%*:|\"<>")) {
            throw new Exception('"' . $folderName . '" isn\'t a valid folder name');
        }
        if (file_exists($this->currentElem . $folderName)) {
            throw new Exception('"' . $this->currentElem . $folderName . '" already exist');
        }

        $path = $this->makePath($folderName, 'current', false);

        $this->fmMkdir($path, $chmod);

        $this->addLog('A new folder "' . $folderName . '" as been folderCreated in ' . $this->currentElem);
        return $path;
    }

    /**
     * Create a file, by default with the 'w' fopen() param.
     *
     * @param string $fileName the name of the file to create
     * @param string $param a valid paramater for the fopen() function
     *
     * @throws Exception
     * @return string
     */
    public function fileCreate ($fileName, $param = 'w')
    {
        if (strpbrk($fileName, "\\/?%*:|\"<>")) {
            throw new Exception('"' . $fileName . '" isn\'t a valid file name');
        }
        if (file_exists($this->currentElem . $fileName)) {
            throw new Exception('"' . $this->currentElem . $fileName . '" already exist');
        }

        $path = $this->makePath($fileName, 'current', false);

        $this->fmFopen($path, $param, true);

        $this->addLog('A new file "' . $fileName . '" as been folderCreated in ' . $this->currentElem);
        return $path;
    }

    /**
     * Function to handle file upload.
     *
     * This function use the $_FILES super global with the HTML artibute of your file input
     * to handle an upload, the file will be uploaded to the currentElem path, the currentElem
     * must be a folder.
     *
     * @param string $fileInputName the name attribute of the input file (HTML)
     *
     * @return string
     * @throws Exception if the $_FILES superglobal can't be found or if the upload failed
     */
    public function uploadFile ($fileInputName)
    {
        if (!isset($_FILES[$fileInputName]) || empty($_FILES[$fileInputName])) {
            throw new Exception('invalid input name for file upload : "' . $fileInputName . '"');
        }

        if (empty($_FILES[$fileInputName]['tmp_name']) || $_FILES[$fileInputName]['error'] != UPLOAD_ERR_OK) {
            throw new Exception('an error occurred during upload : "' . $fileInputName . '"');
        }

        $filePath = $_FILES[$fileInputName]['tmp_name'];
        $destinationPath = $this->makePath($_FILES[$fileInputName]['name'], 'current', false);
        $this->fmMoveUploadedFile($filePath, $destinationPath);

        $this->addLog('File "' . $destinationPath . '" as been uploaded');
        return $destinationPath;
    }

    /**
     * Return the content of a file.
     *
     * The currentElem must be a valid file to read.
     *
     * @param string $type set type to 'array' for a return as an array
     * @param int $start first line to return
     * @param int @end last line to return
     *
     * @return string|array
     * @throws Exception if the currentElem isn't a file
     */
    public function getFileContent ($type = null, $start = null, $end = null)
    {
        clearstatcache(true, $this->currentElem);

        if (!is_file($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a file');
        }

        if (!$start && !$end && !$type) {
            $stream = $this->fmFopen($this->currentElem, "rb");
            $size = $this->fmFilesize($this->currentElem) ?: 1;

            $content = $this->fmFread($stream, $size);
            $this->fmFclose($stream);
        } else {
            $content = $type == 'array' ? array() : '';
            $file = $this->fmFile($this->currentElem);

            if ($start && $end) {
                foreach ($file as $key => $line) {
                    if ($key > $start && $key < $end) {
                        if ($type == 'array') {
                            $content[] = $line;
                        } else {
                            $content .= $line;
                        }
                    }
                }
            } else {
                $content = $file;
            }

        }

        $this->addLog('File "' . $this->currentElem . '" as been readed');
        return $content;
    }

    /**
     * Change all the content of a file with a new content.
     *
     * The old content of the file is erased. The currentElem must be a valid file.
     *
     * @param string $newContent the new content to put on the file
     *
     * @return string
     * @throws Exception if the currentElem isn't a valid file
     */
    public function changeFileContent ($newContent)
    {
        if (!is_file($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a file');
        }

        $this->fmFopen($this->currentElem, "w+");

        if (is_writable($this->currentElem)) {
            $file = $this->fmFopen($this->currentElem, "w");
            $this->fmWrite($file, $newContent);
            $this->fmFclose($file);
        }

        $this->addLog('File "' . $this->currentElem . '" content as been changed');
        return $this->getFileContent();
    }

    /**
     * Add a new content at the end of the file.
     *
     * The old file content is preserved.
     *
     * @param string $newContent the new content to put at the end of the file.
     *
     * @return string
     * @throws Exception if the currentElem isn't a valid file
     */
    public function addFileContent ($newContent)
    {
        if (!is_file($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a file');
        }

        $content = $this->getFileContent();
        $content = $content . $newContent;

        $this->changeFileContent($content);

        $this->addLog('File "' . $this->currentElem . '" content as been updated');
        return $this->getFileContent();
    }

    /**
     * Launch a download of the current file.
     *
     * The currentElem must be a valid file
     *
     * @return string the value of the file to download
     * @throws Exception if the currentElem isn't a valid file
     */
    public function downloadFile ()
    {
        if (!is_file($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a file');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->fmBasename($this->currentElem));
        header('Content-Length: ' . $this->fmFilesize($this->currentElem));

        $flux = $this->fmFopen($this->currentElem, 'rb');

        $result = '';
        while (!feof($flux)) {
            $result .= $this->fmFread($flux, 8192);
        }

        $this->fmFclose($flux);

        ob_flush();
        flush();

        return $result;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return bool|string
     * @throws Exception
     */
    private function fmFilesize ($path)
    {
        if (!file_exists($path)) {
            throw new Exception('"file: ' . $path . ' didn\'t exist"');
        }

        $fileSize = filesize($path);

        if (!is_numeric($fileSize)) {
            throw new Exception('"An error occurred when tried to get filesize for file: ' . $path . '"');
        }

        return $fileSize;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return int
     * @throws Exception
     */
    private function fmFilemtime ($path)
    {
        if (!file_exists($path)) {
            throw new Exception('"file: ' . $path . ' didn\'t exist"');
        }

        $fileMTime = filemtime($path);

        if (!$fileMTime) {
            throw new Exception('"An error occured when tried to get filemtime for file: ' . $path . '"');
        }

        return $fileMTime;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return string
     * @throws Exception
     */
    private function fmBasename ($path)
    {
        if (!file_exists($path)) {
            throw new Exception('"file: ' . $path . ' didn\'t exist"');
        }

        return basename($path);
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return resource
     * @throws Exception
     */
    private function fmOpendir ($path)
    {
        if (!is_dir($path)) {
            throw new Exception('"file: ' . $path . ' need to be a folder"');
        }

        $stream = opendir($path);

        if (!$stream) {
            throw new Exception('"An error occured when tried to open the dir : ' . $path . '"');
        }

        return $stream;
    }

    /**
     * Handler function
     *
     * @param $stream
     *
     * @return string
     */
    private function fmReaddir ($stream)
    {
        $read = readdir($stream);

        return $read;
    }

    /**
     * Handler function
     *
     * @param $stream
     */
    private function fmClosedir ($stream)
    {
        closedir($stream);
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return bool
     * @throws Exception
     */
    private function fmRmdir ($path)
    {
        if (!is_dir($path)) {
            throw new Exception('"file: ' . $path . ' need to be a folder"');
        }

        $rm = rmdir($path);

        if (!$rm) {
            throw new Exception('"An error occured when tried to delete dir: ' . $path . '"');
        }

        return $rm;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return bool
     * @throws Exception
     */
    private function fmUnlink ($path)
    {
        if (!is_file($path)) {
            throw new Exception('"file: ' . $path . ' need to be a valid file"');
        }

        $rm = unlink($path);

        if (!$rm) {
            throw new Exception('"An error occured when tried to delete file: ' . $path . '"');
        }

        return $rm;
    }

    /**
     * Handler function
     *
     * @param $oldPath
     * @param $newPath
     *
     * @return bool
     * @throws Exception
     */
    private function fmRename ($oldPath, $newPath)
    {
        if (!file_exists($oldPath)) {
            throw new Exception('"file: ' . $oldPath . ' need to be a valid file"');
        }

        if (file_exists($newPath)) {
            throw new Exception('"file: ' . $oldPath . ' already exist"');
        }

        $rename = rename($oldPath, $newPath);

        if (!$rename) {
            throw new Exception(
                '"An error occured when tried to rename file from: ' . $oldPath . ' to ' . $newPath . '"'
            );
        }

        return $rename;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return bool
     * @throws Exception
     */
    private function fmMkdir ($path)
    {
        if (file_exists($path)) {
            throw new Exception('file: "' . $path . '" already exist');
        }

        $mkdir = mkdir($path);

        if (!$mkdir) {
            throw new Exception('An error occurred when tried to create folder: "' . $path . '"');
        }

        return $mkdir;
    }

    /**
     * Handler function
     *
     * @param $name
     * @param $destination
     *
     * @return bool
     * @throws Exception
     */
    private function fmMoveUploadedFile ($name, $destination)
    {
        $moved = move_uploaded_file($name, $destination);

        if (!$moved) {
            throw new Exception(
                'An error occurred when tried to move uploaded file: "' . $name . '" to "' . $destination . '"'
            );
        }

        return $moved;
    }

    /**
     * Handler function
     *
     * @param string $path
     * @param string $param
     * @param bool   $create
     *
     * @return resource
     * @throws Exception
     */
    private function fmFopen ($path, $param, $create = false)
    {
        if (!is_file($path) && $create === false) {
            throw new Exception('file: ' . $path . ' need to be a valid file');
        }

        $params = array('r', 'r+', 'rb', 'rb+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+');

        if (!in_array($param, $params)) {
            throw new Exception('Param: "' . $param . ' need to be a valid fopen parameter"');
        }

        $fopen = fopen($path, $param);

        if (!$fopen) {
            throw new Exception('An error occurred when tried to open file: "' . $path . '"');
        }

        return $fopen;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return array
     * @throws Exception
     */
    private function fmFile ($path)
    {
        $file = file($path);

        if (!$file) {
            throw new Exception('An error occurred when tried to read the file');
        }

        return $file;
    }

    /**
     * Handler function
     *
     * @param $stream
     * @param $size
     *
     * @return string
     * @throws Exception
     */
    private function fmFread ($stream, $size)
    {
        $fread = fread($stream, $size);

        if ($fread === false) {
            throw new Exception('An error occurred when tried to read file');
        }

        return $fread;
    }

    /**
     * Handler function
     *
     * @param $stream
     *
     * @return bool
     * @throws Exception
     */
    private function fmFclose ($stream)
    {
        $fclose = fclose($stream);

        if (!$fclose) {
            throw new Exception('An error occurred when tried to close file');
        }

        return $fclose;
    }

    /**
     * Handler function
     *
     * @param $path
     * @param $content
     *
     * @return int
     * @throws Exception
     */
    private function fmWrite ($path, $content)
    {
        $fwrite = fwrite($path, $content);

        if ($fwrite === false) {
            throw new Exception('An error occurred when tried to write in file : "' . $path . '"');
        }

        return $fwrite;
    }

    /**
     * Add a log line to the log array.
     *
     * @param string $message
     */
    private function addLog ($message)
    {
        $this->logs[] = array('datetime' => date("Y-m-d H:i:s"),
                              'message' => $message);
    }

    /**
     * Return the complete array of the WalrusFileManager log.
     *
     * @return array
     */
    public function getLogs ()
    {
        return $this->logs;
    }
}

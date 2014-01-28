<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 19:36 28/01/14
 */


namespace Walrus\core;

use Exception;

class WalrusFileManager
{

    private $root;
    private $currentElem;
    private $logs;

    /**
     * FileManager Basics
     */
    public function __construct ($root)
    {
        if (!is_dir($root)) {
            throw new Exception('"' . $root . '" isn\'t a valid folder path');
        }

        if ($root[strlen($root) - 1] !== '/') {
            $root .= '/';
        }

        $this->root = $root;
        $this->currentElem = $this->makePath('');
        $this->addLog('Filemanager as been initialized with the root: ' . $root);
    }

    private function makePath ($path, $type = 'root', $needToExist = true)
    {
        if (!empty($path) && $path[0] == '/') {
            $path = substr($path, 1, strlen($path));
        }

        if (!empty($path) && $path[strlen($path) - 1] !== '/' && is_dir($this->root . $path)) {
            $path .= '/';
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
     * General
     */

    public function setCurrentElem ($elem)
    {
        $this->currentElem = $this->makePath($elem);
        $this->addLog('Current item as been set: ' . $this->currentElem);

        return $this->currentElem;
    }

    public function fileDetails ()
    {
        $fileInfo['fileSize'] = $this->fmFilesize($this->currentElem);
        $fileInfo['name'] = $this->fmBasename($this->currentElem);
        $fileInfo['path'] = $this->currentElem;
        $fileInfo['lastEdit'] = date('Y-m-d H:i:s', $this->fmFilemtime($this->currentElem));

        $this->addLog('Current item infos as been required');
        return $fileInfo;
    }

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

    public function moveCurrent ($newPath)
    {

        if (!empty($newPath) && $newPath[strlen($newPath)- 1] !== '/') {
            $newPath .= '/';
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
     * Folders
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

    public function getFolderTree ()
    {
        if (!is_dir($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a folder');
        }

        $elements = $this->getElementsRecursivly($this->currentElem, true, true);

        $this->addLog('Current folder folderTree as been requested');
        return $elements;
    }

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

        $this->addLog('Current folder as been emptied');
    }

    public function folderCreate ($folder, $chmod = 0700)
    {
        if (strpbrk($folder, "\\/?%*:|\"<>")) {
            throw new Exception('"' . $folder . '" isn\'t a valid folder name');
        }
        if (file_exists($this->currentElem . $folder)) {
            throw new Exception('"' . $this->currentElem . $folder . '" already exist');
        }

        $path = $this->makePath($folder, 'current', false);

        $this->fmMkdir($path, $chmod);

        $this->addLog('A new folder "' . $folder . '" as been folderCreated in ' . $this->currentElem);
        return $path;
    }

    /**
     * Files
     */

    public function fileCreate ($file)
    {
        if (strpbrk($file, "\\/?%*:|\"<>")) {
            throw new Exception('"' . $file . '" isn\'t a valid file name');
        }
        if (file_exists($this->currentElem . $file)) {
            throw new Exception('"' . $this->currentElem . $file . '" already exist');
        }

        $path = $this->makePath($file, 'current', false);

        $this->fmFopen($path, 'w', true);

        $this->addLog('A new file "' . $file . '" as been folderCreated in ' . $this->currentElem);
        return $path;
    }

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

    public function getFileContent ()
    {
        if (!is_file($this->currentElem)) {
            throw new Exception('"' . $this->currentElem . '" need to be a file');
        }

        $stream = $this->fmFopen($this->currentElem, "rb");
        $size = $this->fmFilesize($this->currentElem) ?: 1;

        $content = $this->fmFread($stream, $size);
        $this->fmFclose($stream);

        $this->addLog('File "' . $this->currentElem . '" as been readed');
        return $content;
    }

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
     * System Call
     */
    private function fmFilesize ($path)
    {
        if (!file_exists($path)) {
            throw new Exception('"file: ' . $path . ' didn\'t exist"');
        }

        $fileSize = date(filesize($path));

        if (!is_numeric($fileSize)) {
            throw new Exception('"An error occurred when tried to get filesize for file: ' . $path . '"');
        }

        return $fileSize;
    }

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

    private function fmBasename ($path)
    {
        if (!file_exists($path)) {
            throw new Exception('"file: ' . $path . ' didn\'t exist"');
        }

        return basename($path);
    }

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

    private function fmReaddir ($stream)
    {
        $read = readdir($stream);

        return $read;
    }

    private function fmClosedir ($stream)
    {
        closedir($stream);
    }

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

    private function fmFopen ($path, $param)
    {
        if (!is_file($path)) {
            throw new Exception('"file: ' . $path . ' need to be a valid file"');
        }

        $params = array('r', 'r+', 'rb', 'rb+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+');

        if (!in_array($param, $params)) {
            throw new Exception('file: "' . $path . ' need to be a valid file"');
        }

        $fopen = fopen($path, $param);

        if (!$fopen) {
            throw new Exception('An error occurred when tried to open file: "' . $path . '"');
        }

        return $fopen;
    }

    private function fmFread ($stream, $size)
    {
        $fread = fread($stream, $size);

        if ($fread === false) {
            throw new Exception('An error occurred when tried to read file');
        }

        return $fread;
    }

    private function fmFclose ($stream)
    {
        $fclose = fclose($stream);

        if (!$fclose) {
            throw new Exception('An error occurred when tried to close file');
        }

        return $fclose;
    }

    private function fmWrite ($path, $content)
    {
        $fwrite = fwrite($path, $content);

        if ($fwrite === false) {
            throw new Exception('An error occurred when tried to write in file : "' . $path . '"');
        }

        return $fwrite;
    }

    /**
     * Log tools
     */

    private function addLog ($message)
    {
        $this->logs[] = array('datetime' => date("Y-m-d H:i:s"),
                              'message' => $message);
    }

    public function getLogs ()
    {
        return $this->logs;
    }
}

<?php

namespace Walrus\core;


class WalrusFileManager
{

    private $root;
    private $currentElem;
    private $logs;

    /**
     * FileManager Basics
     */
    function __construct ($root)
    {
        if (!is_dir($root))
            throw new FileManagerException('"' . $root . '" isn\'t a valid folder path');

        if ($root[strlen($root) - 1] !== '/')
            $root .= '/';

        $this->root = $root;
        $this->currentElem = $this->makePath('');
        $this->addLog('Filemanager as been initialized with the root: ' . $root);
    }

    private function makePath ($path, $type = 'root', $needToExist = TRUE)
    {
        if (!empty($path) && $path[0] == '/')
            $path = substr($path, 1, strlen($path));

        if (!empty($path) && $path[strlen($path) - 1] !== '/' && is_dir($this->root . $path))
            $path .= '/';

        if ($type === 'root')
            $path = $this->root . $path;
        else if ($type === 'current')
            $path = $this->currentElem . $path;

        if ($needToExist && !file_exists($path)){
            throw new FileManagerException('"' . $path . '" isn\'t a valid element');
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
        $fileInfo['fileSize'] = $this->FM_fileSize($this->currentElem);
        $fileInfo['name'] = $this->FM_basename($this->currentElem);
        $fileInfo['path'] = $this->currentElem;
        $fileInfo['lastEdit'] = date('Y-m-d H:i:s', $this->FM_filemtime($this->currentElem));

        $this->addLog('Current item infos as been required');
        return $fileInfo;
    }

    public function deleteCurrent ()
    {
        if (is_dir($this->currentElem))
        {
            $directoryStream = $this->FM_opendir($this->currentElem);
            while($file = $this->FM_readdir($directoryStream))
            {
                if($file != "." && $file != "..")
                    throw new FileManagerException('"' . $this->currentElem . '" must be empty to delete it');
            }

            $this->FM_rmdir($this->currentElem);
        }
        else
        {
            $this->FM_unlink($this->currentElem);
        }

        $this->setCurrentElem('');

        $this->addLog('Current item as been deleted');
    }

    public function renameCurrent ($newName)
    {
        if (strpbrk($newName, "\\/?%*:|\"<>"))
            throw new FileManagerException('"' . $newName . '" isn\'t a valid file name');

        $oldPath = $this->currentElem;
        $newPath = $this->makePath($newName, 'root', FALSE);

        $this->FM_rename($oldPath, $newPath);
        $this->setCurrentElem($newName);

        $this->addLog('Current item as been renamed from: ' . $oldPath . ' to:' . $newPath);
        return $newPath;
    }

    public function moveCurrent ($newPath)
    {

        if (!empty($newPath) && $newPath[strlen($newPath)- 1] !== '/')
            $newPath .= '/';

        $fileDetails = $this->fileDetails();
        $fileName = $fileDetails['name'];

        if (!is_dir($this->root . $newPath))
            throw new FileManagerException('"' . $this->root . $newPath . '" isn\'t a valid folder for move');

        if(file_exists($newPath . $fileName))
            throw new FileManagerException('"' . $newPath . $fileName . '" already exist');

        $filePath = $newPath . $fileName;
        $oldPath = $this->currentElem;
        $newPath = $this->makePath($filePath, 'root', FALSE);

        $this->FM_rename($oldPath, $newPath);
        $this->setCurrentElem($filePath);

        $this->addLog('Current item as been moved from: ' . $oldPath . ' to:' . $newPath);
        return $newPath;
    }

    /**
     * Folders
     */

    public function getElements ($recursive = FALSE)
    {
        if (!is_dir($this->currentElem))
            throw new FileManagerException('"' . $this->currentElem . '" need to be a folder');

        $elements = $this->getElementsRecursivly($this->currentElem, $recursive);

        $this->addLog('Current folder items as been requested');
        return $elements;
    }

    public function getFolderTree ()
    {
        if (!is_dir($this->currentElem))
            throw new FileManagerException('"' . $this->currentElem . '" need to be a folder');

        $elements = $this->getElementsRecursivly($this->currentElem, TRUE, TRUE);

        $this->addLog('Current folder folderTree as been requested');
        return $elements;
    }

    private function getElementsRecursivly ($path, $recursive, $dirOnly = FALSE)
    {
        $folderStream = $this->FM_opendir($path);
        $elements = array();

        while($file = $this->FM_readdir($folderStream)){

            if($file == "." || $file == "..")
                continue;

            if(is_file($path . $file) && !$dirOnly)
            {
                $elements[] = $file;
            }
            else if(is_dir($path . $file))
            {
                if ($recursive)
                    $elements[$file] = $this->getElementsRecursivly($path . $file . '/', $recursive, $dirOnly);
                else
                    $elements[] = $file;
            }
        }
        $this->FM_closedir($folderStream);

        return $elements;
    }

    public function emptyFolder ()
    {
        if (!is_dir($this->currentElem))
            throw new FileManagerException('"' . $this->currentElem . '" need to be a folder');

        $elements = $this->getElements(TRUE);

        foreach ($elements AS $key => $value)
        {
            if (is_array($value) && !empty($value))
                throw new FileManagerException('"' . $this->currentElem . $key . '" must be empty');

            if (is_dir($this->currentElem . $key))
            {
                $this->FM_rmdir($this->currentElem . $key);
            }
            else
            {
                $this->FM_unlink($this->currentElem . $value);
            }
        }

        $this->addLog('Current folder as been emptied');
    }

    public function folderCreate ($folder, $chmod = 0700)
    {
        if (strpbrk($folder, "\\/?%*:|\"<>"))
            throw new FileManagerException('"' . $folder . '" isn\'t a valid folder name');
        if (file_exists($this->currentElem . $folder))
            throw new FileManagerException('"' . $this->currentElem . $folder . '" already exist');

        $path = $this->makePath($folder, 'current', FALSE);

        $this->FM_mkdir($path, $chmod);

        $this->addLog('A new folder "' . $folder . '" as been folderCreated in ' . $this->currentElem);
        return $path;
    }

    /**
     * Files
     *
     * Récupérer le contenu
     * Changer le contenu
     * Ajouter du contenu
     * Télécharger
     */

    public function uploadFile ($fileInputName)
    {
        if (!isset($_FILES[$fileInputName]) || empty($_FILES[$fileInputName]))
            throw new FileManagerException('invalid input name for file upload : "' . $fileInputName . '"');

        if(empty($_FILES[$fileInputName]['tmp_name']) || $_FILES[$fileInputName]['error'] != UPLOAD_ERR_OK)
            throw new FileManagerException('an error occurred during upload : "' . $fileInputName . '"');

        $filePath = $_FILES[$fileInputName]['tmp_name'];
        $destinationPath = $this->makePath($_FILES[$fileInputName]['name'], 'current', FALSE);
        $this->FM_move_uploaded_file($filePath, $destinationPath);

        $this->addLog('File "' . $destinationPath . '" as been uploaded');
        return $destinationPath;
    }

    public function getFileContent ()
    {
        if (!is_file($this->currentElem))
            throw new FileManagerException('"' . $this->currentElem . '" need to be a file');

        $stream = $this->FM_fopen($this->currentElem, "rb");
        $size = $this->FM_filesize($this->currentElem) ?: 1;

        $content = $this->FM_fread($stream, $size);
        $this->FM_fclose($stream);

        $this->addLog('File "' . $this->currentElem . '" as been readed');
        return $content;
    }

    public function changeFileContent ($newContent)
    {
        if (!is_file($this->currentElem))
            throw new FileManagerException('"' . $this->currentElem . '" need to be a file');

        $this->FM_fopen($this->currentElem, "w+");

        if (is_writable($this->currentElem)){
            $file = $this->FM_fopen($this->currentElem, "w");
            $this->FM_fwrite($file, $newContent);
            $this->FM_fclose($file);
        }

        $this->addLog('File "' . $this->currentElem . '" content as been changed');
        return $this->getFileContent();
    }

    public function addFileContent ($newContent)
    {
        if (!is_file($this->currentElem))
            throw new FileManagerException('"' . $this->currentElem . '" need to be a file');

        $content = $this->getFileContent();
        $content = $content . $newContent;

        $this->changeFileContent($content);

        $this->addLog('File "' . $this->currentElem . '" content as been updated');
        return $this->getFileContent();
    }

    public function downloadFile ()
    {
        if (!is_file($this->currentElem))
            throw new FileManagerException('"' . $this->currentElem . '" need to be a file');

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->FM_basename($this->currentElem));
        header('Content-Length: ' . $this->FM_filesize($this->currentElem));

        $flux = $this->FM_fopen($this->currentElem, 'rb');

        $result = '';
        while (!$this->FM_feof($flux))
            $result .= $this->FM_fread($flux, 8192);

        $this->FM_fclose($flux);

        $this->FM_ob_flush();
        $this->FM_flush();

        return $result;
    }

    /**
     * System Call
     */
    private function FM_fileSize ($path)
    {
        if (!file_exists($path))
            throw new systemCallException('"file: ' . $path . ' didn\'t exist"');

        $fileSize = date(filesize($path));

        if (!is_numeric($fileSize))
            throw new systemCallException('"An error occurred when tried to get filesize for file: ' . $path . '"');

        return $fileSize;
    }

    private function FM_filemtime ($path)
    {
        if (!file_exists($path))
            throw new systemCallException('"file: ' . $path . ' didn\'t exist"');

        $fileMTime = filemtime($path);

        if (!$fileMTime)
            throw new systemCallException('"An error occured when tried to get filemtime for file: ' . $path . '"');

        return $fileMTime;
    }

    private function FM_basename ($path)
    {
        if (!file_exists($path))
            throw new systemCallException('"file: ' . $path . ' didn\'t exist"');

        return basename($path);
    }

    private function FM_opendir ($path)
    {
        if (!is_dir($path))
            throw new systemCallException('"file: ' . $path . ' need to be a folder"');

        $stream = opendir($path);

        if(!$stream)
            throw new systemCallException('"An error occured when tried to open the dir : ' . $path . '"');

        return $stream;
    }

    private function FM_readdir ($stream)
    {
        $read = readdir($stream);

        return $read;
    }

    private function FM_closedir ($stream)
    {
        closedir($stream);
    }

    private function FM_rmdir ($path)
    {
        if (!is_dir($path))
            throw new systemCallException('"file: ' . $path . ' need to be a folder"');

        $rm = rmdir($path);

        if(!$rm)
            throw new systemCallException('"An error occured when tried to delete dir: ' . $path . '"');

        return $rm;
    }

    private function FM_unlink ($path)
    {
        if (!is_file($path))
            throw new systemCallException('"file: ' . $path . ' need to be a valid file"');

        $rm = unlink($path);

        if(!$rm)
            throw new systemCallException('"An error occured when tried to delete file: ' . $path . '"');

        return $rm;
    }

    private function FM_rename ($oldPath, $newPath)
    {
        if (!file_exists($oldPath))
            throw new systemCallException('"file: ' . $oldPath . ' need to be a valid file"');

        if(file_exists($newPath))
            throw new systemCallException('"file: ' . $oldPath . ' already exist"');

        $rename = rename($oldPath, $newPath);

        if(!$rename)
            throw new systemCallException('"An error occured when tried to rename file from: ' . $oldPath
                . ' to ' . $newPath . '"');

        return $rename;
    }

    private function FM_mkdir ($path)
    {
        if (file_exists($path))
            throw new systemCallException('file: "' . $path . '" already exist');

        $mkdir = mkdir($path);

        if(!$mkdir)
            throw new systemCallException('An error occurred when tried to create folder: "' . $path . '"');

        return $mkdir;
    }

    private function FM_move_uploaded_file ($name, $destination)
    {
        $moved = move_uploaded_file($name, $destination);

        if (!$moved)
            throw new systemCallException('An error occurred when tried to move uploaded file: "' . $name
                . '" to "' . $destination . '"');

        return $moved;
    }

    private function FM_fopen ($path, $param)
    {
        if (!is_file($path))
            throw new systemCallException('"file: ' . $path . ' need to be a valid file"');

        $params = array('r', 'r+', 'rb', 'rb+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+');

        if (!in_array($param, $params))
            throw new systemCallException('file: "' . $path . ' need to be a valid file"');

        $fopen = fopen($path, $param);

        if (!$fopen)
            throw new systemCallException('An error occurred when tried to open file: "' . $path . '"');

        return $fopen;
    }

    private function FM_fread($stream, $size)
    {
        $fread = fread($stream, $size);

        if ($fread === FALSE)
            throw new systemCallException('An error occurred when tried to read file');

        return $fread;
    }

    private function FM_fclose ($stream)
    {
        $fclose = fclose($stream);

        if (!$fclose)
            throw new systemCallException('An error occurred when tried to close file');

        return $fclose;
    }

    private function FM_fwrite ($path, $content)
    {
        $fwrite = fwrite($path, $content);

        if ($fwrite === FALSE)
            throw new systemCallException('An error occurred when tried to write in file : "' . $path . '"');

        return $fwrite;
    }

    private function FM_feof ($flux)
    {
        $feof = feof($flux);
        return $feof;
    }

    private function FM_ob_flush ()
    {
        ob_flush();
    }

    private function FM_flush ()
    {
        flush();
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

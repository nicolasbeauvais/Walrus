<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 19:36 28/01/14
 */

namespace Walrus\core;

use Walrus\core\WalrusException;

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
     * @throws WalrusException in case the root path isn't valid
     */
    public function __construct ($root)
    {
        if (!is_dir($root)) {
            throw new WalrusException('"' . $root . '" isn\'t a valid folder path');
        }

        if ($root[strlen($root) - 1] !== '/' && $root[strlen($root) - 1] !== '\\') {
            $root .= DIRECTORY_SEPARATOR;
        }

        $this->root = $root;
        $this->currentElem = $this->makePath('');
        $this->addLog('FileManager as been initialized with the root: ' . $root);
    }

    /**
     * Format a given path with several parameters.
     *
     * @param string $path
     * @param string $type
     * @param bool   $needToExist
     *
     * @return string
     * @throws WalrusException in case the path isn't a valid element
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
            throw new WalrusException('"' . $path . '" isn\'t a valid element');
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
     * @throws WalrusException
     */
    public function deleteCurrent ()
    {
        if (is_dir($this->currentElem)) {
            $directoryStream = $this->fmOpendir($this->currentElem);
            while ($file = $this->fmReaddir($directoryStream)) {
                if ($file != "." && $file != "..") {
                    throw new WalrusException('"' . $this->currentElem . '" must be empty to delete it');
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
     * @throws WalrusException if $newName isn't a valid file name
     */
    public function renameCurrent ($newName)
    {
        if (strpbrk($newName, "\\/?%*:|\"<>")) {
            throw new WalrusException('"' . $newName . '" isn\'t a valid file name');
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
     * @throws WalrusException if the new path isn't valid
     */
    public function moveCurrent ($newPath)
    {

        if (!empty($newPath) && ($newPath[strlen($newPath)- 1] !== '/' || $newPath[strlen($newPath)- 1] !== '\\')) {
            $newPath .= DIRECTORY_SEPARATOR;
        }

        $fileDetails = $this->fileDetails();
        $fileName = $fileDetails['name'];

        if (!is_dir($this->root . $newPath)) {
            throw new WalrusException('"' . $this->root . $newPath . '" isn\'t a valid folder for move');
        }

        if (file_exists($newPath . $fileName)) {
            throw new WalrusException('"' . $newPath . $fileName . '" already exist');
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
     * Copy the entire folder recursively, or a single file to a new path.
     *
     * @param string $origin
     * @param string $newPath
     * @param array $blacklist an array of folders / files to ignore while copying
     *
     * @return string
     * @throws WalrusException if the new path isn't valid
     */
    public function copy ($origin, $newPath, $blacklist = array())
    {
        $currentOrigin = $this->currentElem;

        if (!is_dir($this->currentElem)) {
            throw new WalrusException('"' . $this->currentElem . '" isn\'t a valid folder for copy');
        }

        if (!file_exists($this->pathJoin($currentOrigin, $origin))) {
            throw new WalrusException('"' . $this->filerPathJoin($origin) . '" need to exist');
        }

        $dir = opendir($this->pathJoin($currentOrigin, $origin));

        while (false !== ($file = readdir($dir))) {

            $item = !empty($origin) ? $this->pathJoin($origin, $file) : $file;

            if (($file != '.') && ($file != '..')
                && !in_array($item, $blacklist)) {

                if (is_dir($this->filerPathJoin($origin, $file))) {
                    $this->setCurrentElem($newPath);
                    $this->folderCreate($file);
                    $this->setCurrentElem('');
                    $this->copy(
                        $this->pathJoin($origin, $file),
                        $this->pathJoin($newPath, $file),
                        $blacklist
                    );
                } else {
                    copy(
                        $this->currentElem . $this->pathJoin($origin, $file),
                        $this->currentElem . $this->pathJoin($newPath, $file)
                    );
                }
            }
        }

        closedir($dir);
        $this->currentElem = $currentOrigin;
    }

    /**
     * Return an array of the elements in currentItem.
     *
     * Can be recursive, currentItem must be a folder.
     *
     * @param bool $recursive
     *
     * @return array
     * @throws WalrusException
     */
    public function getElements ($recursive = false)
    {
        if (!is_dir($this->currentElem)) {
            throw new WalrusException('"' . $this->currentElem . '" need to be a folder');
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
     * @throws WalrusException
     */
    public function getFolderTree ()
    {
        if (!is_dir($this->currentElem)) {
            throw new WalrusException('"' . $this->currentElem . '" need to be a folder');
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
                    $elements[$file] = $this->getElementsRecursivly(
                        $path . $file . DIRECTORY_SEPARATOR,
                        $recursive,
                        $dirOnly
                    );
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
     * @throws WalrusException if current elem isn't a directory
     */
    public function emptyFolder ($array = null)
    {
        if (!is_dir($this->currentElem)) {
            throw new WalrusException('"' . $this->currentElem . '" need to be a folder');
        }

        $originElem = $this->currentElem;

        $elements = !empty($array) ? $array : $this->getElements(true);

        foreach ($elements as $key => $value) {

            if (is_dir($this->filerPathJoin($key))) {

                if (is_array($value) && !empty($value)) {
                    $this->currentElem = $this->filerPathJoin($key);
                    $this->emptyFolder($value);
                    $this->currentElem = $originElem;
                }

                $this->fmRmdir($this->filerPathJoin($key));
            } else {
                $this->fmUnlink($this->filerPathJoin($value));
            }
        }

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
     * @throws WalrusException in case the $folderName isn't valid
     * @return string
     */
    public function folderCreate ($folderName, $chmod = 0755)
    {
        if (strpbrk($folderName, "\\/?%*:|\"<>")) {
            throw new WalrusException('"' . $folderName . '" isn\'t a valid folder name');
        }
        if (file_exists($this->currentElem . $folderName)) {
            throw new WalrusException('"' . $this->currentElem . $folderName . '" already exist');
        }

        $path = $this->makePath($folderName, 'current', false);

        $this->fmMkdir($path, $chmod);

        $this->addLog('A new folder "' . $folderName . '" as been created in ' . $this->currentElem);
        return $path;
    }

    /**
     * Create a file, by default with the 'w' fopen() param.
     *
     * @param string $fileName the name of the file to create
     * @param string $param a valid paramater for the fopen() function
     *
     * @throws WalrusException
     * @return WalrusFileManager
     */
    public function fileCreate ($fileName, $param = 'w')
    {
        if (strpbrk($fileName, "\\/?%*:|\"<>")) {
            throw new WalrusException('"' . $fileName . '" isn\'t a valid file name');
        }
        if (file_exists($this->currentElem . $fileName)) {
            throw new WalrusException('"' . $this->currentElem . $fileName . '" already exist');
        }

        $path = $this->makePath($fileName, 'current', false);

        $this->fmFopen($path, $param, true);

        $this->addLog('A new file "' . $fileName . '" as been created in ' . $this->currentElem);

        $this->currentElem = $path;

        return $this;
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
     * @throws WalrusException if the $_FILES superglobal can't be found or if the upload failed
     */
    public function uploadFile ($fileInputName)
    {
        if (!isset($_FILES[$fileInputName]) || empty($_FILES[$fileInputName])) {
            throw new WalrusException('invalid input name for file upload : "' . $fileInputName . '"');
        }

        if (empty($_FILES[$fileInputName]['tmp_name']) || $_FILES[$fileInputName]['error'] != UPLOAD_ERR_OK) {
            throw new WalrusException('an error occurred during upload : "' . $fileInputName . '"');
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
     * @throws WalrusException if the currentElem isn't a file
     */
    public function getFileContent ($type = null, $start = null, $end = null)
    {
        clearstatcache(true, $this->currentElem);

        if (!is_file($this->currentElem)) {
            throw new WalrusException('"' . $this->currentElem . '" need to be a file');
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
     * @throws WalrusException if the currentElem isn't a valid file
     */
    public function changeFileContent ($newContent)
    {
        if (!is_file($this->currentElem)) {
            throw new WalrusException('"' . $this->currentElem . '" need to be a file');
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
     * @throws WalrusException if the currentElem isn't a valid file
     */
    public function addFileContent ($newContent)
    {
        if (!is_file($this->currentElem)) {
            throw new WalrusException('"' . $this->currentElem . '" need to be a file');
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
     * @throws WalrusException if the currentElem isn't a valid file
     */
    public function downloadFile ()
    {
        if (!is_file($this->currentElem)) {
            throw new WalrusException('"' . $this->currentElem . '" need to be a file');
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
     * Join path with DIRECTORY_SEPARATOR concatednated with currentElem.
     *
     * @return string
     */
    public function filerPathJoin()
    {
        $elem = $this->currentElem;
        if ($elem[strlen($elem) - 1] !== '/' && $elem[strlen($elem) - 1] !== '\\') {
            $elem .= DIRECTORY_SEPARATOR;
        }
        return $elem . implode(DIRECTORY_SEPARATOR, func_get_args());
    }

    /**
     * Join path with DIRECTORY_SEPARATOR
     *
     * @return string
     */
    public function pathJoin()
    {
        return implode(DIRECTORY_SEPARATOR, func_get_args());
    }


    /**
     * Handler function
     *
     * @param $path
     *
     * @return bool|string
     * @throws WalrusException
     */
    private function fmFilesize ($path)
    {
        if (!file_exists($path)) {
            throw new WalrusException('"file: ' . $path . ' didn\'t exist"');
        }

        $fileSize = filesize($path);

        if (!is_numeric($fileSize)) {
            throw new WalrusException('"An error occurred when tried to get filesize for file: ' . $path . '"');
        }

        return $fileSize;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return int
     * @throws WalrusException
     */
    private function fmFilemtime ($path)
    {
        if (!file_exists($path)) {
            throw new WalrusException('"file: ' . $path . ' didn\'t exist"');
        }

        $fileMTime = filemtime($path);

        if (!$fileMTime) {
            throw new WalrusException('"An error occured when tried to get filemtime for file: ' . $path . '"');
        }

        return $fileMTime;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return string
     * @throws WalrusException
     */
    private function fmBasename ($path)
    {
        if (!file_exists($path)) {
            throw new WalrusException('"file: ' . $path . ' didn\'t exist"');
        }

        return basename($path);
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return resource
     * @throws WalrusException
     */
    private function fmOpendir ($path)
    {
        if (!is_dir($path)) {
            throw new WalrusException('"file: ' . $path . ' need to be a folder"');
        }

        $stream = opendir($path);

        if (!$stream) {
            throw new WalrusException('"An error occured when tried to open the dir : ' . $path . '"');
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
     * @throws WalrusException
     */
    private function fmRmdir ($path)
    {
        if (!is_dir($path)) {
            throw new WalrusException('"file: ' . $path . ' need to be a folder"');
        }

        $rm = rmdir($path);

        if (!$rm) {
            throw new WalrusException('"An error occured when tried to delete dir: ' . $path . '"');
        }

        return $rm;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return bool
     * @throws WalrusException
     */
    private function fmUnlink ($path)
    {
        if (!is_file($path)) {
            throw new WalrusException('"file: ' . $path . ' need to be a valid file"');
        }

        $rm = unlink($path);

        if (!$rm) {
            throw new WalrusException('"An error occured when tried to delete file: ' . $path . '"');
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
     * @throws WalrusException
     */
    private function fmRename ($oldPath, $newPath)
    {
        if (!file_exists($oldPath)) {
            throw new WalrusException('"file: ' . $oldPath . ' need to be a valid file"');
        }

        if (file_exists($newPath)) {
            throw new WalrusException('"file: ' . $oldPath . ' already exist"');
        }

        $rename = rename($oldPath, $newPath);

        if (!$rename) {
            throw new WalrusException(
                '"An error occured when tried to rename file from: ' . $oldPath . ' to ' . $newPath . '"'
            );
        }

        return $rename;
    }

    /**
     * Handler function
     *
     * @param $path
     * @param $chmod
     *
     * @return bool
     * @throws WalrusException
     */
    private function fmMkdir ($path, $chmod = 0755)
    {
        if (file_exists($path)) {
            throw new WalrusException('file: "' . $path . '" already exist');
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $mkdir = mkdir($path);
        } else {
            $mkdir = mkdir($path, $chmod);
        }

        if (!$mkdir) {
            throw new WalrusException('An error occurred when tried to create folder: "' . $path . '"');
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
     * @throws WalrusException
     */
    private function fmMoveUploadedFile ($name, $destination)
    {
        $moved = move_uploaded_file($name, $destination);

        if (!$moved) {
            throw new WalrusException(
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
     * @throws WalrusException
     */
    private function fmFopen ($path, $param, $create = false)
    {
        if (!is_file($path) && $create === false) {
            throw new WalrusException('file: ' . $path . ' need to be a valid file');
        }

        $params = array('r', 'r+', 'rb', 'rb+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+');

        if (!in_array($param, $params)) {
            throw new WalrusException('Param: "' . $param . ' need to be a valid fopen parameter"');
        }

        $fopen = fopen($path, $param);

        if (!$fopen) {
            throw new WalrusException('An error occurred when tried to open file: "' . $path . '"');
        }

        return $fopen;
    }

    /**
     * Handler function
     *
     * @param $path
     *
     * @return array
     * @throws WalrusException
     */
    private function fmFile ($path)
    {
        $file = file($path);

        if (!$file) {
            throw new WalrusException('An error occurred when tried to read the file');
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
     * @throws WalrusException
     */
    private function fmFread ($stream, $size)
    {
        $fread = fread($stream, $size);

        if ($fread === false) {
            throw new WalrusException('An error occurred when tried to read file');
        }

        return $fread;
    }

    /**
     * Handler function
     *
     * @param $stream
     *
     * @return bool
     * @throws WalrusException
     */
    private function fmFclose ($stream)
    {
        $fclose = fclose($stream);

        if (!$fclose) {
            throw new WalrusException('An error occurred when tried to close file');
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
     * @throws WalrusException
     */
    private function fmWrite ($path, $content)
    {
        $fwrite = fwrite($path, $content);

        if ($fwrite === false) {
            throw new WalrusException('An error occurred when tried to write in file : "' . $path . '"');
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

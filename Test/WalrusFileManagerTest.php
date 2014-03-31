<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 23:47 06/03/14
 */

namespace Test;

use PHPUnit_Framework_TestCase;
use Walrus\core\WalrusFileManager;
use Walrus\core\WalrusException;

/**
 * Class WalrusFileManagerTest
 * @package Test
 */
class WalrusFileManagerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return WalrusFileManager
     */
    public function testInstance()
    {
        try {
            $filer = new WalrusFileManager($_ENV['W']['ROOT_PATH'] . 'Test\testbox');
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testInstance
     */
    public function testFileCreate(WalrusFileManager $filer)
    {
        try {
            $filer->fileCreate('test.txt');
            $elements = $filer->getElements();

            $this->assertEquals(1, count($elements));
            $this->assertEquals('test.txt', $elements[0]);
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testFileCreate
     */
    public function testFolderCreate(WalrusFileManager $filer)
    {
        try {
            $filer->folderCreate('test');
            $elements = $filer->getElements();
            $this->assertEquals(2, count($elements));
            $this->assertEquals('test', $elements[0]);
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testFolderCreate
     */
    public function testRenameCurrent(WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('test.txt');
            $filer->renameCurrent('test2.txt');

            $filer->setCurrentElem('test');
            $filer->renameCurrent('test2');

            $filer->setCurrentElem('');
            $elements = $filer->getElements();

            $this->assertEquals(2, count($elements));
            $this->assertEquals('test2', $elements[0]);
            $this->assertEquals('test2.txt', $elements[1]);
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testRenameCurrent
     */
    public function testMoveCurrent(WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('test2.txt');
            $filer->moveCurrent('test2');
            $filer->setCurrentElem('');

            $elements = $filer->getElements(true);
            $keys = array_keys($elements);

            $this->assertEquals(1, count($elements));
            $this->assertEquals('test2', $keys[0]);
            $this->assertEquals('test2.txt', $elements[$keys[0]][0]);
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testMoveCurrent
     */
    public function testFileDetail(WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('test2\test2.txt');
            $details = $filer->fileDetails();

            $this->assertEquals(0, $details['fileSize']);
            $this->assertEquals('test2.txt', $details['name']);
            $this->assertEquals(true, !empty($details['path']));
            $this->assertEquals(true, !empty($details['lastEdit']));

        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testFileDetail
     */
    public function testGetFolderTree (WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('');
            $filer->folderCreate('test');

            $filer->setCurrentElem('test2');
            $filer->folderCreate('test');

            $filer->setCurrentElem('');

            $elements = $filer->getFolderTree();

            $keys = array_keys($elements);
            $keys_2 = array_keys($elements[$keys[1]]);

            $this->assertEquals(2, count($elements));
            $this->assertEquals('test', $keys[0]);
            $this->assertEquals('test2', $keys[1]);
            $this->assertEquals('test', $keys_2[0]);
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testGetFolderTree
     */
    public function testChangeFileContent (WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('test2\test2.txt');
            $content = $filer->changeFileContent('Hello World!');

            $this->assertEquals('Hello World!', $content);
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testChangeFileContent
     */
    public function testAddFileContent (WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('test2\test2.txt');
            $content = $filer->addFileContent("\r\n" . 'second line!');

            $this->assertEquals('Hello World!' . "\r\n" . 'second line!', $content);
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testAddFileContent
     */
    public function testGetFileContent (WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('test2\test2.txt');
            $content = $filer->getFileContent('array');

            $this->assertEquals('Hello World!', trim($content[0]));
            $this->assertEquals('second line!', $content[1]);

            $content = $filer->changeFileContent('');
            $this->assertEquals('', $content);

            for ($i = 0; $i < 10; $i++) {
                $filer->addFileContent('New line ' . $i . '!' . "\r\n");
            }

            $content = $filer->getFileContent('array', 5, 7);

            $this->assertEquals('New line 6!', trim($content[0]));
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testGetFileContent
     */
    public function testDeleteCurrent (WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('test2\test2.txt');
            $filer->deleteCurrent();

            $this->assertEquals($_ENV['W']['ROOT_PATH'] . 'Test\testbox\\', $filer->getCurrentElem());

            $filer->setCurrentElem('test2');
            $elements = $filer->getElements();

            $this->assertEquals(1, count($elements));
            $this->assertEquals('test', $elements[0]);
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }

    /**
     * @depends testDeleteCurrent
     */
    public function testEmptyFolder (WalrusFileManager $filer)
    {
        try {
            $filer->setCurrentElem('test2');
            $filer->emptyFolder();

            $filer->setCurrentElem('');
            $filer->emptyFolder();

            $elements = $filer->getElements();
            $this->assertEquals(0, count($elements));
        } catch (WalrusException $exception) {
            $this->fail($exception->getMessage());
        }

        return $filer;
    }
}

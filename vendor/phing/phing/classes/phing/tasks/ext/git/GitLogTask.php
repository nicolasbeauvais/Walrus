<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */
 
require_once 'phing/Task.php';
require_once 'phing/tasks/ext/git/GitBaseTask.php';

/**
 * Wrapper aroung git-log
 *
 * @author Evan Kaufman <evan@digitalflophouse.com>
 * @author Victor Farazdagi <simple.square@gmail.com>
 * @version $Id$
 * @package phing.tasks.ext.git
 * @see VersionControl_Git
 * @since 2.4.5
 */
class GitLogTask extends GitBaseTask
{
    /**
     * Generate a diffstat. See --stat of git-log
     * @var string|boolean
     */
    private $stat = false;
    
    /**
     * Names + status of changed files. See --name-status of git-log
     * @var boolean
     */
    private $nameStatus = false;
    
    /**
     * Number of commits to show. See -<n>|-n|--max-count of git-log
     * @var integer
     */
    private $maxCount;
    
    /**
     * Don't show commits with more than one parent. See --no-merges of git-log
     * @var boolean
     */
    private $noMerges = false;
    
    /**
     * Commit format. See --format of git-log
     * @var string
     */
    private $format = 'medium';
    
    /**
     * Date format. See --date of git-log
     * @var string
     */
    private $date;
    
    /**
     * <since> argument to git-log
     * @var string
     */
    private $sinceCommit;

    /**
     * <until> argument to git-log
     * @var string
     */
    private $untilCommit = 'HEAD';
    
    /**
     * <path> arguments to git-log
     * Accepts one or more paths delimited by PATH_SEPARATOR
     * @var string
     */
    private $paths;
    
    /**
     * Property name to set with output value from git-log
     * @var string
     */
    private $outputProperty;
    
    /**
     * The main entry point for the task
     */
    public function main()
    {
        if (null === $this->getRepository()) {
            throw new BuildException('"repository" is required parameter');
        }

        $client = $this->getGitClient(false, $this->getRepository());
        $command = $client->getCommand('log');
        $command
            ->setOption('stat', $this->getStat())
            ->setOption('name-status', $this->isNameStatus())
            ->setOption('no-merges', $this->isNoMerges())
            ->setOption('format', $this->getFormat());
        
        if (null !== $this->getMaxCount()) {
            $command->setOption('max-count', $this->getMaxCount());
        }
        
        if (null !== $this->getDate()) {
            $command->setOption('date', $this->getDate());
        }

        if (null !== $this->getSince()) {
            $command->setOption('since', $this->getSince());
        }
        $command->setOption('until', $this->getUntil());

        $command->addDoubleDash(true);
        if (null !== $this->getPaths()) {
            $command->addDoubleDash(false);
            $paths = explode(PATH_SEPARATOR, $this->getPaths());
            foreach ($paths as $path) {
                $command->addArgument($path);
            }
        }

        $this->log('git-log command: ' . $command->createCommandString(), Project::MSG_INFO);

        try {
            $output = $command->execute();
        } catch (Exception $e) {
            throw new BuildException('Task execution failed', $e);
        }

        if (null !== $this->outputProperty) {
            $this->project->setProperty($this->outputProperty, $output);
        }

        $this->log(
            sprintf('git-log: commit log for "%s" repository', $this->getRepository()), 
            Project::MSG_INFO); 
        $this->log('git-log output: ' . trim($output), Project::MSG_INFO);
    }
    
    public function setStat($stat)
    {
        $this->stat = $stat;
    }
    
    public function getStat()
    {
        return $this->stat;
    }
    
    public function setNameStatus($flag)
    {
        $this->nameStatus = (boolean)$flag;
    }
    
    public function getNameStatus()
    {
        return $this->nameStatus;
    }
    
    public function isNameStatus()
    {
        return $this->getNameStatus();
    }
    
    public function setMaxCount($count)
    {
        $this->maxCount = (int)$count;
    }
    
    public function getMaxCount()
    {
        return $this->maxCount;
    }
    
    public function setNoMerges($flag)
    {
        $this->noMerges = (bool)$flag;
    }
    
    public function getNoMerges()
    {
        return $this->noMerges;
    }
    
    public function isNoMerges()
    {
        return $this->getNoMerges();
    }
    
    public function setFormat($format)
    {
        $this->format = $format;
    }
    
    public function getFormat()
    {
        return $this->format;
    }
    
    public function setDate($date)
    {
        $this->date = $date;
    }
    
    public function getDate()
    {
        return $this->date;
    }
    
    public function setSince($since)
    {
        $this->sinceCommit = $since;
    }

    public function getSince()
    {
        return $this->sinceCommit;
    }

    public function setAfter($after)
    {
        $this->setSince($after);
    }

    public function setUntil($until)
    {
        $this->untilCommit = $until;
    }

    public function getUntil()
    {
        return $this->untilCommit;
    }

    public function setBefore($before)
    {
        $this->setUntil($before);
    }

    public function setPaths($paths)
    {
        $this->paths = $paths;
    }
    
    public function getPaths()
    {
        return $this->paths;
    }
    
    public function setOutputProperty($prop)
    {
        $this->outputProperty = $prop;
    }

}

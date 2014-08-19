<?php
/*
 * This file is part of StaticReview
 *
 * Copyright (c) 2014 Samuel Parkinson <@samparkinson_>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/sjparkinson/static-review/blob/master/LICENSE.md
 */

namespace StaticReview\VersionControl;

use StaticReview\VersionControl\VersionControlInterface;
use StaticReview\File\FileInterface;
use StaticReview\File\File;
use StaticReview\Collection\FileCollection;

use Symfony\Component\Process\Process;

class GitVersionControl implements VersionControlInterface
{
    const CACHE_DIR = '/sjparkinson.static-review/cached/';

    /**
     * Gets a list of the files currently staged under git.
     *
     * Returns either an empty array or a tab separated list of staged files and
     * their git status.
     *47
     * @link http://git-scm.com/docs/git-status
     *
     * @return FileCollection
     */
    public function getStagedFiles()
    {
        $base = $this->getBaseProjectPath();

        $files = new FileCollection();

        foreach($this->getModifiedFiles() as $line) {

            list($status, $relativePath) = explode("\t", $line);

            $path = $base . '/' . $relativePath;

            $file = new File($status, $path, $base);

            $this->saveFileToCache($file);

            $files->append($file);
        }

        return $files;
    }

    /**
     * Gets a list of the staged files from git.
     *
     * @return string[]
     */
    private function getModifiedFiles()
    {
        $process = new Process('git diff --cached --name-status --diff-filter=ACMR');
        $process->run();

        $raw = explode(PHP_EOL, $process->getOutput());

        return array_filter($raw);
    }

    /**
     * Gets the base path for the git project.
     *
     * @return string
     */
    private function getBaseProjectPath()
    {
        $process = new Process('git rev-parse --show-toplevel');
        $process->run();

        return trim($process->getOutput());
    }

    /**
     * Saves a copy of the cached version of the given file to a temp directory.
     *
     * @param FileInterface $file
     * @return FileInterface
     */
    private function saveFileToCache(FileInterface $file)
    {
        $cachedPath = sys_get_temp_dir() . self::CACHE_DIR . $file->getRelativePath();

        if (! is_dir(dirname($cachedPath))) {
            mkdir(dirname($cachedPath), 0700, true);
        }

        $cmd = sprintf('git show :%s > %s', $file->getRelativePath(), $cachedPath);
        $process = new Process($cmd);
        $process->run();

        $file->setCachedPath($cachedPath);

        return $file;
    }
}

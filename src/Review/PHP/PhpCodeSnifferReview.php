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

namespace StaticReview\Review\PHP;

use StaticReview\File\FileInterface;
use StaticReview\Reporter\ReporterInterface;
use StaticReview\Review\AbstractReview;

class PhpCodeSnifferReview extends AbstractReview
{
    protected $standard;

    /**
     * Sets the standard to use when reviewing with PHP_CodeSniffer.
     *
     * @param string $standard
     * @return PhpCodeSnifferReview
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;

        return $this;
    }

    /**
     * Determins if a given file should be reviewed.
     *
     * @param FileInterface $file
     * @return bool
     */
    public function canReview(FileInterface $file)
    {
        $mime = $file->getMimeType();

        // check to see if the mime-type contains 'php'
        return (strpos($mime, 'php') !== false);
    }

    /**
     * Checks PHP files using PHP_CodeSniffer.
     */
    public function review(ReporterInterface $reporter, FileInterface $file)
    {
        $cmd = sprintf('vendor/bin/phpcs %s --report=json', $file->getFullPath());

        if ($this->standard) {
            $cmd .= sprintf(' --standard=%s', $this->standard);
        }

        $process = $this->getProcess($cmd);
        $process->run();

        // Create the array of outputs and remove empty values.
        $output = array_filter(explode(PHP_EOL, $process->getOutput()));

        if ($process->getExitCode() === 2) {

            foreach ($output as $error) {
                $reporter->warning($error, $this, $file);
            }

        }
    }
}

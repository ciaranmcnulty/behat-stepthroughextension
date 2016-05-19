<?php

namespace Cjm\Behat\StepThroughExtension\Pauser;

use Symfony\Component\Console\Output\OutputInterface;

final class CliPauser implements Pauser
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var resource
     */
    private $inputStream;

    /**
     * @var boolean
     */
    private $isActive = false;

    public function __construct(OutputInterface $output, $inputStream = null)
    {
        $this->output = $output;
        $this->inputStream = $inputStream ?: STDIN;
    }

    public function pause($stepText)
    {
        if (!$this->isActive) {
            return;
        }

        $this->output->write(sprintf('  [Paused after "%s" - press enter to continue]', $stepText));

        fgets($this->inputStream, 1024);
    }

    public function activate()
    {
        $this->isActive = true;
    }
}

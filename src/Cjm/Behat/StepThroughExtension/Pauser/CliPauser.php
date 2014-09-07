<?php

namespace Cjm\Behat\StepThroughExtension\Pauser;

use Behat\Testwork\Output\Printer\OutputPrinter;

final class CliPauser implements Pauser
{
    /**
     * @var OutputPrinter
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

    public function __construct(OutputPrinter $output, $inputStream = null)
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

<?php

namespace Cjm\Behat\StepThroughExtension\Pauser;

use Symfony\Component\Console\Output\OutputInterface;
use Behat\Testwork\Tester\Result\TestResult;

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


    private $isActiveOnFail = false;

    public function __construct(OutputInterface $output, $inputStream = null)
    {
        $this->output = $output;
        $this->inputStream = $inputStream ?: STDIN;
    }

    public function pause($stepText, $resultCode)
    {
        if (!$this->isActive && (!$this->isActiveOnFail || $resultCode != TestResult::FAILED)) {
            return;
        }

        $this->output->write(sprintf('  [Paused after "%s" - press enter to continue]', $stepText));

        fgets($this->inputStream, 1024);
    }

    public function activate()
    {
        $this->isActive = true;
    }

    public function activateOnFail()
    {
        $this->isActiveOnFail = true;
    }
}

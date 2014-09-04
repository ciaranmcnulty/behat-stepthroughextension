<?php

namespace Cjm\Behat\StepThroughExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Cjm\Behat\StepThroughExtension\Pauser\Pauser;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class StepThroughController implements Controller
{
    /**
     * @var \Cjm\Behat\StepThroughExtension\Pauser\Pauser
     */
    private $pauser;

    /**
     * @param Pauser $pauser
     */
    public function __construct(Pauser $pauser)
    {
        $this->pauser = $pauser;
    }

    /**
     * Configures command to be executable by the controller.
     *
     * @param SymfonyCommand $command
     */
    public function configure(SymfonyCommand $command)
    {
        $command->addOption('--step-through', null, InputOption::VALUE_NONE, 'Pause after every step to aid debugging');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('step-through') && $input->isInteractive()) {
            $this->pauser->activate();
        }
    }
}

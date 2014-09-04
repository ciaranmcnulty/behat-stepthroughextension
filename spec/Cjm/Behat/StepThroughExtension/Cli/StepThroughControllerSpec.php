<?php

namespace spec\Cjm\Behat\StepThroughExtension\Cli;

use Cjm\Behat\StepThroughExtension\Pauser\Pauser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class StepThroughControllerSpec extends ObjectBehavior
{
    function let(Pauser $pauser)
    {
        $this->beconstructedWith($pauser);
    }

    function it_is_a_behat_controller()
    {
        $this->shouldHaveType('Behat\Testwork\Cli\Controller');
    }

    function it_adds_a_new_option_to_the_command(Command $command)
    {
        $this->configure($command);

        $command->addOption('--step-through', null, InputOption::VALUE_NONE, Argument::type('string'))->shouldHaveBeenCalled();
    }

    function it_activates_the_pauser(InputInterface $input, OutputInterface $output, Pauser $pauser)
    {
        $input->getOption('step-through')->willReturn(true);
        $input->isInteractive()->willReturn(true);

        $this->execute($input, $output);

        $pauser->activate()->shouldHaveBeenCalled();
    }

    function it_does_not_activate_the_pauser_if_option_is_not_set(InputInterface $input, OutputInterface $output, Pauser $pauser)
    {
        $input->getOption('step-through')->willReturn(false);
        $input->isInteractive()->willReturn(true);

        $this->execute($input, $output);

        $pauser->activate()->shouldNotHaveBeenCalled();
    }

    function it_does_not_activate_the_pauser_if_input_is_not_interactive(InputInterface $input, OutputInterface $output, Pauser $pauser)
    {
        $input->getOption('step-through')->willReturn(true);
        $input->isInteractive()->willReturn(false);

        $this->execute($input, $output);

        $pauser->activate()->shouldNotHaveBeenCalled();
    }
}

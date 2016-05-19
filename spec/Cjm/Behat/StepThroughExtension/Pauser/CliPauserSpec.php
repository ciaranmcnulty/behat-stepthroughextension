<?php

namespace spec\Cjm\Behat\StepThroughExtension\Pauser;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;

class CliPauserSpec extends ObjectBehavior
{
    /**
     * @var resource
     */
    private $inputStream;

    function let(OutputInterface $output)
    {
        $this->inputStream = fopen('php://memory', 'r+', false);
        $this->beConstructedWith($output, $this->inputStream);
    }

    function it_is_a_prompter()
    {
        $this->shouldHaveType('Cjm\Behat\StepThroughExtension\Pauser\Pauser');
    }

    function it_does_not_show_a_message_if_it_has_not_been_activated(OutputInterface $output)
    {
        $this->pause('step name');

        $output->write(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_shows_message_when_pause_is_called(OutputInterface $output)
    {
        fputs($this->inputStream, "Y\n");
        rewind($this->inputStream);

        $this->activate();
        $this->pause('step name');

        $output->write(Argument::containingString('step name'))->shouldHaveBeenCalled();
    }
}

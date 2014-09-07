<?php

namespace spec\Cjm\Behat\StepThroughExtension\Pauser;

use Behat\Testwork\Output\Printer\OutputPrinter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CliPauserSpec extends ObjectBehavior
{
    /**
     * @var resource
     */
    private $inputStream;

    function let(OutputPrinter $output)
    {
        $this->inputStream = fopen('php://memory', 'r+', false);
        $this->beConstructedWith($output, $this->inputStream);
    }

    function it_is_a_prompter()
    {
        $this->shouldHaveType('Cjm\Behat\StepThroughExtension\Pauser\Pauser');
    }

    function it_does_not_show_a_message_if_it_has_not_been_activated(OutputPrinter $output)
    {
        $this->pause('step name');

        $output->writeln(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_shows_message_when_pause_is_called(OutputPrinter $output)
    {
        fputs($this->inputStream, "Y\n");
        rewind($this->inputStream);

        $this->activate();
        $this->pause('step name');

        $output->writeln(Argument::containingString('step name'))->shouldHaveBeenCalled();
    }
}

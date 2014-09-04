<?php

namespace spec\Cjm\Behat\StepThroughExtension\ServiceContainer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class StepThroughExtensionSpec extends ObjectBehavior
{
    function it_is_a_behat_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_reports_a_configuration_key()
    {
        $this->getConfigKey()->shouldReturn('stepthrough');
    }
}

<?php

namespace spec\Cjm\Behat\StepThroughExtension\Listener;

use Behat\Behat\EventDispatcher\Event\StepTested;
use Cjm\Behat\StepThroughExtension\Pauser\Pauser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PausingListenerSpec extends ObjectBehavior
{
    function let(Pauser $pauser)
    {
        $this->beConstructedWith($pauser);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_listens_to_before_step_events()
    {
        $this->getSubscribedEvents()->shouldHaveKey('tester.step_tested.after');
    }

    function it_asks_when_event_is_triggered(Pauser $pauser, StepTested $event)
    {
        $this->pauseAfterStep($event);

        $pauser->pause()->shouldHaveBeenCalled();
    }
}

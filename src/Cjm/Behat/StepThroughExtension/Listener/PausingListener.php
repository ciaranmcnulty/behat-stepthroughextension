<?php

namespace Cjm\Behat\StepThroughExtension\Listener;

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Testwork\Tester\Result\TestResult;
use Cjm\Behat\StepThroughExtension\Pauser\Pauser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PausingListener implements EventSubscriberInterface
{
    /**
     * @var Pauser
     */
    private $pauser;

    /**
     * @param Pauser $pauser
     */
    public function __construct(Pauser $pauser)
    {
        $this->pauser = $pauser;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'tester.step_tested.after' => array('pauseAfterStep', 10000)
        );
    }

    public function pauseAfterStep(AfterStepTested $event)
    {
        if (in_array($event->getTestResult()->getResultCode(), array(TestResult::PENDING, TestResult::SKIPPED))) {
            return;
        }
        $this->pauser->pause();
    }
}

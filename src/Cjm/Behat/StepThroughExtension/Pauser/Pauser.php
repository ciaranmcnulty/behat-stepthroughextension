<?php

namespace Cjm\Behat\StepThroughExtension\Pauser;

interface Pauser
{
    /**
     * Pause (block) until some further action is taken
     */
    public function pause();

    /**
     * Enable the pauser
     */
    public function activate();
} 
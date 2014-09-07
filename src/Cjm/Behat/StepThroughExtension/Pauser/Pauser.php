<?php

namespace Cjm\Behat\StepThroughExtension\Pauser;

interface Pauser
{
    /**
     * Pause (block) until some further action is taken
     *
     * @param string $stepText
     */
    public function pause($stepText);

    /**
     * Enable the pauser
     */
    public function activate();
} 
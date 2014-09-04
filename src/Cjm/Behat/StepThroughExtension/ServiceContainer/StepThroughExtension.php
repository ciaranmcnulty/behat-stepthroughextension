<?php

namespace Cjm\Behat\StepThroughExtension\ServiceContainer;

use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class StepThroughExtension implements Extension
{
    const PAUSER_ID = 'stepthroughextension.pauser';

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'stepthrough';
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadPauser($container);
        $this->loadController($container);
        $this->loadListener($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadPauser(ContainerBuilder $container)
    {
        $definition = new Definition(
            'Cjm\Behat\StepThroughExtension\Pauser\CliPauser',
             array(new Definition('Behat\Behat\Output\Printer\ConsoleOutputPrinter'))
        );
        $container->setDefinition(self::PAUSER_ID , $definition);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadController(ContainerBuilder $container)
    {
        $definition = new Definition(
            'Cjm\Behat\StepThroughExtension\Cli\StepThroughController',
            array(new Reference(self::PAUSER_ID))
        );
        $definition->addTag(CliExtension::CONTROLLER_TAG, array('priority' => 0));
        $container->setDefinition(CliExtension::CONTROLLER_TAG . '.stepthrough', $definition);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadListener(ContainerBuilder $container)
    {
        $definition = new Definition('Cjm\Behat\StepThroughExtension\Listener\PausingListener', array(
            new Reference(self::PAUSER_ID)
        ));
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition(EventDispatcherExtension::SUBSCRIBER_TAG . '.stepthrough.pausing', $definition);
    }
}

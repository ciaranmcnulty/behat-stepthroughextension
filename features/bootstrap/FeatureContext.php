<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Behat context class.
 */
class FeatureContext implements SnippetAcceptingContext
{
    private $filesystem;
    private $process;
    private $workingDirectory;
    private $input;
    private $option;
    const OUTPUT_TIMEOUT = 5;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @beforeScenario
     */
    public function createWorkingDirectory()
    {
        $this->workingDirectory = tempnam(sys_get_temp_dir(), 'behat-stepthrough');
        $this->filesystem->remove($this->workingDirectory);
        $this->filesystem->mkdir($this->workingDirectory . '/features/bootstrap', 0777);
    }

    /**
     * @afterScenario
     */
    public function clearWorkingDirectory()
    {
        $this->filesystem->remove($this->workingDirectory);
    }

    /**
     * @beforeScenario
     */
    public function createProcess()
    {
        $this->process = new Process(null);
    }

    /**
     * @afterScenario
     */
    public function stopProcessIfRunning()
    {
        if ($this->process->isRunning()) {
            $this->process->stop(10);
        }
    }

    /**
     * @Given I have the configuration:
     */
    public function iHaveTheConfiguration(PyStringNode $config)
    {
        $this->filesystem->dumpFile(
            $this->workingDirectory.'/behat.yml',
            $config->getRaw()
        );
    }

    /**
     * @Given I have the feature:
     */
    public function iHaveTheFeature(PyStringNode $content)
    {
        $this->filesystem->dumpFile(
            $this->workingDirectory.'/features/feature.feature',
            $content->getRaw()
        );
    }

    /**
     * @Given I have the context:
     */
    public function iHaveTheContext(PyStringNode $definition)
    {
        $this->filesystem->dumpFile(
            $this->workingDirectory.'/features/bootstrap/FeatureContext.php',
            $definition->getRaw()
        );
    }

    /**
     * @When I run behat with the :option option
     */
    public function iRunBehatWithTheOption($option)
    {
        $this->option = $option;
    }

    /**
     * @When I press enter
     * @When I press enter :times times
     */
    public function iPressEnter($times=1)
    {
        $this->input = str_repeat("\n", (int)$times);
    }

    /**
     * @Then I should see:
     */
    public function iShouldSee(PyStringNode $expected)
    {
        $this->runBehat();

        $start = microtime(true);
        while (!$this->outputMatches($expected->getRaw())) {
            if (!$this->process->isRunning() || microtime(true)-$start > self::OUTPUT_TIMEOUT) {
                throw new RuntimeException(
                    sprintf(
                        'Did not get output after expected time. Actual: "%s"',
                        $this->process->getoutput()
                    )
                );
            }
            usleep(50);
        }
    }

    private function outputMatches($expected)
    {
        $output = $this->normaliseOutput($this->process->getOutput());
        $expected = $this->normaliseOutput($expected);

        if (!$output) {
            return false;
        }

        return $output==$expected;
    }

    /**
     * @Then The process should not have ended
     */
    public function theProcessShouldNotHaveEnded()
    {
        if (!$this->process->isRunning()) {
            throw new RuntimeException('Process unexpectedly stopped');
        }
    }

    /**
     * @Then The process should have ended
     */
    public function theProcessShouldHaveEnded()
    {
        if ($this->process->isRunning()) {
            throw new RuntimeException('Process has not stopped');
        }
    }

    /**
     * @todo test on other platforms
     */
    private function wrapWithPty($command)
    {
        if (`which script`) {
            $usage = `script --help 2>&1`;

            // bsd style
            if (preg_match('/file.*command/', $usage)) {
                return sprintf('exec script -q /dev/null %s', $command);
            }
        }

        throw new \RuntimeException('Can not execute child process as PTY on this platform');
    }

    private function runBehat()
    {
        $phpFinder = new PhpExecutableFinder();
        $phpBin = $phpFinder->find();

        $this->process->setWorkingDirectory($this->workingDirectory);
        $this->process->setCommandLine($this->wrapWithPty(sprintf(
            '%s %s %s --no-colors',
            $phpBin,
            escapeshellarg(BEHAT_BIN_PATH),
            $this->option
        )).' 2>&1');
        $this->process->setInput($this->input);
        $this->process->setPty(true);

        $this->process->start();
    }

    /**
     * @param string
     * @return string
     */
    private function normaliseOutput($output)
    {
        return trim(preg_replace('/[0-9ms.]+ \([0-9.GkMb]+\)/', 'TIME', preg_replace('/\\s+/', ' ', $output)));
    }
}

Behat-StepThroughExtension helps you debug Behat scenarios by stopping execution in between steps.

This allows you to do things like observe DB state, look at the pages open in your browser automation suite, examine files created on disks etc., while still being able to resume scenario execution and let any post-scenario cleanup hooks run.

Usage
-----

When debugging a particular scenario, use the `--step-through` flag at the cli:

```bash
bin/behat --step-through features/my-failing-feature
```

After each step you will see the message `[Paused after "<step text>" - press enter to continue]`. The Behat test suite will stay in this suspended state until a carriage return is received, to allow you to do any inspections necessary.

Note: Execution will also pause after each step of a Background, but depending on the formatter in use there may not be any other indication that a Background step has been run.

Installation
------------

Install by adding to your `composer.json`:

```json
{
     "require-dev": {
         "ciaranmcnulty/behat-stepthroughextension" : "dev-master"
     }
 }
```

Configuration
-------------

There are no configuration parameters, just enable the extension in `behat.yml`

```yml
default:
  extensions:
    Cjm\Behat\StepThroughExtension: ~
```

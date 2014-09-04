Behat-StepThroughExtension helps you debug Behat scenarios by stopping execution in between steps.

This allows you to do things like observe DB state, inspect browser state, etc. while still being able to resume scenario execution (and post-scenario cleanup)

Configuration
-------------

There are no configuration parameters, just enable the extension in `behat.yml`

```yml
default:
  extensions:
    Cjm\Behat\StepThroughExtension: ~
```

Usage
-----

When debugging a particular scenario, use the `--step-through` flag at the cli:

```bash
bin/behat --step-through features/my-failing-feature
```

After each step you will see the message `[Paused: press enter to continue]`. Until you do this the suite execution is paused
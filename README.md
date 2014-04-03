# ponsole

this is a pre-alpha version of a PHP command line wrapper. It uses symfony/console
to combine a number of different commands into a single utility.

If you want to build your own, command, you can easily do that

```php
<?php

namespace Test;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ponsole\CommandInterface;
use Ponsole\BaseCommand;

class CmdTestCommand extends BaseCommand implements CommandInterface {

    public function configure() {
        $this
                ->setName('cmd:test')
                ->addArgument('name', InputArgument::REQUIRED, 'a name')                
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument('name');
        echo $name;
    }

}
```

Once you've created this command, you'll have to register it. To do that, append
your class to the array found in ```commands.php```, make sure you use the complete
namespace, in the example above, it would be ```Test\\CmdTestCommand```. 
<?php
/**
 * Date: 15/10/26
 * Time: 11:23
 */

namespace ReFUEL4\commands;


use Scruit\Runnable;

abstract class BaseIntegrationTest implements Runnable
{
    public function getName()
    {
        return 'integration-test';
    }

    public function run($args)
    {
        $userId = $args['userId'];
        $result = array();
        system("php scruit change-env 'env=it'");
        system("php scruit migrate");
        system("php scruit load");
        system("php scruit gen-token 'userId=$userId'");

        foreach ($this->getTestCommands() as $key => $value) {
            system($value, $tmp);
            if ($tmp)  $result[] = $key;
        }
        system("php scruit change-env 'env=dev'");
        system("php scruit gen-token 'userId=$userId'");
        if ($result) {
            throw new \RuntimeException('<< integration tests failed >>' . "\n" . implode("\n", array_map(function ($element) {
               return 'failed:' . $element;
            }, $result)));
        }
    }

    public abstract function getTestCommands();
    public function doc()
    {?>
run integration test
<?php
    }
}
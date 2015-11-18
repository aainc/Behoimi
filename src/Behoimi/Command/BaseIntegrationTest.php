<?php
namespace Behoimi\Command;
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
        if (!preg_match('#^\d+$#', $userId)) {
            throw new \InvalidArgumentException('invalid user Id');
        }

        $result = array();
        foreach ($this->getSetupCommands($args) as $value) {
            system($value);
        }
        system("php scruit gen-token 'userId=$userId'");

        foreach ($this->getTestCommands() as $key => $value) {
            system($value, $tmp);
            if ($tmp)  $result[] = $key;
        }

        foreach ($this->getTeardownCommands($args) as $value) {
            system($value);
        }
        system("php scruit gen-token 'userId=$userId'");
        if ($result) {
            throw new \RuntimeException('<< integration tests failed >>' . "\n" . implode("\n", array_map(function ($element) {
               return 'failed:' . $element;
            }, $result)));
        }
    }

    public function getSetupCommands($args)
    {
        $env = 'it';
        if (isset($args['testEnv'])) {
            if (!preg_match('#^[0-9A-Za-z_-]+$#', $args['testEnv'])) {
                throw new \InvalidArgumentException('testEnv is invalid');
            }
            $env = $args['testEnv'];
        }

        return array (
            "php scruit change-env 'env=$env'",
            "php scruit migrate",
            "php scruit load",
        );
    }

    public function getTeardownCommands($args)
    {
        $env = 'dev';
        if (isset($args['devEnv'])) {
            if (!preg_match('#^[0-9A-Za-z_-]+$#', $args['devEnv'])) {
                throw new \InvalidArgumentException('devEnv is invalid');
            }
            $env = $args['devEnv'];
        }
        return array (
            "php scruit change-env 'env=$env'",
        );
    }

    public abstract function getTestCommands();
    public function doc()
    {?>
run integration test
<?php
    }
}
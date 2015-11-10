<?php
namespace ReFUEL4\commands;


use Hoimi\ArrayContainer;
use Loula\OAuthClient;
use Scruit\Runnable;

abstract class BaseClientCommand implements Runnable
{
    const SKIP_TEST = 'SKIP';
    public function run($args)
    {
        if (is_file('.accessToken')) {
            $tmp = require '.accessToken';
            $args['accessToken']  = $tmp['accessToken'];
            $args['refreshToken'] = $tmp['refreshToken'];
        }
        if (!isset($args['accessToken']) || !isset($args['refreshToken'])) {
            print "need accessToken, need refreshToken";
            return ;
        }
        ini_set('xdebug.overload_var_dump', 0);
        ini_set('xdebug.var_display_max_children', -1);
        ini_set('xdebug.var_display_max_data', -1);
        ini_set('xdebug.var_display_max_depth', -1);

        $validationResult = \Hoimi\Validator::validate(new ArrayContainer($args), $this->getValidatorDefinitions());
        if (!$validationResult) {
            try {
                $before = microtime();
                $client = $this->createClient($args['accessToken'], $args['refreshToken'], array(new AccessTokenPrinter()));
                $result = $this->operation($args, $client);
                $after  = microtime();
                print "<<" . (($after - $before) * 1000) . ">>\n";
                if (isset($args['integration'])) {
                    $expected = $this->expected();
                    if ((!is_callable($expected) && $this->expected() == $result) || (is_callable($expected) && $expected($result))) {
                        print get_class($this) . ":OK\n";
                    } elseif ($this->expected() !== self::SKIP_TEST) {
                        print get_class($this) . ":NG\n";
                        if (!is_callable($this->expected())) {
                            error_log("<<expected>>\n");
                            error_log(var_export($this->expected(), true));
                            error_log("\n\n<<actual>>\n");
                            error_log(var_export($result, true));
                            error_log("\n\n");
                        } else {
                            error_log(var_export($result, true));
                        }
                        throw new \RuntimeException(get_class($this) . ' test:failed');
                    } else {
                        print get_class($this) . ":skipped\n";
                    }
                } else {
                    var_dump($result);
                }
            }  catch (\Loula\Exception $e) {
                print "<< Request Error>>\n";
                print $e->getStatus() . "\n";
                print "<< header \n";
                print $e->getHeader() . "\n";
                print "<< body \n";
                print $e->getBody() . "\n";
                print "<< request \n";
                var_dump($e->getRequest());
                throw $e;
            }
        } else {
            print "<< ValidationError >>\n";
            var_dump($validationResult);
        }
    }

    abstract public function expected() ;
    abstract public function getValidatorDefinitions();
    abstract public function operation ($args, OAuthClient $client);

    /**
     *
     * @param $accessToken
     * @param $refreshToken
     * @param $observers
     * @return \Loula\OAuthClient
     */
    abstract public function createClient ($accessToken, $refreshToken, $observers);
}
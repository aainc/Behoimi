<?php
namespace ReFUEL4\commands;


use Behoimi\Dao\AccessTokensDao;
use Behoimi\Dao\ApplicationsDao;
use Behoimi\Dao\AuthorizedApplicationsDao;
use Behoimi\Dao\ScopesDao;

abstract class AccessTokenGenerator implements \Scruit\Runnable {
    public function getName()
    {
        return 'gen-token';
    }

    public function run($args)
    {
        if (!isset($args['userId'])) {
            throw new \InvalidArgumentException('userId is not required');
        }
        $diContainer = \Zaolik\DIContainer::getInstance();
        $databaseSession = $diContainer->getFlyWeight('DatabaseSession');
        $applicationsDao = new ApplicationsDao($databaseSession);
        $authorizedApplicationDao = new AuthorizedApplicationsDao($databaseSession);
        $scopesDao = new ScopesDao($databaseSession);
        $accessTokensDao = new AccessTokensDao($databaseSession);
        $application = $applicationsDao->find(isset($args['appId']) ? $args['appId'] : 1);
        if (!$application) {
            $application = new \stdClass();
            $application->name = 'test';
            $application->client_id = 'test';
            $application->client_secret = 'test';
            $application->redirect_uri = 'https://test.test.test/code_at';
            $application->withdraw_uri = 'https://test.test.test/withdraw_at';
            $applicationsDao->save($application);
        }
        $authorize = $authorizedApplicationDao->findByAppIdAndUserId($application->id, $args['userId']);
        if (!$authorize) {
            $authorize = new \stdClass();
            $authorize->user_id = $args['userId'];
            $authorize->application_id = $application->id;
            $authorize->running = 1;
            $authorizedApplicationDao->save($authorize);
        }

        $scopes = $scopesDao->findByAuthorizedAppid($authorize->id);
        if (!$scopes) {
            foreach ($this->getScopes() as $scope) {
                $obj = new \stdClass();
                $obj->authorized_application_id = $authorize->id;
                $obj->scope = $scope;
                $scopesDao->save($obj);
            }
        }

        $accessToken = (object)array(
            'authorized_application_id' => $authorize->id,
            'access_token' => bin2hex(openssl_random_pseudo_bytes(25)),
            'refresh_token' => bin2hex(openssl_random_pseudo_bytes(25)),
            'created_at' => time(),
        );
        $accessTokensDao->save($accessToken);
        print "access_token:  $accessToken->access_token\n";
        print "refresh_token: $accessToken->refresh_token\n";
        file_put_contents('.accessToken', '<?php return ' . var_export(array(
            'accessToken'  => $accessToken->access_token,
            'refreshToken'  => $accessToken->refresh_token,
        ), true) . ';');
    }

    abstract public function getScopes();

    public function doc()
    {?>
generate accessToken
<?php
    }
}
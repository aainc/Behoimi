# Behoimi

## description

Behoimi is "Hoimi Plugin" that append functions as OAuth Server.

## Requirements

- PHP 5.6
- MySQL
- Hoimi
- Zaolik
- Mahotora
- Scruit
- Loula

## QuickStart

### run sql

```
CREATE TABLE `applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `client_secret` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `withdraw_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `applications_client_id_unique` (`client_id`),
  UNIQUE KEY `applications_client_secret_unique` (`client_secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `authorized_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `application_id` bigint(20) unsigned NOT NULL,
  `running` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authorized_applications_user_id_application_id_unique` (`user_id`,`application_id`),
  KEY `authorized_applications_user_id_index` (`user_id`),
  KEY `authorized_applications_application_id_index` (`application_id`),
  CONSTRAINT `authorized_applications_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `authorization_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `authorized_application_id` bigint(20) unsigned NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `expired_at` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authorization_codes_code_unique` (`code`),
  KEY `authorization_codes_authorized_application_id_index` (`authorized_application_id`),
  KEY `authorization_codes_expired_at_index` (`expired_at`),
  CONSTRAINT `authorization_codes_authorized_application_id_foreign` FOREIGN KEY (`authorized_application_id`) REFERENCES `authorized_applications` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `access_token_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `authorized_application_id` bigint(20) unsigned NOT NULL,
  `action_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `method_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logs` text COLLATE utf8_unicode_ci,
  `created_at` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `access_token_logs_authorized_application_id_index` (`authorized_application_id`),
  CONSTRAINT `access_token_logs_authorized_application_id_foreign` FOREIGN KEY (`authorized_application_id`) REFERENCES `authorized_applications` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `authorized_application_id` bigint(20) unsigned NOT NULL,
  `access_token` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `refresh_token` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_tokens_access_token_unique` (`access_token`),
  UNIQUE KEY `access_tokens_refresh_token_unique` (`refresh_token`),
  KEY `access_tokens_authorized_application_id_index` (`authorized_application_id`),
  CONSTRAINT `access_tokens_authorized_application_id_foreign` FOREIGN KEY (`authorized_application_id`) REFERENCES `authorized_applications` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `scopes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `authorized_application_id` bigint(20) NOT NULL,
  `scope` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

### add routing settings

```
return new Router(
    '/oauth/exchange_token' => '\Behoimi\Action\ExchangeToken',
    '/oauth/refresh_token' => '\Behoimi\Action\ExchangeToken',
    // urls
    ...
    ...
    ...
);
```

### Action extending BaseOAuthAction

```
class SomeAction extends BaseOAuthAction
{
   // plz return a scope is needed for running "get method"
   public function getGetScope()
   {
        return Scopes::R_PROFILE;
   }

   public function get()
   {
        // getting userId from accessToken
        // checking access_token( validate expired, validate scope) when call this method.
        $userId = $this->getAccessToken()->getUserId();
        $user = $this->getUsersDao()->find($userId);
        if ($user === null) {
            throw new NotFoundException();
        }
        return new EntityResult(true, $user, null);
   }
}
```

### creating client

```
class SampleClient extends OAuthClient {
    public function getEndPoint()
    {
        return 'https://behoimi.local';
    }

    public function exchangeCode ($code)
    {
        $result = $this->get('/oauth/exchange_code', array('code' => $code));
        $this->accessToken = $result->data->entity->access_token;
        $this->refreshToken = $result->data->entity->refresh_token;
    }

    public function refreshAccessToken ()
    {
        $result = $this->get('/oauth/refresh_token', array('refresh_token' => $this->refreshToken));
        $this->accessToken = $result->data->entity->access_token;
        $this->refreshToken = $result->data->entity->refresh_token;
    }

}
```

### creating integration tests

see also Scruit.
you need to understand this section then you know about Scruit framework.

#### mkdir ./datas

Behoimi integration test use scruit migration and scruit loader.
create ./datas and put csv files of sample data to here.

#### mkdir src/app/resources/it

Behoimi integration test use scruit change-env.
create src/app/resources/it and put database variables and some environment variables to here.

#### create test commands.

this is scruit command.
you can use this command for develop and integration tests.


```
class SampleCommand extend BaseClientCommand
{
    public function getName()
    {
        return 'me-test';
    }
    public function doc()
    {?>
this is document about this command.
explain description, arguments, and else.
<?php
    }

    public function expected ()
    {
        return (object)array(
            //
            // expected data on executing command on test data.
            //
            'data' => (object)array(
                'result'  => true,
                'entity' => (object)array(
                    'name'  => 't_ishida',
                    'gender' => 'm'
                ),
                'error' => null,
            ),
        );
    }

    public function getValidatorDefinitions()
    {
        //
        // this is Hoimi\Validator's definitions.
        // BaseClass validate command line parameters(scruit style) by this.
        //
        return array();
    }

    public function createClient ($accessToken, $refreshToken)
    {
        return new SampleClient($accessToken, $refreshToken);
    }

    public function operation ($args, OAuthClient $client)
    {
        // operation
        return $client->get('/@me');
    }
}
```

#### create integration test.

this is scruit command for integration tests.
this is facade for bundling test commands.
this is return error code on failed. you can use this command on CI system(ex:jenkins).
```
class SampleTest extends BaseIntegrationTest
{
    public function getTestCommands()
    {
        return array(
            'me-test'  => 'php scruit',
            ...
            ...
            ... some tests.
        );
    }
}
```


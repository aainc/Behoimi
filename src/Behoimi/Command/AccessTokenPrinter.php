<?php
/**
 * Date: 15/10/16
 * Time: 19:25
 */

namespace ReFUEL4\commands;


use Loula\AccessTokenListener;

class AccessTokenPrinter implements AccessTokenListener
{
    public function changedAccessTokenAt($accessToken, $refreshToken)
    {
        print "AccessToken: $accessToken\n";
        print "RefreshToken: $refreshToken\n";
        print "AccessToken saved\n";
        file_put_contents('.accessToken',
             '<?php return ' . var_export(array(
               'accessToken' => $accessToken ,
               'refreshToken' => $refreshToken
            ), true) . ';');

    }

}

<?php
/**
 * Date: 15/11/12
 * Time: 15:03.
 */

namespace Behoimi\OAuth;


class BaseScopeDefinition
{
    public static function isValidScopes(array $scopes)
    {
        static $values;
        if (!$values) {
            $clazz = new \ReflectionClass(get_called_class());
            $values = array_values($clazz->getConstants());
        }
        $result = true;
        foreach ($scopes as $value) {
            if (!isset($value) || !is_int($value) || !in_array($value, $values)) {
                $result = false;
                break;
            }
        }
        return $result;
    }
}
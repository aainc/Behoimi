<?php

/**
 * Date: 15/09/25
 * Time: 19:15.
 */
namespace Behoimi\OAuth;

class Scopes
{
    private $scopes = array();
    private $definedScopes = array();
    const R_PROFILE = 1;
    const W_PROFILE = 2;

    public function __construct(array $array)
    {
        static $definedScopes;
        if (!$definedScopes) {
            $clazz = new \ReflectionClass($this);
            $definedScopes = $clazz->getConstants();
        }
        $values = array_values($definedScopes);
        foreach ($array as $value) {
            if (!in_array($value, $values)) {
                throw new \InvalidArgumentException('invalid scope:' . $value);
            }
        }
        $this->definedScopes = $definedScopes;
        $this->scopes = $array;
    }

    public function isAllow($scope)
    {
        return in_array($scope, $this->scopes);
    }

    public function count()
    {
        return count($this->scopes);
    }

    public function get($i)
    {
        return isset($this->scopes[$i]) ? $this->scopes[$i] : null;
    }

    /**
     * @return array
     */
    public function getDefinedScopes()
    {
        return $this->definedScopes;
    }
}

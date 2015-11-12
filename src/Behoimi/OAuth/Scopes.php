<?php
namespace Behoimi\OAuth;

class Scopes
{
    private $scopes = array();

    public function __construct(array $array)
    {
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
}

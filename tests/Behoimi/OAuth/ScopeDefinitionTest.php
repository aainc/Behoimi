<?php
/**
 * Date: 15/11/12
 * Time: 15:41.
 */

namespace Behoimi\OAuth;


class ScopeDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testValidScope ()
    {
        $this->assertTrue(SampleScopeDefinition::isValidScopes(array(
            SampleScopeDefinition::R_PROFILE,
            SampleScopeDefinition::W_PROFILE
        )));
        $this->assertTrue(SampleScopeDefinition::isValidScopes(array(
            SampleScopeDefinition::R_PROFILE,
        )));
        $this->assertTrue(SampleScopeDefinition::isValidScopes(array(
            SampleScopeDefinition::W_PROFILE,
        )));
    }

    public function testInValidScope ()
    {
        $this->assertFalse(SampleScopeDefinition::isValidScopes(array(
            1, 2, 3, 4,
        )));
        $this->assertFalse(SampleScopeDefinition::isValidScopes(array(
            null
        )));
        $this->assertFalse(SampleScopeDefinition::isValidScopes(array(
            ""
        )));
        $this->assertFalse(SampleScopeDefinition::isValidScopes(array(
            new \stdClass(),
            new \stdClass(),
        )));
    }
}
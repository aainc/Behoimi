<?php
namespace Behoimi\Action;

interface DIInjectable
{
    public function setContainer(\Zaolik\DIContainer $di);
    public function getContainer();
}

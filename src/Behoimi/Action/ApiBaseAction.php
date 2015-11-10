<?php
namespace Behoimi\Action;
use Hoimi\BaseAction;
use Zaolik\DIContainer;

class ApiBaseAction extends BaseAction implements DIInjectable
{
    /**
     * @var DIContainer
     */
    protected $container = null;

    /**
     * @param DIContainer $container
     */
    public function setContainer(DIContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @return DIContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Mahotora\DatabaseSession
     */
    public function getDatabaseSession()
    {
        return $this->container->getFlyWeight('databaseSession');
    }
}
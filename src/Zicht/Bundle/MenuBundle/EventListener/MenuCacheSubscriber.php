<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Orm\Events;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;
use Zicht\Bundle\MenuBundle\Manager\CacheManager;

class MenuCacheSubscriber implements EventSubscriber
{

    /** @var CacheManager */
    protected $cacheManager;

    function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postLoad,
            Events::preUpdate,
            Events::preRemove,
            Events::postPersist,
        );
    }

    /**
     * simple helper to invalidate dependency
     * cache file, this is needed when a new
     * menu item is add/updated so the menu
     * is compiled again.
     *
     */
    protected function touchFile()
    {
        $file = __DIR__ . '/../Resources/config/services.xml';
        @touch($file);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($this->isValid($entity)) {
            $data = $this->cacheManager->loadFile();
            $data['menus'][$entity->getId()] = $entity->getName();
            $this->cacheManager->writeFile($data, true);
            $this->touchFile();
        }
    }


    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->isValid($entity)) {
            $data = $this->cacheManager->loadFile();
            unset($data['menus'][$entity->getId()]);
            $this->cacheManager->writeFile($data, true);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->isValid($entity) && $args->hasChangedField('name')) {
            $data = $this->cacheManager->loadFile();
            $data['menus'][$entity->getId()] = $entity->getName();
            $this->cacheManager->writeFile($data, true);
            $this->touchFile();
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        if ($this->isValid($args->getEntity())) {
            $this->cacheManager->writeFile($args->getEntityManager()->getConnection());
        }
    }

    /**
     * Check if given object is a entity of type
     * MenuItem and is a parent with a valid name
     *
     * @param   object $entity
     * @return  bool
     */
    protected function isValid($entity)
    {
        return
            $this->cacheManager->isEnabled() &&
            is_object($entity) &&
            $entity instanceof MenuItem &&
            is_null($entity->getParent()) &&
            !is_null($entity->getName());
    }

}
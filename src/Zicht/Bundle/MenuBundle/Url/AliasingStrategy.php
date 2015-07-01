<?php
/**
 * @author Rik van der Kemp <rik@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Url;

use \Zicht\Bundle\MenuBundle\Manager\MenuManager;
use \Zicht\Bundle\UrlBundle\Aliasing\DefaultAliasingStrategy;
use \Zicht\Bundle\UrlBundle\Aliasing\ProviderDecorator;
use \Zicht\Util\Str;

/**
 * Menu AliasingStrategy
 *
 * Creates aliases based on the menu
 *
 * @package Zicht\Bundle\MenuBundle\Url
 */
class AliasingStrategy extends DefaultAliasingStrategy
{
    /**
     * @var \Zicht\Bundle\MenuBundle\Manager\MenuManager
     */
    private $menuManager;
    /**
     * @var \Zicht\Bundle\UrlBundle\Aliasing\ProviderDecorator
     */
    private $urlProvider;

    public function __construct(MenuManager $menuManager, ProviderDecorator $urlProvider)
    {
        parent::__construct('/');
        $this->menuManager = $menuManager;
        $this->urlProvider = $urlProvider;
    }

    /**
     * Generate a public alias for the passed object
     *
     * @param mixed $subject
     * @return string
     */
    public function generatePublicAlias($subject, $currentAlias = '')
    {
        $path = $this->urlProvider->url($subject, array('aliasing' => true));
        $menuItem = $this->menuManager->getItemBy(array(':path' => $path));
        if (!empty($menuItem)) {
            $parts = array();
            for ($item = $menuItem; !is_null($item->getParent()); $item = $item->getParent()) {
                $parts [] = Str::systemize($item->getTitle());
            }
            $alias = '/' . join('/', array_reverse($parts));
        } else {
            $alias = parent::generatePublicAlias($subject);
        }

        return $alias;
    }
}
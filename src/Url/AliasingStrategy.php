<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Url;

use Zicht\Bundle\MenuBundle\Manager\MenuManager;
use Zicht\Bundle\UrlBundle\Aliasing\DefaultAliasingStrategy;
use Zicht\Bundle\UrlBundle\Aliasing\ProviderDecorator;
use Zicht\Bundle\UrlBundle\Url\Provider;
use Zicht\Util\Str;

/**
 * Creates aliases based on the menu
 */
class AliasingStrategy extends DefaultAliasingStrategy
{
    /** @var MenuManager */
    private $menuManager;

    /** @var ProviderDecorator */
    private $urlProvider;

    public function __construct(MenuManager $menuManager, Provider $urlProvider)
    {
        parent::__construct('/');
        $this->menuManager = $menuManager;
        $this->urlProvider = $urlProvider;
    }

    /**
     * @param mixed $subject
     * @param string $currentAlias
     * @return string
     */
    public function generatePublicAlias($subject, $currentAlias = '')
    {
        $path = $this->urlProvider->url($subject);
        $menuItem = $this->menuManager->getItemBy([':path' => $path]);
        if (!empty($menuItem)) {
            $parts = [];
            for ($item = $menuItem; !is_null($item->getParent()); $item = $item->getParent()) {
                $parts[] = Str::systemize($item->getTitle());
            }
            $alias = '/' . join('/', array_reverse($parts));
        } else {
            $alias = parent::generatePublicAlias($subject);
        }

        return $alias;
    }
}

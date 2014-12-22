<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Menu;

use \Knp\Menu\Iterator\RecursiveItemIterator;
use \Zicht\Bundle\UrlBundle\Entity\UrlAlias;

class UrlAliasingAwareBuilder extends Builder
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $root
     * @return \Knp\Menu\ItemInterface
     */
    public function createMenu($request, $root)
    {
        /** @var $qb \Doctrine\ORM\QueryBuilder */
        $query = sprintf('
            SELECT
                menu_item.title,
                COALESCE(url_alias.public_url, menu_item.path) as path,
                menu_item.id,
                menu_item.lvl,
                menu_item.name,
                menu_item.parent_id,
                menu_item.json_data,
                COALESCE(url_alias.internal_url, url_alias2.internal_url, menu_item.path) as internal_url
            FROM
                menu_item
                    LEFT JOIN url_alias ON(
                        menu_item.path=url_alias.internal_url
                        AND url_alias.mode=%1$d
                    )
                    LEFT JOIN url_alias url_alias2 ON(
                        menu_item.path=url_alias.public_url
                        AND url_alias.mode=%1$d
                    )
            WHERE
                menu_item.lft BETWEEN %2$d AND %3$d AND menu_item.root=%4$d AND lvl > %5$d
            ORDER BY
                menu_item.lft
            ',
            UrlAlias::REWRITE,
            $root->getLft(),
            $root->getRgt(),
            $root->getRoot(),
            $root->getLvl()
        );

        $hierarchy = $this->em->getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $menu = $this->factory->createItem('root');

        $this->addMenuItemHierarchy(
            $request,
            $this->menuItemEntity->buildTree($hierarchy),
            $menu
        );

        /** @var $items \Knp\Menu\ItemInterface[] */
        $items = new \RecursiveIteratorIterator(new RecursiveItemIterator($menu), \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($items as $item) {
            if ($item->getUri() === $request->getRequestUri()) {
                $item->setCurrent(true);
                break;
            } elseif($item->getUri() === $request->attributes->get('_internal_url')) {
                $item->setCurrent(true);
                break;
            } elseif ($item->getExtra('internal_url') && $item->getExtra('internal_url') === $request->getRequestUri()) {
                $item->setCurrent(true);
                break;
            } elseif ($item->getExtra('internal_url') && $item->getExtra('internal_url') === $request->attributes->get('_internal_url')) {
                $item->setCurrent(true);
                break;
            }
        }

        return $menu;
    }
}

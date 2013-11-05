<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Menu;

use \Zicht\Bundle\UrlBundle\Entity\UrlAlias;

class UrlAliasingAwareBuilder extends Builder
{
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
                menu_item.path as path_alias
            FROM
                menu_item
                    LEFT JOIN url_alias ON(
                        menu_item.path=url_alias.internal_url
                        AND url_alias.mode=%d
                    )
            WHERE
                menu_item.lft BETWEEN %d AND %d AND menu_item.root=%d AND lvl > %d
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

        $menu->setCurrentUri($request->getRequestUri());
        return $menu;
    }
}

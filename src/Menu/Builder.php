<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Menu;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Zicht\Bundle\MenuBundle\Entity\MenuItem as MenuItemEntity;

class Builder implements ContainerAwareInterface, BuilderInterface
{
    use ContainerAwareTrait;

    /** @var FactoryInterface */
    protected $factory;

    /** @var NestedTreeRepository */
    protected $menuItemEntity;

    /** @var Registry */
    protected $em;

    /**
     * @var array Keep cache map of locales -> [root names -> [root_id, left_value, right_value]]
     */
    protected $roots = [];

    /**
     * @var array Previously (pre-)loaded menus, mapped by locale and name
     */
    protected $menus = [];

    /**
     * @var array List with menu names that should be loaded
     */
    protected $preloadMenus = [];

    /**
     * @var string The default locale from the application
     */
    protected $defaultLocale;

    /**
     * @param class-string $entity
     * @param string $defaultLocale The [null] variable was inherited from old code, and will always be overwritten with a valid locale.
     */
    public function __construct(FactoryInterface $factory, ManagerRegistry $doctrine, $entity = MenuItemEntity::class, $defaultLocale = '[null]')
    {
        $this->factory = $factory;
        $this->em = $doctrine->getManager();
        $this->menuItemEntity = $this->em->getRepository($entity);
        $this->defaultLocale = $defaultLocale;
        $this->roots = [];
    }

    /**
     * @param array $menus
     */
    public function setPreloadMenus($menus)
    {
        $this->preloadMenus = $menus;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $name
     * @return ItemInterface
     * @throws \InvalidArgumentException
     */
    public function build($name, Request $request)
    {
        $ret = $this->factory->createItem($name);

        $menus = $this->loadRoots($request);

        if (!isset($menus[$name])) {
            return $ret;
        }

        $requestLocale = $request->get('_locale');

        if (!isset($this->menus[$requestLocale][$name])) {
            $rootIdToNameMap = array_combine(array_column($menus, 0), array_keys($menus));

            if (in_array($name, $this->preloadMenus)) {
                $menusToLoad = [];
                foreach ($this->preloadMenus as $preloadMenuName) {
                    if (!isset($menus[$preloadMenuName])) {
                        continue;
                    }

                    $menusToLoad[$preloadMenuName] = $menus[$preloadMenuName];
                }
            } else {
                $menusToLoad[$name] = $menus[$name];
            }

            $query = 'SELECT root, menu_item.* FROM menu_item WHERE ';
            $i = 0;
            // `$vals` contains [id, lft, rgt]
            foreach ($menusToLoad as $vals) {
                if ($i++ > 0) {
                    $query .= ' OR ';
                }
                $query .= vsprintf('(root=%d AND lft BETWEEN %d AND %d AND id <> root)', $vals);
            }
            $query .= ' ORDER BY root, lft';

            /** @var Connection $conn */
            $conn = $this->em->getConnection();
            $items = $conn->executeQuery($query)->fetchAllAssociative();
            $roots = [];
            foreach ($items as $item) {
                $roots[$item['root']][] = $item;
            }
            foreach ($roots as $rootId => $menu) {
                if (!isset($rootIdToNameMap)) {
                    continue;
                }
                $menuName = $rootIdToNameMap[$rootId];
                $this->menus[$requestLocale][$menuName] = $this->factory->createItem($menuName);

                $this->addMenuItemHierarchy(
                    $request,
                    $this->menuItemEntity->buildTree($menu),
                    $this->menus[$requestLocale][$menuName]
                );
            }
        }

        if (isset($this->menus[$requestLocale][$name])) {
            $ret = $this->menus[$requestLocale][$name];

            if (is_callable([$ret, 'setCurrentUri'])) {
                $ret->setCurrentUri($request->getRequestUri());
            }
        }

        return $ret;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $name
     * @param Request $request
     * @return mixed
     */
    public function getRootItemByName($name, $request)
    {
        $menus = $this->loadRoots($request);

        if (!isset($menus[$name])) {
            return null;
        }

        return $this->menuItemEntity->find($menus[$name][0]);
    }

    /**
     * @param string $name
     * @param Request $request
     * @return bool
     */
    public function hasRootItemByName($name, $request)
    {
        $menus = $this->loadRoots($request);
        return isset($menus[$name]);
    }

    /**
     * @return array
     */
    protected function loadRoots(Request $request)
    {
        $locale = $request->get('_locale', $this->defaultLocale);

        if (array_key_exists($locale, $this->roots)) {
            return $this->roots[$locale];
        }

        /** @var Connection $connection */
        $connection = $this->em->getConnection();
        $where = 'lvl=0';

        if ($locale) {
            $where .= sprintf(' AND (language IS NULL OR language=%s)', $connection->quote($locale));
        } else {
            $where .= ' AND language IS NULL';
        }

        $rows = $connection->query('SELECT id, name, language, lft, rgt FROM menu_item WHERE ' . $where)->fetchAllNumeric();
        foreach ($rows as [$id, $name, $language, $lft, $rgt]) {
            // if the language is null, and the root items is already loaded; ignore it.
            if (null === $language && isset($this->roots[$name])) {
                continue;
            }

            $this->roots[$locale][$name] = [$id, $lft, $rgt];
        }

        if (!isset($this->roots[$locale])) {
            // Apparently the query returned empty. We can safely mark this too.
            $this->roots[$locale] = null;
        }

        return $this->roots[$locale];
    }

    /**
     * @param Request $request
     * @param ItemInterface $root
     * @return ItemInterface
     * @throws \InvalidArgumentException
     */
    public function createMenu($request, $root)
    {
        if (!$root) {
            throw new \InvalidArgumentException('Invalid root item');
        }
        $menu = $this->factory->createItem('root');
        $this->addMenuItemHierarchy($request, $this->menuItemEntity->childrenHierarchy($root), $menu);

        // 1.x compatibility
        if (is_callable([$menu, 'setCurrentUri'])) {
            $menu->setCurrentUri($request->getRequestUri());
        }

        return $menu;
    }

    /**
     * @param Request $request
     * @param mixed $children
     * @param ItemInterface $parent
     * @return int
     */
    public function addMenuItemHierarchy($request, $children, $parent)
    {
        $ret = 0;
        foreach ($children as $child) {
            ++$ret;
            $item = $this->addMenuItem($request, $child, $parent);
            if (!empty($child['__children'])) {
                $ret += $this->addMenuItemHierarchy($request, $child['__children'], $item);
            }
        }
        return $ret;
    }

    /**
     * {@inheritDoc}
     *
     * @return ItemInterface
     */
    public function addMenuItem(Request $request, array $item, MenuItem $menu)
    {
        $attributes = [];

        // if the menu item has a name, add it as a css class.
        if ($name = $item['name']) {
            $attributes['class'] = $name;
        }
        if (empty($item['path'])) {
            $uri = null;
        } elseif (preg_match('!^(?:https?://|mailto:)!', $item['path'])) {
            $uri = $item['path'];
        } else {
            $baseUrl = $request->getBaseUrl();
            $uri = $baseUrl . '/' . ltrim($item['path'], '/');
        }

        $menuItem = $menu->addChild(
            $item['id'],
            [
                'uri' => $uri,
                'attributes' => $attributes,
                'label' => $item['title'],
            ]
        );

        if (!empty($item['json_data'])) {
            $item['json_data'] = @json_decode($item['json_data']);
        }

        $menuItem->setExtras($item);
        return $menuItem;
    }

    /**
     * {@inheritDoc}
     *
     * @param ItemInterface $item
     * @return void
     */
    public function addGhostItem(Request $request, $item)
    {
        $item->addChild(
            $this->factory->createItem(
                (string)$item->getExtra('id'),
                [
                    'uri' => $request->getRequestUri(),
                    'display' => false,
                    'label' => $item->getExtra('title'),
                ]
            )
        );
    }
}

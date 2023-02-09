<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Url;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Routing\RouterInterface;
use Zicht\Bundle\UrlBundle\Url\StaticProvider;
use Zicht\Bundle\UrlBundle\Url\SuggestableProvider;

class MenuItemNameUrlProvider extends StaticProvider implements SuggestableProvider
{
    /** @var EntityRepository */
    private $repository;

    public function __construct(Registry $doctrine, RouterInterface $router)
    {
        parent::__construct($router);
        $this->em = $doctrine->getManager();
        $this->repository = $doctrine->getManager()->getRepository('Zicht\Bundle\MenuBundle\Entity\MenuItem');
        $this->loaded = [];
    }

    /**
     * @param mixed $name
     * @return bool|mixed
     */
    public function supports($name)
    {
        if (!isset($this->loaded[$this->router->getContext()->getParameter('_locale')])) {
            $this->loadMappings();
            $this->loaded[$this->router->getContext()->getParameter('_locale')] = true;
        }

        return parent::supports($name);
    }

    /**
     * Loads all mappings from the url, based on the current request locale.
     */
    protected function loadMappings()
    {
        // using a subquery to allow for FETCH_KEY_PAIR
        $query = '
            SELECT
                name, path
            FROM (
                SELECT
                    menu_item.name,
                    menu_item.path,
                    COALESCE(menu_item.language, root_item.language) language
                FROM
                    menu_item INNER JOIN menu_item root_item ON (menu_item.root=root_item.id)
                WHERE
                    menu_item.path IS NOT NULL
                    AND menu_item.name IS NOT NULL AND LENGTH(menu_item.name) > 0
                HAVING
                    language IS NULL OR language=:lang
            ) s
            ORDER BY
                language=:lang DESC
        ';

        /** @var Connection $conn */
        $conn = $this->em->getConnection();
        $stmt = $conn->prepare($query);
        $rows = $stmt->executeQuery([':lang' => $this->router->getContext()->getParameter('_locale')])->fetchAllKeyValue();
        $this->addAll($rows);
    }

    /**
     * Suggest url's based on the passed pattern. The return value must be an array containing "label" and "value" keys.
     *
     * @param string $pattern
     * @return mixed
     */
    public function suggest($pattern)
    {
        $menuItems = $this->repository->createQueryBuilder('m')
            ->andWhere('m.name LIKE :pattern')
            ->getQuery()
            ->execute(['pattern' => '%' . $pattern . '%']);

        $suggestions = [];
        foreach ($menuItems as $item) {
            $suggestions[] = [
                'value' => $item->getName(),
                'label' => sprintf('%s (menu item)', $item),
            ];
        }

        return $suggestions;
    }
}

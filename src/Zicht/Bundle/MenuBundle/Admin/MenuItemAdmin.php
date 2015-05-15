<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Admin;

use \Sonata\AdminBundle\Datagrid\DatagridMapper;
use \Sonata\AdminBundle\Datagrid\ListMapper;
use \Sonata\AdminBundle\Form\FormMapper;
use \Zicht\Bundle\MenuBundle\Entity;
use \Zicht\Bundle\FrameworkExtraBundle\Admin\TreeAdmin;

/**
 * Class MenuItemAdmin
 *
 * @package Zicht\Bundle\MenuBundle\Admin
 */
class MenuItemAdmin extends TreeAdmin
{
    /**
     * @{inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        $formMapper
            ->add('path', 'zicht_url', array('required' => false))
            ->add('name')
            ->add('language')
            ->add('is_collapsible')
        ;

        $formMapper->setHelps(array(
            'name' => 'help.name'
        ));
    }


    /**
     * @{inheritDoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        return $listMapper
            ->addIdentifier('title', null, array('template' => 'ZichtAdminBundle:CRUD:tree_title.html.twig'))
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'filter'   => array(
                            'template' => 'ZichtAdminBundle:CRUD:actions/filter.html.twig',
                        ),
                        'move'   => array(
                            'template' => 'ZichtAdminBundle:CRUD:actions/move.html.twig',
                        ),
                        'edit'   => array(),
                        'delete' => array(),
                    )
                )
            );
    }

    /**
     * @{inheritDoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        parent::configureDatagridFilters($filter);

        $filter
            ->add('title')
            ->add('path')
            ->add('id', 'doctrine_orm_callback', array(
                'callback' => array($this, 'filterWithChildren')
            ));
    }


    /**
     * Get item plus children
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string $alias
     * @param string $field
     * @param array $value
     *
     * @return bool
     */
    public function filterWithChildren($queryBuilder, $alias, $field, $value)
    {
        if (!$value['value']) {
            return;
        }

        // Get the parent item, todo, check if necessary
        $parentQb = clone $queryBuilder;
        $parentItem =  $parentQb->where(sprintf('%s.id = %s', $alias, $value['value']))->getQuery()->getResult();
        $currentItem = current($parentItem);

        $expr = $queryBuilder->expr();
        $queryBuilder->where(
            $expr->andX(
                $expr->eq(sprintf('%s.root', $alias), $currentItem->getRoot()),
                $expr->orX(
                    $expr->andX(
                        $expr->lt(sprintf('%s.lft', $alias), $currentItem->getLft()),
                        $expr->gt(sprintf('%s.rgt', $alias), $currentItem->getRgt())
                    ),
                    $expr->between(sprintf('%s.lft', $alias), $currentItem->getLft(), $currentItem->getRgt())
                )
            )
        );

        return true;
    }
}
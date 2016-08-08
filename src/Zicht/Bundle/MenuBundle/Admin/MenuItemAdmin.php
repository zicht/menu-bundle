<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Zicht\Bundle\AdminBundle\Admin\TreeAdmin;

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
            ->tab('admin.tab.menu_item')
                ->add('path', 'zicht_url', array('required' => false))
                ->add('name')
            ->end()
            ->setHelps(array('name' => 'admin.help.menu_item_name'));
    }

    /**
     * @{inheritDoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper = parent::configureListFields($listMapper);
        $listMapper->add('path', 'string', array('template' => 'ZichtAdminBundle:CRUD:list_url.html.twig'));
        $listMapper->reorder(
            array(
                'title',
                'path'
            )
        );

        return $listMapper;
    }


    /**
     * @{inheritDoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        parent::configureDatagridFilters($filter);

        $filter
            ->add('title')
            ->add('name')
            ->add('path');
    }
}

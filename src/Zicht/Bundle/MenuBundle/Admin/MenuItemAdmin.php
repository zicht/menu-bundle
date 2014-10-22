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
        $listMapper = parent::configureListFields($listMapper);
        $listMapper->add('is_collapsible');
        $listMapper->reorder(array(
            'title',
            'is_collapsible'
        ));
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
            ->add('path')
        ;
    }
}
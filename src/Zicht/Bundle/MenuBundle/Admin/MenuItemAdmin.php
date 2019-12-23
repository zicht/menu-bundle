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
use Zicht\Bundle\MenuBundle\Security\Authorization\MenuVoter;
use Zicht\Bundle\UrlBundle\Type\UrlType;

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
                ->with('admin.tab.menu_item')
                    ->add('path', UrlType::class, array('required' => false))
                    ->add(
                        'name',
                        null,
                        array(
                            'attr' => ['read_only' => !$this->hasNameFieldAccess()],
                            'disabled'  => !$this->hasNameFieldAccess(),
                            'help' => 'admin.help.menu_item_name'
                        )
                    )
                ->end()
            ->end();
        ;
    }

    /**
     * @{inheritDoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper = parent::configureListFields($listMapper);
        $listMapper->add('path', 'string', array('template' => '@ZichtAdmin/CRUD/list_url.html.twig'));
        $listMapper->reorder(
            array(
                'title',
                'path'
            )
        );

        return $listMapper;
    }



    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        parent::configureDatagridFilters($filter);

        $filter
            ->add('title')
            ->add('name')
            ->add('path');
    }

    /**
     * @return bool
     */
    protected function hasNameFieldAccess()
    {
        return $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('security.authorization_checker')
            ->isGranted(
                MenuVoter::ROLE_NAME_FIELD_ACCESS
            )
        ;
    }
}

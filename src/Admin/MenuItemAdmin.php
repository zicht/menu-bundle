<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Zicht\Bundle\AdminBundle\Admin\TreeAdmin;
use Zicht\Bundle\MenuBundle\Security\Authorization\MenuVoter;
use Zicht\Bundle\UrlBundle\Type\UrlType;

class MenuItemAdmin extends TreeAdmin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->tab('admin.tab.menu_item')
                ->with('admin.tab.menu_item')
                    ->add('path', UrlType::class, ['required' => false])
                    ->add(
                        'name',
                        null,
                        [
                            'label' => 'form.label_name_technical',
                            'attr' => ['read_only' => !$this->hasNameFieldAccess()],
                            'disabled' => !$this->hasNameFieldAccess(),
                            'help' => 'admin.help.menu_item_name',
                        ]
                    )
                ->end()
            ->end();
    }

    public function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);

        $listMapper->add('path', 'string', ['template' => '@ZichtAdmin/CRUD/list_url.html.twig']);
        $listMapper->reorder(
            [
                'title',
                'path',
            ]
        );
    }

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

<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Zicht\Bundle\AdminBundle\Admin\TreeAdmin;
use Zicht\Bundle\MenuBundle\Security\Authorization\MenuVoter;
use Zicht\Bundle\UrlBundle\Type\UrlType;

class MenuItemAdmin extends TreeAdmin
{
    private ?AuthorizationCheckerInterface $checker = null;

    protected function configureFormFields(FormMapper $formMapper): void
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

    protected function configureListFields(ListMapper $listMapper): void
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

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        parent::configureDatagridFilters($filter);

        $filter
            ->add('title')
            ->add('name')
            ->add('path');
    }

    public function setAuthorizationChecker(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @return bool
     */
    protected function hasNameFieldAccess()
    {
        return $this->checker->isGranted(MenuVoter::ROLE_NAME_FIELD_ACCESS);
    }
}

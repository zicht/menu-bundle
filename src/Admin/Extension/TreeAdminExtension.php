<?php

namespace Zicht\Bundle\MenuBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TreeAdminExtension extends AbstractAdminExtension
{
    public function configureFormFields(FormMapper $formMapper): void
    {
        $subject = $formMapper->getAdmin()->getSubject();
        // follow the path from Zicht\Bundle\AdminBundle\Admin\TreeAdmin
        $formMapper->remove('name');
        $formMapper
            ->tab('General')
            ->with('General')
                ->add('language', TextType::class, ['required' => false, 'attr' => ['placeholder' => 'nl']])
                ->add('name', TextType::class, ['required' => false, 'help_html' => true, 'help' => 'admin.help.menu_item_name', 'disabled' => $subject && $subject->getId()])
            ->end()
            ->end();
    }
}

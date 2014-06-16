<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Zicht\Bundle\MenuBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zicht\Bundle\UrlBundle\Url\Provider;
use Symfony\Component\Form\FormBuilderInterface;

class MenuItemType extends \Symfony\Component\Form\AbstractType
{
    public function __construct($menuManager, Provider $urlProvider)
    {
        $this->menuManager = $menuManager;
        $this->urlProvider = $urlProvider;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver
            ->setDefaults(array(
                'disable_subscriber'    => false,
                'property_path'         => false,
                'data_class'            => 'Zicht\Bundle\MenuBundle\Entity\MenuItem',
            ));
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('add_to_menu', 'checkbox', array('required' => false, 'label' => 'add_to_menu'))
            ->add('parent', 'zicht_parent_choice', array('class' => 'Zicht\Bundle\MenuBundle\Entity\MenuItem', 'label' => 'parent'))
            ->add('title', 'text', array('required' => false, 'label' => 'title'))
        ;

        if (!$options['disable_subscriber']) {
            $builder->getParent()->addEventSubscriber(
                new Subscriber\MenuItemPersistenceSubscriber(
                    $this->menuManager,
                    $this->urlProvider,
                    $builder
                )
            );
        }
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'zicht_menu_item';
    }
}
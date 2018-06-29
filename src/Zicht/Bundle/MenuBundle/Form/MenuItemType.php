<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zicht\Bundle\FrameworkExtraBundle\Form\ParentChoiceType;
use Zicht\Bundle\UrlBundle\Url\Provider;

/**
 * Class MenuItemType
 *
 * @package Zicht\Bundle\MenuBundle\Form
 */
class MenuItemType extends AbstractType
{
    /**
     * @var object
     */
    protected $menuManager;

    /**
     * @var Provider
     */
    protected $urlProvider;

    /**
     * MenuItemType constructor.
     *
     * @param object $menuManager
     * @param Provider $urlProvider
     */
    public function __construct($menuManager, Provider $urlProvider)
    {
        $this->menuManager = $menuManager;
        $this->urlProvider = $urlProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'mapped' => false,
                    'data_class' => 'Zicht\Bundle\MenuBundle\Entity\MenuItem',
                )
            );
    }

    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('add_to_menu', CheckboxType::class, array('required' => false, 'label' => 'form.label_add_to_menu'))
            ->add('parent', ParentChoiceType::class, array('class' => 'Zicht\Bundle\MenuBundle\Entity\MenuItem', 'label' => 'form.label_parent'))
            ->add('title', TextType::class, array('required' => false, 'label' => 'form.label_title'));
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

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'zicht_menu_item';
    }
}

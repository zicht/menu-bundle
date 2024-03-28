<?php
/**
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

class MenuItemType extends AbstractType
{
    /** @var object */
    protected $menuManager;

    /** @var Provider */
    protected $urlProvider;

    /**
     * @param object $menuManager
     */
    public function __construct($menuManager, Provider $urlProvider)
    {
        $this->menuManager = $menuManager;
        $this->urlProvider = $urlProvider;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'mapped' => false,
                    'data_class' => 'Zicht\Bundle\MenuBundle\Entity\MenuItem',
                ]
            );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('add_to_menu', CheckboxType::class, ['required' => false, 'label' => 'form.label_add_to_menu'])
            ->add('parent', ParentChoiceType::class, ['class' => 'Zicht\Bundle\MenuBundle\Entity\MenuItem', 'label' => 'form.label_parent'])
            ->add('title', TextType::class, ['required' => false, 'label' => 'form.label_title']);
    }

    public function getBlockPrefix(): string
    {
        return 'zicht_menu_item';
    }
}

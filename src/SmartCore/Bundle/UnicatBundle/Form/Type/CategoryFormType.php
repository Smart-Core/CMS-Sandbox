<?php

namespace SmartCore\Bundle\UnicatBundle\Form\Type;

use SmartCore\Bundle\CMSBundle\Container;
use SmartCore\Bundle\UnicatBundle\Entity\UnicatRepository;
use SmartCore\Bundle\UnicatBundle\Form\Tree\CategoryTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryFormType extends AbstractType
{
    /**
     * @var UnicatRepository
     */
    protected $repository;

    /**
     * @param UnicatRepository $repository
     */
    public function __construct(UnicatRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categoryTreeType = (new CategoryTreeType(Container::get('doctrine')))->setStructure($options['data']->getStructure());

        $builder
            ->add('title', null, ['attr' => ['class' => 'focused']])
            ->add('slug')
            ->add('is_inheritance', null, ['required' => false])
            ->add('parent', $categoryTreeType)
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->repository->getCategoryClass(),
        ]);
    }

    public function getName()
    {
        return 'smart_unicat_repository_' . $this->repository->getName() . '_category';
    }
}

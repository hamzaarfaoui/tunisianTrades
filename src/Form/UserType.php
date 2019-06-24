<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class, array(
                    'label' => 'Nom',
                    'required' => false,
                ))
                ->add('prenom', TextType::class, array(
                    'required' => false,
                    'label' => 'Prenom'
                ))
                ->add('username', TextType::class, array(
                    'required' => true,
                    'label' => 'Username'
                ))
                ->add('email', EmailType::class, array(
                    'required' => true,
                    'label' => 'E-mail'
                ))
                ->add('password', PasswordType::class, array(
                    'required' => true,
                    'label' => 'Mot de passe'
                ))
                ->add('phone', NumberType::class, array(
                    'required' => false,
                    'label' => 'Télephone'
                ))
                
                ->add('adress', TextType::class, array(
                    'required' => false,
                    'label' => 'Adresse'
                ))
                
                ->add('city', TextType::class, array(
                    'required' => false,
                    'label' => 'Gouvernaurat'
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Document\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'user';
    }
}
<?php

namespace App\Form;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Hotel;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Client;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('password')
            ->add('clientPicture',FileType::class,[
            'label'=>'Client picture (image)',
            'mapped'=>false,
            'required'=>true,
            'constraints'=>[
                new File(
                    maxSize: '1024k',
                    mimeTypes : [
                            'image/jpeg',
                            'image/png',
                        ],
                    mimeTypesMessage:'Please upload a valid Image',
                )
                ],
            ])
            ->add('Hotel')
            ->add('Validate',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}

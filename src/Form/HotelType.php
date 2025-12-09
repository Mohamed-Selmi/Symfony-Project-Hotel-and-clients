<?php
namespace App\Form;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Hotel;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class HotelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('capacity')
            ->add('price')
            ->add('hotel_picture',FileType::class,[
            'label'=>'Hotel picture (image)',
            'mapped'=>false,
            'required'=>true,
            'constraints'=>[
                new File(
                    maxSize: '1024k',
                    mimeTypes : [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpeg',
                        ],
                    mimeTypesMessage:'Please upload a valid Image',
                )
                ],
            ])
            ->add('Validate',SubmitType::class)
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hotel::class,
        ]);
    }
}

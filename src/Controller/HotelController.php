<?php

namespace App\Controller;
use App\Entity\Hotel;
use App\Form\HotelType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
class HotelController extends AbstractController
{
    #[Route('/hotel', name: 'app_hotel')]
    public function index(): Response
    {
        return $this->render('hotel/index.html.twig', [
            'controller_name' => 'HotelController',
        ]);
    }
    #[Route('/hotel/add',name:'add_hotel')]
    public function addHotel(Request $request,EntityManagerInterface $em,SluggerInterface $slugger): Response
    {
        $hotel=new Hotel();
        //$form=$this->createForm("App\Form\HotelType",$hotel);
       
        $form=$this->createForm( HotelType::class,$hotel);

        $form ->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()){ 
            $HotelImage=$form->get('hotel_picture')->getData();
            if ($HotelImage){
                 $originalFilename = pathinfo($HotelImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$HotelImage->guessExtension();

                try {
                    $HotelImage->move(
                        $this->getParameter('hotel_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }

                $hotel->setHotelPicture($newFilename);
            }
            $em->persist($hotel);
            $em->flush();
            return $this->redirectToRoute('app_hotel');
        }
        return $this->render('/hotel/form.html.twig',['form'=>$form->createView()]);
    }
}

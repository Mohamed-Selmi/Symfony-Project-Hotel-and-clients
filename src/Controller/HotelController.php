<?php

namespace App\Controller;
use App\Entity\Hotel;
use App\Form\HotelType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
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
    public function addHotel(Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $hotel=new Hotel();
        //$form=$this->createForm("App\Form\HotelType",$hotel);
       
        $form=$this->createForm( HotelType::class,$hotel);

        $form ->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid()){ 
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
            $entityManager->persist($hotel);
            $entityManager->flush();
            return $this->redirectToRoute('hotel_list');
        }
        return $this->render('/hotel/form.html.twig',['form'=>$form->createView()]);
    }


    #[Route('/hotel/show/{id}',name:'show_hotel')]
    public function show(EntityManagerInterface $entityManager,$id)
    {
        $hotel=$entityManager->getRepository(Hotel::class)->find($id);
        return $this->render('hotel/show.html.twig',['hotel'=>$hotel]);
    }


    #[Route('/hotel/all',name:'hotel_list')]
    public function showAll(EntityManagerInterface $entityManager){
        $repository=$entityManager->getRepository(Hotel::class);
        $Hotels=$repository->findAll();
        return $this->render('hotel/home.html.twig',['Hotels'=>$Hotels]);
    }


    #[Route('/hotel/delete/{id}',name:'delete_hotel')]
    public function delete(Request $request,EntityManagerInterface $entityManager, $id ): Response
    {
        $hotel=$entityManager->getRepository(Hotel::class)->find($id);
        $HotelImage=$hotel->getHotelPicture();

         if ($HotelImage){
                $fileSystem=new Filesystem();
                $projectDir=$this->getParameter('kernel.project_dir');
                $fileSystem->remove($projectDir.'/public/uploads/HotelImages/'.$HotelImage);
            }
        $entityManager->remove($hotel);
        $entityManager->flush();
       
        return $this->redirectToRoute('hotel_list');
    
}
    #[Route('/hotel/update/{id}',name:'update_hotel',methods:['GET','POST'])]
    public function update(Request $request,EntityManagerInterface $entityManager, $id,SluggerInterface $slugger)
    {
        $hotel=new Hotel();
        $hotel=$entityManager->getRepository(Hotel::class)->find($id);
         if (!$hotel){
            throw $this->createNotFoundException('No hotel with id='.$id.'exists');
        }
        $form=$this->createForm( HotelType::class,$hotel);
        $form ->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid()){ 
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
            $entityManager->persist($hotel);
            $entityManager->flush();
            return $this->redirectToRoute('hotel_list');
    }
         return $this->render('/hotel/form.html.twig',['form'=>$form->createView()]);

}
}

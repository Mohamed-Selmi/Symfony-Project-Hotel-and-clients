<?php

namespace App\Controller;
use App\Entity\Hotel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

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
    public function addHotel(Request $request,EntityManagerInterface $em): Response
    {
        $hotel=new Hotel();
        $form=$this->createForm("App\Form\HotelType");
        $form ->handleRequest($request);
        if ($form.isSubmited() && form.isValid()){
            $em->persist($hotel);
            $em->flush();
            return this->redirectToRoute('app_hotel');
        }
        return $this->render('/hotel/form.html.twig',['form'=>$form.createView()]);
    }
}

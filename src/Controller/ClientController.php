<?php

namespace App\Controller;
use App\Entity\Client;
use App\Form\ClientType;
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
class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }
        #[Route('/client/add',name:'add_client')]
public function addClient(Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $client=new Client();
        //$form=$this->createForm("App\Form\ClientType",$client);
       
        $form=$this->createForm( ClientType::class,$client);

        $form ->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid()){ 
            $ClientImage=$form->get('clientPicture')->getData();
            if ($ClientImage){
                 $originalFilename = pathinfo($ClientImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$ClientImage->guessExtension();

                try {
                    $ClientImage->move(
                        $this->getParameter('client_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }

                $client->setClientPicture($newFilename);
            }
            $entityManager->persist($client);
            $entityManager->flush();
            return $this->redirectToRoute('app_client');
        }
        return $this->render('/client/form.html.twig',['form'=>$form->createView()]);
    }
  #[Route('/client/show/{id}',name:'show_client')]
    public function show(EntityManagerInterface $entityManager,$id)
    {
        $client=$entityManager->getRepository(Client::class)->find($id);
        return $this->render('/client/show.html.twig',['client'=>$client]);
    }
 #[Route('/client/all',name:'client_list')]
    public function showAll(EntityManagerInterface $entityManager,$id)
    {
        $repository=$entityManager->getRepository(Client::class);
        $clients=$repository->findAll();
        return $this->render('/client/home.html.twig',['clients'=>$clients]);
    }

    
    #[Route('/client/delete/{id}',name:'delete_client')]
    public function delete(Request $request,EntityManagerInterface $entityManager, $id ): Response
    {
        $client=$entityManager->getRepository(Client::class)->find($id);
        $clientImage=$client->getClientPicture();

         if ($clientImage){
                $fileSystem=new Filesystem();
                $projectDir=$this->getParameter('kernel.project_dir');
                $fileSystem->remove($projectDir.'/public/uploads/ClientImages/'.$clientImage);
            }
        $entityManager->remove($client);
        $entityManager->flush();
       
        return $this->redirectToRoute('app_client');
    
}
}

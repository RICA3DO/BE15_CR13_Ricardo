<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use App\Form\EventsType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Events;

class EventsController extends AbstractController
{
    #[Route('/', name: 'events')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $events = $doctrine->getRepository(Events::class)->findAll();

        return $this->render('events/index.html.twig', ['events' => $events]);
        
    }

    #[Route('/create', name: 'events_create')]
   public function create(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader): Response
   {
       $events = new Events();
       $form = $this->createForm(EventsType::class, $events);

       $form->handleRequest($request);

/* Here we have an if statement, if we click submit and if  the form is valid we will take the values from the form and we will save them in the new variables */
       if($form->isSubmitted() && $form->isValid()){

            $name = $form['name']->getData();
            $description = $form['description']->getData();
            $image = $form['image']->getData();
            $capacity = $form['capacity']->getData();
            $email = $form['email']->getData();
            $phone = $form['phone']->getData();
            $address = $form['address']->getData();
            $url = $form['url']->getData();
            $date_time = $form['date_time']->getData();

            $events->setName($name);
            $events->setDescription($description);
            $events->setImage($image);
            $events->setCapacity($capacity);
            $events->setEmail($email);
            $events->setPhone($phone);
            $events->setAddress($address);
            $events->setUrl($url);
            $events->setDateTime($date_time);

    //         $em = $doctrine->getManager();
    //         $em->persist($events);
    //         $em->flush();

    //        $this->addFlash(
    //            'notice',
    //            'Event Added'
    //            );
     
    //        return $this->redirectToRoute('events');
    //    }
    //    return $this->render('events/create.html.twig', ['form' => $form->createView()]);
           $now = new \DateTime('now');
           $imageFile = $form->get('image')->getData();
           //imageUrl is the name given to the input field
           if ($imageFile) {
             $imageFileName = $fileUploader->upload($imageFile);
             $events->setImage($imageFileName);
           }
 
           $events = $form->getData();
           $events->setDateTime($now);  
           $em = $doctrine->getManager();
           $em->persist($events);
           // $status = $doctrine->getRepository(Status::class)->find(1);
           $em->flush();

           $this->addFlash(
               'notice',
               'Event Added'
               );
     
           return $this->redirectToRoute('events');
       }
       return $this->render('events/create.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/edit/{id}', name: 'events_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $events = $doctrine->getRepository(Events::class)->find($id);
        $form = $this->createForm(EventsType::class, $events);
        $form->handleRequest($request);
  
        if ($form->isSubmitted() && $form->isValid()) {

            $events->setName($events->getName());
            $events->setDescription($events->getDescription());
            $events->setImage($events->getImage());
            $events->setCapacity($events->getCapacity());
            $events->setEmail($events->getEmail());
            $events->setPhone($events->getPhone());
            $events->setAddress($events->getAddress());
            $events->setUrl($events->getUrl());
            $events->setDateTime($events->getDateTime());

            $em = $doctrine->getManager();
            $em->persist($events);
            $em->flush();

            $this->addFlash(
                'notice',
                'Event has been succesfuly changed'
            );
  
            return $this->redirectToRoute('events');
        }
  
        return $this->render('events/edit.html.twig', ['form' => $form->createView()]);
    }

  #[Route('/details/{id}', name: 'events_details')]
  public function details(ManagerRegistry $doctrine, $id): Response
  {
      $events = $doctrine->getRepository(Events::class)->find($id);
    // dd($events);
      return $this->render('events/details.html.twig', ['events' => $events]);
  }

  #[Route('/delete/{id}', name: 'events_delete')]
  public function delete(ManagerRegistry $doctrine, $id): Response
  {
    $em = $doctrine->getManager();
    $events = $em->getRepository('App:Events')->find($id);
    $em->remove($events);
    
    $em->flush();
    $this->addFlash(
        'notice',
        'Event Removed'
    );  
    return $this->redirectToRoute('events');
}

}
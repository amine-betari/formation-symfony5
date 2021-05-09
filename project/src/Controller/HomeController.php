<?php

namespace App\Controller;

use App\Entity\Incident;
use App\Form\IncidentType;
use App\Message\MailNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $task = new Incident();
        $task->setUser($this->getUser())
            ->setCreatedAt(new DateTime('now'));

        $form = $this->createForm(IncidentType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $em->persist($task);
            $em->flush();

            // Test Mailer
          /*
           $email = (new Email())
                ->from($task->getUser()->getEmail())
                ->to('amine.betari@gmail.com')
                ->subject('New Incident #' . $task->getId() . ' - ' . $task->getUser()->getEmail())
                ->html('<p>' . $task->getDescription() . '</p>');

            sleep(10);

            $mailer->send($email);
          */

            $this->dispatchMessage(
                new MailNotification($task->getDescription(), $task->getId(), $task->getUser()->getEmail())
            );

            // Test Mailer
            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'HomeController',
        ]);
    }
}

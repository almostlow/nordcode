<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FeedController extends AbstractController
{
    /**
     * @Route("/feed", name="app_feed")
     */
    public function show(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        return $this->render('feed/show.html.twig', [
        ]);
    }
}

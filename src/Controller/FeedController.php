<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{
    /**
     * @Route("/feed", name="app_feed")
     */
    public function show(): Response
    {
        
        return $this->render('feed/show.html.twig', [
        ]);
    }
}

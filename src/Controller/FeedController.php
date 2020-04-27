<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Feeds\FeedCreator;

class FeedController extends AbstractController
{
    /**
     * @Route("/feed", name="app_feed")
     */
    public function show(FeedCreator $feed): Response
    {
        $feedsForResponse = $feed->create();
       
        return $this->render('feed/show.html.twig', [
            'feeds' => $feedsForResponse
        ]);
    }
}

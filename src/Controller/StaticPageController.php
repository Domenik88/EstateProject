<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StaticPageController extends AbstractController
{

    /**
     * @Route("/contact-us", name="contact_us", priority=10)
     */
    public function contactUs()
    {
        $contactUsArr = [
            'phone'   => '778-918-5990',
            'address' => '247 Sixth St, New Westminster, BC V3L 3A5',
            'email'   => 'vadim@estateblock.com',
            'lat'     => 0,
            'lng'     => 0,
        ];
        return $this->render('contact_us/index.html.twig', [
            'contactsData' => $contactUsArr,
        ]);
    }

    /**
     * @Route ("/how-it-works", name="how-it-works", priority=10)
     */
    public function howItWorks()
    {
        return $this->render('how-it-works/index.html.twig');
    }

    /**
     * @Route ("/selling", name="selling", priority=10)
     */
    public function selling()
    {
        return $this->render('selling/index.html.twig');
    }

    /**
     * @Route ("/buying", name="buying", priority=10)
     */
    public function buying()
    {
        return $this->render('buying/index.html.twig');
    }

    /**
     * @Route ("/sitemap", name="sitemap", priority=10)
     */
    public function sitemap()
    {
        return $this->render('sitemap/index.html.twig');
    }

    /**
     * @Route ("/browse-by-street", name="browse-by-street", priority=10)
     */
    public function browseByStreet()
    {
        return $this->render('browse_by_street/index.html.twig');
    }

    /**
     * @Route ("/assessment", name="assessment", priority=10)
     */
    public function assessment()
    {
        return $this->render('assessment/index.html.twig');
    }

    /**
     * @Route ("/price-your-home", name="price-your-home", priority=10)
     */
    public function priceYourHome()
    {
        return $this->render('price_your_home/index.html.twig');
    }

}
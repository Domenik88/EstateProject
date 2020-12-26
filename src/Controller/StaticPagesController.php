<?php

namespace App\Controller;

use App\Entity\StaticPages;
use App\Form\StaticPagesType;
use App\Repository\StaticPagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/static-pages")
 */
class StaticPagesController extends AbstractController
{
    /**
     * @Route("/", name="static_pages_index", methods={"GET"})
     */
    public function index(StaticPagesRepository $staticPagesRepository): Response
    {
        return $this->render('admin/static_pages/index.html.twig', [
            'static_pages' => $staticPagesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="static_pages_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $staticPage = new StaticPages();
        $form = $this->createForm(StaticPagesType::class, $staticPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($staticPage);
            $entityManager->flush();

            return $this->redirectToRoute('static_pages_index');
        }

        return $this->render('admin/static_pages/new.html.twig', [
            'static_page' => $staticPage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="static_pages_show", methods={"GET"})
     */
    public function show(StaticPages $staticPage): Response
    {
        return $this->render('admin/static_pages/show.html.twig', [
            'static_page' => $staticPage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="static_pages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, StaticPages $staticPage): Response
    {
        $form = $this->createForm(StaticPagesType::class, $staticPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('static_pages_index');
        }

        return $this->render('admin/static_pages/edit.html.twig', [
            'static_page' => $staticPage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="static_pages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, StaticPages $staticPage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$staticPage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($staticPage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('static_pages_index');
    }
}

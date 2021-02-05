<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Form\SearchPageType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/static-pages", priority=10)
 */
class AdminStaticPageController extends AbstractController
{
    /**
     * @Route("/", name="page_index", methods={"GET"})
     */
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render('admin/page/index.html.twig', [
            'static_pages' => $pageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{type}", name="page_new", methods={"GET","POST"}, requirements={"type"="\w+"})
     */
    public function new(Request $request, string $type = null): Response
    {
        $staticPage = new Page();
        if ($type == 'search') {
            $form = $this->createForm(SearchPageType::class, $staticPage);
        } else {
            $form = $this->createForm(PageType::class, $staticPage);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($staticPage);
            $entityManager->flush();

            return $this->redirectToRoute('page_edit',['id' => $staticPage->getId(),'type' => $staticPage->getType()]);
        }

        return $this->render('admin/page/new.html.twig', [
            'static_page' => $staticPage,
            'form' => $form->createView(),
            'form_type' => $type,
        ]);
    }

    /**
     * @Route("/{type}-{id}/edit", name="page_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Page $staticPage, $type): Response
    {
        if ($type == 'search') {
            $form = $this->createForm(SearchPageType::class, $staticPage);
        } else {
            $form = $this->createForm(PageType::class, $staticPage);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('page_edit',['id' => $staticPage->getId(),'type' => $staticPage->getType()]);
        }

        return $this->render('admin/page/edit.html.twig', [
            'static_page' => $staticPage,
            'form' => $form->createView(),
            'form_type' => $type,
        ]);
    }

    /**
     * @Route("/{id}", name="page_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Page $staticPage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$staticPage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($staticPage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('page_index');
    }
}

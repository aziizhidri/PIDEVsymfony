<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\MarketPlace;
use App\Form\AvisType;
use App\Form\MarketPlaceType;
use App\Repository\MarketPlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/marketplace')]
class MarketPlaceController extends AbstractController
{
    #[Route('/', name: 'app_market_place_indexback', methods: ['GET'])]
    public function index(MarketPlaceRepository $marketPlaceRepository): Response
    {
        return $this->render('market_place/index.html.twig', [
            'market_places' => $marketPlaceRepository->findAll(),
        ]);
    }
    #[Route('/front', name: 'app_market_place_index', methods: ['GET'])]
    public function indexfront(MarketPlaceRepository $marketPlaceRepository): Response
    {
        return $this->render('market_place/indexfront.html.twig', [
            'market_places' => $marketPlaceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_market_place_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $marketPlace = new MarketPlace();
        $form = $this->createForm(MarketPlaceType::class, $marketPlace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($marketPlace);
            $entityManager->flush();

            return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_place/new.html.twig', [
            'market_place' => $marketPlace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_place_show', methods: ['GET', 'POST'])]
    public function show(MarketPlace $marketPlace, Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Avis();
        $review->setDateAvis(new \DateTime()); // Set the review date to the current date/time
        $review->setMarketPlace($marketPlace); // Associate the review with the current product

        // Create the review form
        $form = $this->createForm(AvisType::class, $review);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the review to the database
            $entityManager->persist($review);
            $entityManager->flush();

            // Redirect back to the product details page
            return $this->redirectToRoute('app_market_place_show', ['id' => $marketPlace->getId()]);
        }

        return $this->render('market_place/show.html.twig', [
            'market_place' => $marketPlace,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/edit', name: 'app_market_place_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MarketPlace $marketPlace, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarketPlaceType::class, $marketPlace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_place/edit.html.twig', [
            'market_place' => $marketPlace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_place_delete', methods: ['POST'])]
    public function delete(Request $request, MarketPlace $marketPlace, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marketPlace->getId(), $request->request->get('_token'))) {
            $entityManager->remove($marketPlace);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
    }
}

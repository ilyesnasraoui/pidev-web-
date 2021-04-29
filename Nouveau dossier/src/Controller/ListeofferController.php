<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\Offre2Type;
use App\Repository\OffreRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/listeoffer")
 */
class ListeofferController extends AbstractController
{
    /**
     * @Route("/", name="listeoffer_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $offres = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->findAll();
        $size = count($offres);
        $offres = $paginator->paginate(
        // Doctrine Query, not results
            $offres,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            8
        );
        return $this->render('listeoffer/index.html.twig', [
            'offres' => $offres,
            'size' => $size,
        ]);
    }


    /**
     * @Route("/{idOffre}", name="listeoffer_show", methods={"GET"})
     */
    public function show(Offre $offre, int $idOffre): Response
    {
        return $this->render('listeoffer/show.html.twig', [
            'offre' => $offre,
            'idOffre' => $idOffre,
        ]);
    }



}
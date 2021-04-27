<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Offre;
use App\Form\Offre2Type;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/realisateuroffre")
 */
class RealisateuroffreController extends AbstractController
{
    /**
     * @Route("/", name="realisateuroffre_index", methods={"GET"})
     */
    public function index(OffreRepository $offreRepository): Response
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $offres = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->findOffreByUser($user->getIdUser());
        $size = count($offres);

        return $this->render('realisateuroffre/index.html.twig', [
            'offres' => $offres,
            'size' => $size,
        ]);
    }

    /**
     * @Route("/new", name="realisateuroffre_new", methods={"GET","POST"})
     */
    public function new(Request $request, int $idOffre): Response
    {
        $offre = new Offre();
        $form = $this->createForm(Offre2Type::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $offre ->setIdUser($user->getIdUser());
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('realisateuroffre_index');
        }

        return $this->render('realisateuroffre/new.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idOffre}", name="realisateuroffre_show", methods={"GET"})
     */
    public function show(Offre $offre,int $idOffre): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $cand = $this->getDoctrine()
            ->getRepository(Candidature::class)
            ->findByIdOffre($idOffre);
            return $this->render('realisateuroffre/show.html.twig', [
            'candid' => $cand,
                'offre'=>$offre,

        ]);
    }

    /**
     * @Route("/{idOffre}/edit", name="realisateuroffre_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Offre $offre): Response
    {
        $form = $this->createForm(Offre2Type::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('realisateuroffre_index');
        }

        return $this->render('realisateuroffre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idOffre}", name="realisateuroffre_delete", methods={"POST"})
     */
    public function delete(Request $request, Offre $offre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getIdOffre(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('realisateuroffre_index');
    }
}

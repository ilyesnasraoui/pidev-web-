<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Offre;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/candidature")
 */
class CandidatureController extends AbstractController
{
    /**
     * @Route("/", name="candidature_index", methods={"GET"})
     */
    public function index(): Response
    {
        $candidatures = $this->getDoctrine()
            ->getRepository(Candidature::class)
            ->findAll();


        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    /**
     * @Route("/new", name="candidature_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFiless = $form->get('cvpath')->getData();
            if ($ImageFiless) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()) . '.' . $ImageFiless->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/public/images/candidature';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFiless->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $candidature->setCvpath($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $candidature->setIdUser($user->getIdUser());
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('candidature_index');
        }
        return $this->render('candidature/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCandidature}", name="candidature_show", methods={"GET"})
     */
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    /**
     * @Route("/{idCandidature}/edit", name="candidature_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Candidature $candidature): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Cvv = $form->get('cvpath')->getData();
            if ($Cvv) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()).'.'.$Cvv->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/candidature';
                // Move the file to the directory where brochures are stored
                try {
                    $Cvv->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $candidature->setCvpath($newFilename);}
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('candidature_index');
        }

        return $this->render('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCandidature}", name="candidature_delete", methods={"POST"})
     */
    public function delete(Request $request, Candidature $candidature): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidature->getIdCandidature(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('candidature_index');
    }

    /**
     * @Route("/{idCandidature}/{idOffre}/accepter",name="accepter")
     */
    public function acceptbtn(Candidature $candidature, int $idOffre)
    {
        $candidature->setEtatcandidat("accepter");
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute("realisateuroffre_show",['idOffre' => $idOffre]);

    }

    /**
     * @Route("/{idCandidature}/{idOffre}/rejeter",name="rejeter")
     */
    public function rejeterbtn(Candidature $candidature, int $idOffre)
    {

        $candidature->setEtatcandidat("rejeter");
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute("realisateuroffre_show",['idOffre' => $idOffre]);

    }


}
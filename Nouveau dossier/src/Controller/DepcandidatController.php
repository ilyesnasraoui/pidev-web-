<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Form\Candidature2Type;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/depcandidat")
 */
class DepcandidatController extends AbstractController
{


    /**
     * @Route("/new/{idOffre}", name="depcandidat_new", methods={"GET","POST"})
     */
    public function new(Request $request, int $idOffre): Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(Candidature2Type::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Candidature Added Successfully!');

            $ImageFilesse = $form->get('cvpath')->getData();
            if ($ImageFilesse) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()) . '.' . $ImageFilesse->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/public/images/candidature';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFilesse->move(
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
            $candidature->setIdOffre($idOffre);
            $candidature->setEtatcandidat('attente');
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('listeoffer_index');
        }

        return $this->render('depcandidat/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

}

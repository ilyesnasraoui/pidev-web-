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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
           /*             Edit Candidature JSON              */
    /**
     * @Route("/editCandidatureJSON", name="editCandidatureJSON")
     */
    public function editCandidatureJSON(Request $request,NormalizerInterface $Normalizer): Response
    {
        $date = new \DateTime('now');
        $candidature = $this->getDoctrine()
            ->getRepository(Candidature::class)
            ->find($request->get('idCandidature'));

        $candidature->setIdUser($request->get('idUser'));
        $candidature->setIdOffre($request->get('idOffre'));
        $candidature->setCvpath($request->get('cvpath'));
        $candidature->setDate($date);
        $candidature->setDescription($request->get('description'));
        $candidature->setEtatcandidat($request->get('etatcandidat'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($candidature);
        $entityManager->flush();

        $jsonContent=$Normalizer->normalize($candidature,'json',['groups'=>'post:read']);
        return new Response("Candidature edited successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }

    /*       JSON ADD CANDIDATURE */
    /**
     * @Route("/addCandidatureJSON", name="addCandidatureJSON")
     */
    public function addCandidatureJSON(Request $request,NormalizerInterface $Normalizer): Response
    {
        $date = new \DateTime('now');
        $eta="attente";
        $candidature=new Candidature();
        $candidature->setIdUser($request->get('idUser'));
        $candidature->setIdOffre($request->get('idOffre'));
        $candidature->setCvpath($request->get('offreimgpath'));
        $candidature->setDate($date);
        $candidature->setDescription($request->get('description'));
        $candidature->setEtatcandidat($eta);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($candidature);
        $entityManager->flush();

        $jsonContent=$Normalizer->normalize($candidature,'json',['groups'=>'post:read']);
        return new Response("Candidature added successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }

    /*       JSON  DISPLAY ALLLLLL         */
    /**
     * @Route("/displayCandidature", name="displayCandidature", methods={"GET"})
     */
    public function getAll()
    {
        $candidature = $candidature = $this->getDoctrine()
            ->getRepository(Candidature::class)
            ->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($candidature);

        return new JsonResponse($formatted);

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

  #  /**
  #   * @Route("/addCandidature", name="addCandidature", methods={"POST"})
    #    */
    #  public function addCand(Request $request)
    #  {
    #      $candidature = new Candidature();
    #    $em = $this->getDoctrine()->getManager();
    #    $date = new \DateTime('now');
    #     $eta="attente";
    #    $content = json_decode($request->getContent(), true);
    #    $em = $this->getDoctrine()->getManager();
    #    #dd("dsfsfsdfsdfsdfgsdfgs");
    #    $candidature->setIdUser($content['idUser']);
    #    $candidature->setIdOffre($content['idOffre']);
    #    $candidature->setCvpath($content['cvpath']);
    #    $candidature->setDate($date);
    #    $candidature->setDescription($content['description']);
    #    $candidature->setEtatcandidat($eta);


    #     $em->persist($candidature);
    #    $em->flush();
    #    $serializer = new Serializer([new ObjectNormalizer()]);
    #    $formatted = $serializer->normalize($candidature);
    #    return new JsonResponse($formatted);

    # }

    /**
     * @Route("/{idCandidature}", name="candidature_show", methods={"GET"})
     */
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }
         /* MOBILE DISPLAY BY ID OFFRE */
    /**
     * @Route("/displayss/{id}",name="displayss",methods={"GET"})
     */
    public function getCand(int $id)
    {

        $candidature = $this->getDoctrine()->getManager()->getRepository(Candidature::class)->findByIdOffre($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($candidature);

        return new JsonResponse($formatted);
    }

    /*      MOBILE DISPLAY BY ID USER           */
    /**
     * @Route("/displaysse/{id}",name="displaysse",methods={"GET"})
     */
    public function getCands(int $id): JsonResponse
    {

        $candidature = $this->getDoctrine()->getManager()->getRepository(Candidature::class)->findByIdUser($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($candidature);

        return new JsonResponse($formatted);
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

    /*      MOBILE Delete BY ID CANDIDATURE           */
    /**
     * @Route("/deletesCandidd/{id}", name="deletesCandidd")
     */
    public function deleteCan(int $id) {
        $em = $this->getDoctrine()->getManager();
        $candidature =  $this->getDoctrine()
            ->getRepository(Candidature::class)
            ->find($id);
        if($candidature!=null ) {
            $em->remove($candidature);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Candidature a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("Id Candidature invalide.");


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
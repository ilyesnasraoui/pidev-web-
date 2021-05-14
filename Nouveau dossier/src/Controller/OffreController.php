<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\OffreType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/offre")
 */
class OffreController extends AbstractController
{
    /**
     * @Route("/", name="offre_index", methods={"GET"})
     */
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        $offres = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->findAll();

        $offres = $paginator->paginate(
        // Doctrine Query, not results
            $offres,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            8
        );
        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    /**
     * @Route("/displayOffre", name="displayOffre", methods={"GET"})
     */
    public function getAll()
    {
        $offre = $offre = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($offre);

        return new JsonResponse($formatted);

    }


    /**
     * @Route("/new", name="offre_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFile = $form->get('offreimgpath')->getData();
            if ($ImageFile) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()).'.'.$ImageFile->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/offre';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $offre->setOffreimgpath($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $offre->setIdUser($user->getIdUser());
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('offre_index');
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/addOffreJSON", name="addOffreJSON")
     */
    public function addOffreJSON(Request $request,NormalizerInterface $Normalizer): Response
    {
        $date = new \DateTime('now');

        $offre=new Offre();
        $offre->setIdUser($request->get('idUser'));
        $offre->setOffreimgpath($request->get('offreimgpath'));
        $offre->setDate($date);
        $offre->setDescription($request->get('description'));
        $offre->setTitre($request->get('titre'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($offre);
        $entityManager->flush();

        $jsonContent=$Normalizer->normalize($offre,'json',['groups'=>'post:read']);
        return new Response("Offre added successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }

    /**
     * @Route("/editOffreJSON", name="editOffreJSON")
     */
    public function editProfileJSON(Request $request,NormalizerInterface $Normalizer): Response
    {
        $date = new \DateTime('now');
        $offre = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->find($request->get('idOffre'));
        $offre->setIdUser($request->get('idUser'));
        $offre->setOffreimgpath($request->get('offreimgpath'));
        $offre->setDate($date);
        $offre->setDescription($request->get('description'));
        $offre->setTitre($request->get('titre'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($offre);
        $entityManager->flush();

        $jsonContent=$Normalizer->normalize($offre,'json',['groups'=>'post:read']);
        return new Response("Offre edited successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }

#    /**
 #    * @Route("/newOffre", name="newOffre", methods={"POST"})
 #    */
 #   public function addo(Request $request)
 #   {
    #        $offre = new Offre();
    #   $ems = $this->getDoctrine()->getManager();
    #   $date = new \DateTime('now');
    #
    #   $contents = json_decode($request->getContent(), true);
    #   $ems = $this->getDoctrine()->getManager();
    #   #dd("dsfsfsdfsdfsdfgsdfgs");
    #   $offre->setIdUser($contents['idUser']);
    #   $offre->setOffreimgpath($contents['offreimgpath']);
    # #   $offre->setDate($date);
    #  $offre->setDescription($contents['description']);
    #   $offre->setTitre($contents['titre']);

    #   $ems->persist($offre);
    #   $ems->flush();
    #    $serializer = new Serializer([new ObjectNormalizer()]);
    #    $formatted = $serializer->normalize($offre);
    #    return new JsonResponse($formatted);
    #
    # }


    /**
     * @Route("/{idOffre}", name="offre_show", methods={"GET"})
     */
    public function show(Offre $offre): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    /**
     * @Route("/displayoffre/{idUser}",name="displayoffre",methods={"GET"})
     */
    public function getOffres(int $idUser)
    {
        $offre = $this->getDoctrine()->getManager()->getRepository(Offre::class)->findOffreByUser($idUser);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($offre);

        return new JsonResponse($formatted);
    }


    /**
     * @Route("/{idOffre}/edit", name="offre_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Offre $offre): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFilepath = $form->get('offreimgpath')->getData();
            if ($ImageFilepath) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()).'.'.$ImageFilepath->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/offre';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFilepath->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $offre->setOffreimgpath($newFilename);}
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offre_index');
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{idOffre}", name="offre_delete", methods={"POST"})
     */
    public function delete(Request $request, Offre $offre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getIdOffre(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offre_index');
    }

    /*      MOBILE Delete BY ID Offre           */
    /**
     * @Route("/deletesOffres/{id}", name="deletesOffres")
     */
    public function deleteOff(int $id) {
        $em = $this->getDoctrine()->getManager();
        $offre =  $this->getDoctrine()
            ->getRepository(Offre::class)
            ->find($id);
        if($offre!=null ) {
            $em->remove($offre);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Offre a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("Id Offre invalide.");


    }
}
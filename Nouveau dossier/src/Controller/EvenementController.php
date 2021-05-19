<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Entity\CategorieEvent;
use App\Repository\CategorieEventRepository;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement", name="evenement_index", methods={"GET"})
     */
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        $donnees = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();
        $evenements=$paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),
            4
        );

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/evenementttjs", name="evenementttjs", methods={"GET"})
     */
    public function getAll()
    {
        $evenement = $evenement = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evenement);

        return new JsonResponse($formatted);

    }



    /**
     * @Route("/deletesEventt/{id}", name="deletesEventt")
     */
    public function deleteEv(int $id) {
        $em = $this->getDoctrine()->getManager();
        $evenement =  $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->find($id);
        if($evenement!=null ) {
            $em->remove($evenement);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("event a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("Id event invalide.");


    }


    /**
     * @Route("/editEvenementJSON", name="editEvenementJSON")
     */
    public function editEvenementJSON(Request $request,NormalizerInterface $Normalizer): Response
    {

        $evenement = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->find($request->get('idEvenement'));

        $evenement ->setIdCatEvenement($request->get('idCatEvenement'));
        $evenement ->setIdUser($request->get('idUser'));
        $evenement ->setNomEvenement($request->get('nomEvenement'));
        $evenement ->setDateEvenement($request->get('dateEvenement'));
        $evenement ->setDureeEvenement($request->get('dureeEvenement'));


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenement );
        $entityManager->flush();

        $jsonContent=$Normalizer->normalize($evenement ,'json',['groups'=>'post:read']);
        return new Response("Event edited successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }




    /**
     * @Route("/addevenementJSON", name="addevenementJSON")
     */
    public function addEventJSON(Request $request,NormalizerInterface $Normalizer): Response
    {

        $evenement =new Evenement();

        $evenement ->setIdCatEvenement($request->get('idCatEvenement'));
        $evenement ->setIdUser($request->get('idUser'));
        $evenement ->setNomEvenement($request->get('nomEvenement'));
        $evenement ->setDateEvenement($request->get('dateEvenement'));
        $evenement ->setDureeEvenement($request->get('dureeEvenement'));
        $evenement ->setImageEvnement($request->get('imageEvenement'));
        $evenement ->setDescription($request->get('description'));
        $evenement ->setValidate($request->get('validate'));


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenement);
        $entityManager->flush();

        $jsonContent=$Normalizer->normalize($evenement,'json',['groups'=>'post:read']);
        return new Response("Article added successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }














    /**
     * @Route("evenement/new", name="evenement_new", methods={"GET","POST"})
     */
    public function new(Request $request,UsersRepository  $UsersRepository,CategorieEventRepository $CategorieEventRepository): Response
    {
        $evenement = new Evenement();
        $users =new Users();
        $categorieevent = new CategorieEvent();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'users' => $UsersRepository->findAll(),
            'categorieevent'=> $CategorieEventRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/evenement/{idEvenement}", name="evenement_show", methods={"GET"})
     */
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @Route("/evenement/{idEvenement}/edit", name="evenement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Evenement $evenement,UsersRepository  $UsersRepository,CategorieEventRepository $CategorieEventRepository): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        $users =new Users();
        $categorieevent = new CategorieEvent();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evenement_index');


            return $this->render('evenement/edit.html.twig', [
                'evenement' => $evenement,
                'users' => $UsersRepository->findAll(),
                'categorieevent'=> $CategorieEventRepository->findAll(),
                'form' => $form->createView(),
            ]);
        }}

    /**
     * @Route("/evenement/{idEvenement}", name="evenement_delete", methods={"POST"})
     */
    public function delete(Request $request, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIdEvenement(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evenement_index');
    }

    /**
     * @Route("/showeventtt", name="evenement_indexx", methods={"GET"})
     */
    public function indexx(Request $request,PaginatorInterface $paginator): Response
    {
        $donnees = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();
        $evenements=$paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),
            4);

        return $this->render('evenement/eveenement.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/stats", name="stats")
     */

    public function statistiques(CategorieEventRepository $CategorieEventRepository ,EvenementRepository $evenementRepository){

        $categories=$CategorieEventRepository->findAll();
        $evenements=$evenementRepository->findAll();
        $catnom=[];
        $catcount=[];
        $i=0;


        foreach($categories as $categorieEvent)
        {
            $catnom[]=$categorieEvent->getNomCategorieEv();

            $i=0;

            foreach($evenements as $evenement)
            {
                if($evenement->getIdCatEvenement()==$catnom){
                    $i=$i+1;
                }

            }
            $catcount[]=$i;

        }


        return $this->render('evenement/stats.html.twig',[
            "evenement" => $evenements,
            "categories" => $categories,'nom'=>json_encode($catnom),'count'=>json_encode($catcount)

        ]);
    }



}

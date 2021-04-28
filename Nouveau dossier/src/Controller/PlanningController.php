<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Entity\Salle;
use App\Entity\Films;
use App\Form\PlanningType;
use App\Repository\FilmsRepository;
use App\Repository\SalleRepository;
use App\Repository\PlanningRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planning")
 */
class PlanningController extends AbstractController
{

    /**
     * @Route("/all", name="allplannings", methods={"GET","POST"})
     */
    public function sp(Request $request,PaginatorInterface $paginator,SalleRepository  $sr,FilmsRepository $fr, PlanningRepository  $pr)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $donnees = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Planning p
           
             ORDER BY p.date DESC
            '
        );


        $plannings=$paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),
            10
        );
        return $this->render('planning/allplannings.html.twig', [
            'salles' =>$sr->findAll() ,
            'plannings' => $plannings,
            'films' => $fr->findAll(),
        ]);

    }

    /**
     * @Route("/myplanning", name="my_planning", methods={"GET"})
     */
    public function myplanning(PlanningRepository $pr,SalleRepository $sr, FilmsRepository $fr): Response
    {          $salle = $this->get('security.token_storage')->getToken()->getUser();
          // var_dump($salle->getIdSalle());die;
        return $this->render('planning/myplanning.html.twig', [
            'plannings' => $pr->findByIdSalle($salle->getIdSalle()),
            'films' => $fr->findAll(),
        ]);
    }

    /**
     * @Route("/", name="planning_index", methods={"GET"})
     */
    public function index(PlanningRepository $pr,SalleRepository $sr, FilmsRepository $fr): Response
    {   $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->getRole()=="salle")
            return $this->render("error.html.twig");
        return $this->render('planning/index.html.twig', [
            'plannings' => $pr->findAll(),
            'salles' => $sr->findAll(),
            'films' => $fr->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="planning_new", methods={"GET","POST"})
     */
    public function new(FilmsRepository  $FilmRepository, SalleRepository  $SalleRepository): Response
    {

        $films =new Films();
        return $this->render('planning/new.html.twig',[
            'films' => $FilmRepository->findAll(),

        ]);
    }

    /**
     * @Route("/add", name="planning_add", methods={"GET","POST"})
     */
    public function add(Request $request): Response{
        $salle = $this->get('security.token_storage')->getToken()->getUser();
        $planning = new Planning();
        $planning->setIdFilm($request->get('filmname'));
        $planning->setIdSalle($salle->getIdSalle());
        $planning->setProjectionTime($request->get('projectiontime'));
        $planning->setPlaces($request->get('places'));
        $newdate =  (\DateTime::createFromFormat('Y-m-d',$request->get('projectiondate') ));
        $result = $newdate->format('Y-m-d');
        $planning->setDate(\DateTime::createFromFormat('Y-m-d', $result));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($planning);
        $entityManager->flush();
        return $this->redirectToRoute('my_planning');
    }


    /**
     * @Route("/{idPlanning}", name="planning_show", methods={"GET"})
     */
    public function show(Planning $planning): Response
    {
        return $this->render('planning/show.html.twig', [
            'planning' => $planning,
        ]);
    }

    /**
     * @Route("/{idPlanning}/edit", name="planning_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Planning $planning): Response
    {
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('planning_index');
        }

        return $this->render('planning/edit.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idPlanning}", name="planning_delete", methods={"POST"})
     */
    public function delete(Request $request, Planning $planning): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planning->getIdPlanning(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planning);
            $entityManager->flush();
        }

        return $this->redirectToRoute('planning_index');
    }
}

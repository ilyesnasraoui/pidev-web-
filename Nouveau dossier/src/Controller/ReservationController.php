<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\FilmsRepository;
use App\Repository\PlanningRepository;
use App\Repository\SalleRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{

    /**
     * @Route("/myreservations", name="my_reservations", methods={"GET"})
     */
    public function myreservation(PlanningRepository $pr,UsersRepository $ur, FilmsRepository $fr): Response
    {          $salle = $this->get('security.token_storage')->getToken()->getUser();
        // var_dump($salle->getIdSalle());die;
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery(
            'SELECT r
            FROM App\Entity\Reservation r , App\Entity\Planning p
            WHERE (p.idPlanning=r.idPlanning) AND (p.idSalle = :param)
           '
        )
            ->setParameters(array('param' => $salle->getIdSalle()));
        return $this->render('reservation/myreservations.html.twig', [
            'reservations' => $query->getResult(),
            'users' => $ur->findall(),
            'films' => $fr->findAll(),
            'plannings' => $pr->findAll(),
        ]);
    }
    /**
     * @Route("/mytickets", name="mytickets", methods={"GET","POST"})
     */
    public function mytickets(FilmsRepository $fr,SalleRepository $sr){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Reservation r , App\Entity\Planning p
            WHERE  
             (r.idUser = :id) AND (r.idPlanning = p.idPlanning)
             ORDER BY p.date
            '
        )
            ->setParameters(array('id' => $user->getIdUser()));
        return $this->render('reservation/mytickets.html.twig', [
            'salles' =>$sr->findAll() ,
            'plannings' => $query->getResult(),
            'films' => $fr->findAll(),
        ]);


    }

    /**
     * @Route("/book", name="book", methods={"GET","POST"})
     */
    public function book(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $reservation= new Reservation();
        $reservation->setIdPlanning($request->get('idplanning'));
        $reservation->setIdUser($user->getIdUser());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();

        $em=$this->getDoctrine()->getManager();
        $planning=$em->getRepository(Planning::class)->findOneBySomeField($request->get('idplanning'));
        $planning->setPlaces($planning->getPlaces()-1);
        $em->flush();
        return $this->redirectToRoute('allrooms');

    }
    /**
     * @Route("/", name="reservation_index", methods={"GET"})
     */
    public function index(): Response
    {  $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->getRole()=="salle")
            return $this->render("error.html.twig");
        $reservations = $this->getDoctrine()
            ->getRepository(Reservation::class)
            ->findAll();

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {  $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->getRole()=="salle")
            return $this->render("error.html.twig");
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idReservation}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/{idReservation}/edit", name="reservation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idReservation}", name="reservation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getIdReservation(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('my_reservations');
    }
}

<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Url;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/block/{idUser}", name="users_block", methods={"POST"})
     */
    public function block(Request $request, Users $user): Response
    {

         if ($this->isCsrfTokenValid('block'.$user->getIdUser(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(Users::class)->find(7);

            $user->setBlocked(1);
            $entityManager->flush();
        }

            return $this->redirectToRoute('users_index');


    }

    /**
     * @Route("/profile", name="users_profile", methods={"GET"})
     */
    public function profile(): Response
    {

        return $this->render('users/profile.html.twig');

    }


    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(): Response
    {



        $user = $this->get('security.token_storage')->getToken()->getUser();
      if($user=="anon.")
          return $this->render('error.html.twig');

        if($user->getRole()=="client")
          return $this->render('error.html.twig');
        $users = $this->getDoctrine()
            ->getRepository(Users::class)
            ->findAll();

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }


    /**
     * @Route("/new", name="users_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('users_index');
        }


        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/changepd", name="changepersonaldata", methods={"GET","POST"})
     */
    public function changepersonaldata(Request $request): Response
    {  $user = $this->get('security.token_storage')->getToken()->getUser();
        //return new Response($user->getPassword());
        $user->setRole($user->getRole());
        $user->setEmail($request->get('email'));
        $user->setPhone($request->get('phone'));
        $user->setUsername($request->get('username'));
        $user->setFname($request->get('firstname'));
        $user->setLname($request->get('lastname'));
        $user->setIdcard($request->get('idcard'));
        $user->setBlocked($user->getBlocked());
        $user->setPassword($user->getPassword());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('users_profile');


    }

    /**
     * @Route("/signup", name="signup", methods={"GET","POST"})
     */
    public function signup(Request $request): Response
    {
       // return new Response($request->get('username'));
        $user= new Users();
        $user->setBlocked(0);
        $user->setRole("client");
        $user->setEmail($request->get('email'));
        $user->setPhone($request->get('phone'));
        $user->setUsername($request->get('username'));
        $user->setPassword($request->get('password'));
        $user->setFname($request->get('firstname'));
        $user->setLname($request->get('lastname'));
        $user->setIdcard($request->get('idcard'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->render("basefront.html.twig");
    }

    /**
     * @Route("/{idUser}", name="users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{idUser}/edit", name="users_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Users $user): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idUser}", name="users_delete", methods={"POST"})
     */
    public function delete(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getIdUser(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index');
    }





}

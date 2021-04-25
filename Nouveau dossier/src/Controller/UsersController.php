<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Usersdata;
use App\Form\UsersType;
use App\Repository\UsersdataRepository;
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
     * @Route("/test", name="users_test", methods={"POST","GET"})
     */
    public function ttest(\Swift_Mailer $mailer){
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('mohamedkarim.oueslati@esprit.tn')
            ->setBody("test",
                       'text/plain'
                );

 $mailer->send($message);
 return new Response("tawa");

    }

    /**
     * @Route("/resetpwd", name="resetpwd", methods={"GET","POST"})
     */
      public function resetpassword(Request $request, UsersdataRepository $udr, UsersRepository $ur){
          $entityManager = $this->getDoctrine()->getManager();
          $user = $entityManager->getRepository(Users::class)->find($request->get('id'));
          $userdata=$udr->findOneByUserId($user->getIdUser());
          if($request->get('code')==$userdata->getForgetPwd())
          {$user->setPassword($request->get('pwd'));}
          else
              return $this->render('users/forgetpassword.html.twig',['user'=>$user , 'err1'=>"wrong code",'err2'=>""]);
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($userdata);
          $entityManager->flush();
          return $this->redirectToRoute("front");
      }
    /**
     * @Route("/forgetpwd", name="forgetpwd", methods={"GET","POST"})
     */
    public function forgetpwd(Request $request,\Swift_Mailer $mailer , UsersdataRepository $udr, UsersRepository $ur){
        $user=$ur->findOneByEmail($request->get('email'));
        $userdata=$udr->findOneByUserId($user->getIdUser());
        $userdata->setForgetPwd(random_int(100000,1000000000));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($userdata);
        $entityManager->flush();

        $message = (new \Swift_Message('Password reset'))
            ->setFrom('send@example.com')
            ->setTo($request->get('email'))
            ->setBody($userdata->getForgetPwd(),
                'text/plain'
            );

        $mailer->send($message);

        return $this->render('users/forgetpassword.html.twig',['user'=>$user , 'err1'=>"",'err2'=>""]);
    }



    /**
     * @Route("/block/{idUser}", name="users_block", methods={"POST"})
     */

    public function block(Request $request, Users $user): Response
    {

         if ($this->isCsrfTokenValid('block'.$user->getIdUser(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(Users::class)->find($user->getIdUser());

            $user->setBlocked(1);
            $entityManager->flush();
        }

            return $this->redirectToRoute('users_index');


    }

    /**
     * @Route("/profile", name="users_profile", methods={"GET"})
     */
    public function profile(UsersdataRepository $udr): Response
    {  $user = $this->get('security.token_storage')->getToken()->getUser();
        $userdata=$udr->findOneByUserId($user->getIdUser());

        return $this->render('users/profile.html.twig',[
            "userdata"=>$userdata
        ]);

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
    {   $user = $this->get('security.token_storage')->getToken()->getUser();

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
     * @Route("/changepwd", name="changepassword", methods={"GET","POST"})
     */
    public function changepassword(Request $request): Response
    {   $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user->getPassword()==$request->get('oldpwd')) {
           if($request->get('newpwd')==$request->get('confirmpwd'))
           {
               // password confirmed & oldpassword true
               $user->setPassword($request->get('newpwd'));
               $entityManager = $this->getDoctrine()->getManager();
               $entityManager->persist($user);
               $entityManager->flush();
               return $this->redirectToRoute('users_profile');
           }
          // confirmation ghalta mte3 lpassword
            return $this->redirectToRoute('users_profile');
        }
       // return new Response("don't match");

        /* */

        return $this->redirectToRoute('users_profile');


    }

    /**
     * @Route("/signup", name="signup", methods={"GET","POST"})
     */
    public function signup(Request $request,UsersRepository $u): Response
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

        $usr=$u->findOneUsername($user->getUsername());

        $userdata= new Usersdata();
        $userdata->setImage("");
        $userdata->setForgetPwd(0);
        $userdata->setAccountVerif(0);
        $userdata->setIdUser($usr->getIdUser());
        $entityManager->persist($userdata);
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

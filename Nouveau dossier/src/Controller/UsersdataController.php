<?php

namespace App\Controller;

use App\Entity\Usersdata;
use App\Form\UsersdataType;
use App\Repository\UsersdataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/usersdata")
 */
class UsersdataController extends AbstractController
{

    /**
     * @Route("/changeimg", name="changeimage", methods={"GET","POST"})
     */
    public function changeimage(Request $request,UsersdataRepository $udr)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userdata= $udr->findOneByUserId($user->getIdUser());
        //partie image
        $file= $_FILES['img'];
        $fileName=$file['name'];
        $error=$file['error'];
        $fileTmpName=$file['tmp_name'];
        $fileExt= explode('.',$fileName);
        $fileDest="images/users/".$user->getIdUser().".".end($fileExt);

        move_uploaded_file($fileTmpName,$fileDest);
        // image sent to the file

        if($error==0)

        {$userdata->setImage($user->getIdUser().".".end($fileExt));}
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($userdata);
        $entityManager->flush();

        return $this->render('users/profile.html.twig',[
            "userdata"=>$userdata
        ]);



    }

    /**
     * @Route("/", name="usersdata_index", methods={"GET"})
     */
    public function index(UsersdataRepository $usersdataRepository): Response
    {
        return $this->render('usersdata/index.html.twig', [
            'usersdatas' => $usersdataRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="usersdata_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $usersdatum = new Usersdata();
        $form = $this->createForm(UsersdataType::class, $usersdatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usersdatum);
            $entityManager->flush();

            return $this->redirectToRoute('usersdata_index');
        }

        return $this->render('usersdata/new.html.twig', [
            'usersdatum' => $usersdatum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idData}", name="usersdata_show", methods={"GET"})
     */
    public function show(Usersdata $usersdatum): Response
    {
        return $this->render('usersdata/show.html.twig', [
            'usersdatum' => $usersdatum,
        ]);
    }

    /**
     * @Route("/{idData}/edit", name="usersdata_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Usersdata $usersdatum): Response
    {
        $form = $this->createForm(UsersdataType::class, $usersdatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('usersdata_index');
        }

        return $this->render('usersdata/edit.html.twig', [
            'usersdatum' => $usersdatum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idData}", name="usersdata_delete", methods={"POST"})
     */
    public function delete(Request $request, Usersdata $usersdatum): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usersdatum->getIdData(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($usersdatum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('usersdata_index');
    }
}

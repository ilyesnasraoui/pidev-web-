<?php

namespace App\Controller;

use App\Entity\Films;
use App\Entity\CategorieFilm;
use App\Repository\CategorieEventRepository;
use App\Form\FilmsType;
use App\Repository\CategorieFilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Gedmo\Sluggable\Util\Urlizer;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\File\UploadedFile;






/**
 * @Route("/films")
 */
class FilmsController extends AbstractController
{

    /**
     * @Route("/search", name="films_search", methods={"POST","GET"})
     */
    public function searchAction(){



        $films = $this->getDoctrine()
            ->getRepository(Films::class)
            ->findBy(array('nomFilm' => 'new'),array('nomFilm' => 'ASC'),1 ,0);

        var_dump($films);
        return $this->render('films/moviegrid.html.twig', [
            'films' => $films,
        ]);
    }

    /**
     * @Route("/ssearch", name="films_ssearch", methods={"POST","GET"})
     */
    public function searchAAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

         $ch=$request->get("search");
         $cat=$request->get("cat");



        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Films p
            WHERE p.nomFilm LIKE :data '
        )
            ->setParameter('data', "%".$ch."%");

        return $this->render('films/moviegrid.html.twig', array(
            'films' => $query->getResult()));
    }

    /**
     * @Route("/showmov", name="showmov", methods={"GET"})
     * @param $CategorieFilmRepository
     * @return Response
     */
    public function showmovies(CategorieFilmRepository $categorieFilmRepository): Response
    {
        $films = $this->getDoctrine()
            ->getRepository(Films::class)
            ->findAll();

        return $this->render('films/moviegrid.html.twig', [
            'films' => $films,
            'CategorieFilms' => $categorieFilmRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="films_index", methods={"GET"})
     * @param $categorieFilmRepository
     * @return Response
     */
    public function index(CategorieFilmRepository $categorieFilmRepository): Response
    {
        $films = $this->getDoctrine()
            ->getRepository(Films::class)
            ->findAll();

        return $this->render('films/index.html.twig', [
            'films' => $films,
            'CategorieFilms' => $categorieFilmRepository->findAll(),

        ]);
    }


    /**
     * @Route("/new", name="films_new", methods={"GET","POST"})
     * @param Request $request
     * @param CategorieFilmRepository $categorieFilmRepository
     * @param $CategorieFilmRespository
     * @return Response
     */
    public function new(Request $request, CategorieFilmRepository $categorieFilmRepository): Response
    {
      /*  $film = new Films();
        $form = $this->createForm(FilmsType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('image')->getData();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                //... handle exception if something happens during file upload
            }
            $entityManager= $this->getDoctrine()->getManager();
            $film->setImage($fileName);
            $entityManager->persist($film);
            $entityManager->flush();



            return $this->redirectToRoute('films_index');
        }
  */

        return $this->render('films/new.html.twig', [
            'categoriefilms' => $categorieFilmRepository->findAll(),

        ]);
    }

    /**
     * @Route("/addnew", name="add_new", methods={"GET","POST"})
     */
    public function addnew(Request $request): Response
    {

        $film= new Films();
        $film->setIdCategorie($request->get('cat'));
        $film->setLanguage($request->get('lang'));
        $film->setNomFilm($request->get('nomfilm'));
        $film->setDureeFilm($request->get('duree'));
        $film->setImage($request->get('image'));
        $film->setDescription($request->get('desc'));
        $film->setUtube($request->get('utube'));
        $film->setRated($request->get('rated'));
        $newdate =  (\DateTime::createFromFormat('Y-m-d',$request->get('date') ));
        $result = $newdate->format('Y-m-d');
        $film->setDate(\DateTime::createFromFormat('Y-m-d', $result));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($film);
        $entityManager->flush();

          return $this->redirectToRoute('films_index');


    }


    /**
     * @Route("/{idFilm}", name="films_show", methods={"GET"})
     */
    public function show(Films $film): Response
    {
        return $this->render('films/show.html.twig', [
            'film' => $film,
        ]);
    }

    /**
     * @Route("/{idFilm}/edit", name="films_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Films $film): Response
    {

        $form = $this->createForm(FilmsType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var @var $uploadedFile */
            $uploadedFile =$form['image']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/images/produit';

            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);


            $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $film->setImage($newFilename);







            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('films_index');
        }

        return $this->render('films/edit.html.twig', [
            'film' => $film,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idFilm}", name="films_delete", methods={"POST"})
     */
    public function delete(Request $request, Films $film): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getIdFilm(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($film);
            $entityManager->flush();
        }

        return $this->redirectToRoute('films_index');
    }






}

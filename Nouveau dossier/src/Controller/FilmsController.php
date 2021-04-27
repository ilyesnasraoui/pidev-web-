<?php

namespace App\Controller;


use App\Entity\Films;
use App\Entity\Rate;
use App\Entity\Users;
use App\Entity\CategorieFilm;
use App\Repository\CategorieEventRepository;
use App\Repository\FilmsRepository;
use App\Form\FilmsType;
use App\Repository\CategorieFilmRepository;
use App\Repository\UsersdataRepository;
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

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;





/**
 * @Route("/films")
 */





class FilmsController extends AbstractController
{

    /**
     * @Route("/new", name="films_new", methods={"GET","POST"})
     * @param Request $request
     * @param CategorieFilmRepository $categorieFilmRepository

     * @return Response
     */
    public function new(Request $request, CategorieFilmRepository $categorieFilmRepository): Response
    {

        return $this->render('films/new.html.twig', [
            'categoriefilms' => $categorieFilmRepository->findAll(),

        ]);
    }


    /**
     * @Route("/saverate", name="save", methods={"POST","GET"})
     */
    public function saverate(Request $request)
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $id = $user->getIdUser();
        $ch = $request->get("note");
       // var_dump($ch);
        $idm = $request->get("idm");
        //var_dump($request->get("idm"));
        $ratez = $this->getDoctrine()
            ->getRepository(Rate::class)
            ->findAll();
        $var = "You already rated this movie";
        $varr = "done";

        foreach ($ratez as $rate) {
            if ($rate->getIdFilm() == $idm and $rate->getIdUser() == $id) {
                $rate->setNote($ch);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($rate);
                $entityManager->flush();

                $films = $this->getDoctrine()
                    ->getRepository(Films::class)
                    ->findAll();
                return $this->render('films/test2.html.twig', [
                    'films' => $films,]);
            }
        }

        $rate = new Rate();
        echo($varr);

        $rate->setIdUser($id);
        $rate->setNote($ch);
        $rate->setIdFilm($idm);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rate);
        $entityManager->flush();

        $films = $this->getDoctrine()
            ->getRepository(Films::class)
            ->findAll();

        return $this->render('films/test2.html.twig', [
            'films' => $films,]);

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
        //var_dump($films);

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
     * @Route("/{idFilm}", name="showmovi", methods={"GET"})
     */
    public function showsinglemovie(Films $film): Response

     {

         return $this->render('films/singlemovie.html.twig', [
             "film" => $film,


         ]);
    }

    /**
     * @Route("/testapii", name="test_api")
     * @param $categorieFilmRepository
     * @return Response
     */
    public function api(Request $request,CategorieFilmRepository $categorieFilmRepository): Response
    {

        $ch=$request->get("search");
        $curl=curl_init("https://api.themoviedb.org/3/search/multi?api_key=ba9007874ae1b197d4fa0574fabba170&language=en&query=".$ch."&page=1&include_adult=false&fbclid=IwAR08vpervb55VV8BsOuMpsIsfgxxkH_NJDGz1okpdkB20pvNg1vYdw82NVg");
        curl_setopt($curl,CURLOPT_PROXY_SSL_VERIFYPEER,false);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);


        $data=curl_exec($curl);
        var_dump($data);

        if($data === false) {
            var_dump(curl_error($curl));
        }else {

            $data = json_decode($data, true);
        }


        $films=array();

       for($x = '1'; $x <5;$x++){
        //var_dump($data['results'][$x]);
          $film=new Films();
        //  $name = ($data);
        // curl_close($curl);
        // create movie //////////////////////////////////////

        $film->setIdCategorie('5');
        $film->setLanguage($data['results'][$x]['original_language']);
        $film->setNomFilm($data['results'][$x]['original_title']);
        $film->setDureeFilm('5');
        $image = ($data['results'][$x]['poster_path']);
        $imgurl = ("https://image.tmdb.org/t/p/w500" . $image);
        $film->setImage($imgurl);
        echo($imgurl);
        $film->setDescription($data['results'][$x]['overview']);
        $film->setUtube("rgfrger");
        $film->setRated($data['results'][$x]['vote_average']);
        $date = "2020-10-10";
        $newdate = (\DateTime::createFromFormat('Y-m-d', $date));
        $result = $newdate->format('Y-m-d');
        $film->setDate(\DateTime::createFromFormat('Y-m-d', $result));

      // print_r($film);
       // curl_close($curl);


        // add movie to data base
        //    $entityManager = $this->getDoctrine()->getManager();
        //  $entityManager->persist($film);
        // $entityManager->flush();
        // MOVIE CREATED //////////////////
// add movies from database to films array
        /*   $films= $this->getDoctrine()
               ->getRepository(Films::class)
               ->findAll(); */
           $films[$x]=$film;

    }



      return $this->render('films/indexapi.html.twig', [
          array('films'=> $films),
            'films' => $films,
            'CategorieFilms' => $categorieFilmRepository->findAll(),
            ]);


    }


    /**
     * @Route("/ssearch", name="films_ssearch", methods={"POST","GET"})
     */
    public function searchAAction(Request $request,CategorieFilmRepository $categorieFilmRepository,FilmsRepository $filmrepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

         $ch=$request->get("search");
         $cat=$request->get("cat");


        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Films p
            WHERE (p.nomFilm LIKE :data )
            AND (p.idCategorie = :param)'
        )
            ->setParameters(array('data'=> "%".$ch."%", 'param' => $cat));



        return $this->render('films/moviegrid.html.twig', [
            'films' => $query->getResult(),
            'CategorieFilms' => $categorieFilmRepository->findAll(),
        ]);
    }




    /**
     * @Route("/filmtri/{id}", name="film_trie")
     */
    public function tri(FilmsRepository $filmRepository,$id)
    {

        $categorie = $this->getDoctrine()->getRepository(CategorieFilm::class)->find($id);
        
        $film = $filmRepository->findBycat($categorie);

        return $this->render('films/moviegrid.html.twig', [
            "films" => $film,
            "categorie" => $categorie,

        ]);
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
     * @Route("/addnew", name="add_new", methods={"GET","POST"})
     */
    public function addnew(Request $request): Response
    {





        $film= new Films();
        $film->setIdCategorie($request->get('cat'));
        $film->setLanguage($request->get('lang'));
        $film->setNomFilm($request->get('nomfilm'));
        $film->setDureeFilm($request->get('duree'));
        $image=("C:/Users/elyes\Documents/GitHub/pidev-web-/Nouveau dossier/public/images".($request->get('image')));
        $film->setImage($image);

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

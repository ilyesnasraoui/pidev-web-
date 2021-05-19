<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\CategorieEvent;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Evenement;
use App\Repository\EvenementRepository;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ArticleController extends AbstractController
{




    /**
     *@Route("/article",name="article_index")
     */
    public function index(Request $request)
    {

        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class,$propertySearch);
        $form->handleRequest($request);
        //initialement le tableau des articles est vide,
        //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
        $articles= [];

        if($form->isSubmitted() && $form->isValid()) {
            //on récupère le nom d'article tapé dans le formulaire
            $nom = $propertySearch->getNom();
            if ($nom!="")
                //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
                $articles= $this->getDoctrine()->getRepository(Article::class)->findBy(['contenu' => $nom] );
            else
                //si si aucun nom n'est fourni on affiche tous les articles
                $articles= $this->getDoctrine()->getRepository(Article::class)->findAll();
        }
        return  $this->render('article/index.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
    }



    /**
     * @Route("/articleee", name="articleee", methods={"GET"})
     */
    public function getAll()
    {
        $article = $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($article);

        return new JsonResponse($formatted);

    }


    /**
     * @Route("/deletesArticle/{id}", name="deletesArticlee")
     */
    public function deleteAr(int $id) {
        $em = $this->getDoctrine()->getManager();
        $article =  $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);
        if($article!=null ) {
            $em->remove($article);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("article a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("Id article invalide.");


    }



    /**
     * @Route("/editArticleJSON", name="editArticleJSON")
     */
    public function editArticleJSON(Request $request,NormalizerInterface $Normalizer): Response
    {

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($request->get('idArticle'));

        $article->setIdEvenement($request->get('idEvenement'));
        $article->setTitre($request->get('titre'));
        $article->setContenu($request->get('contenu'));


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        $jsonContent=$Normalizer->normalize($article,'json',['groups'=>'post:read']);
        return new Response("Article edited successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }










    /**
     * @Route("/addarticleJSON", name="addarticleJSON")
     */
    public function addArticleJSON(Request $request,NormalizerInterface $Normalizer): Response
    {

        $article =new Article();

        $article->setIdEvenement($request->get('idEvenement'));
        $article->setTitre($request->get('titre'));
        $article->setContenu($request->get('contenu'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        $jsonContent=$Normalizer->normalize($article,'json',['groups'=>'post:read']);
        return new Response("Article added successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }







    /**
     * @Route("article/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request,EvenementRepository $EvenementRepository): Response
    {
        $article = new Article();
        $evenement =new Evenement();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'evenements' => $EvenementRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }






    /**
     * @Route("/article/{idArticle}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }




    /**
     * @Route("/article/{idArticle}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article,TranslatorInterface $translator,EvenementRepository $EvenementRepository ): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $evenement =new Evenement();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $message =$translator->trans('user modified ');
            $this->addFlash('message',$message);
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'evenements' => $EvenementRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{idArticle}", name="article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getIdArticle(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }



    /**
     * @Route("/showarticle", name="article_ind", methods={"GET"})
     */
    public function indexx(): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('article/aarticle.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/change_locale/{locale}", name="change_locale")
     */
    public function changeLocale($locale, Request $request)
    {
        // On stocke la langue dans la session
        $request->getSession()->set('_locale', $locale);

        // On revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }







}

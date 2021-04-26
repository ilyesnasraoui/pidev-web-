<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use Symfony\Contracts\Translation\TranslatorInterface;


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
     * @Route("article/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();
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
    public function edit(Request $request, Article $article,TranslatorInterface $translator ): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $message =$translator->trans('user modified ');
            $this->addFlash('message',$message);
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
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



}

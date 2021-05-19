<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Produit;
use App\Form\AvisType;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/avis")
 */
class AvisController extends AbstractController
{
    /**
     * @Route("/", name="avis_index", methods={"GET"})
     */
    public function index(): Response
    {
        $avis = $this->getDoctrine()
            ->getRepository(Avis::class)
            ->findAll();

        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    /**

    /**
     * @Route("/new", name="avis_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avi);
            $entityManager->flush();

            return $this->redirectToRoute('avis_index');
        }

        return $this->render('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/addAvisLikeJSON", name="addAvisLikeJSON")
     */
    public function addAvisLikeJSON(Request $request,NormalizerInterface $Normalizer): Response
    {

        $avis=new Avis();

        $avis->setIdProduit($request->get('id_produit'));
        //$avis->setTypeAvis($request->get('type_avis'));
        $avis->setTypeAvis('like');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($avis);
        $entityManager->flush();
        $jsonContent=$Normalizer->normalize($avis,'json',['groups'=>'post:read']);
        return new Response("added successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }
    /**
     * @Route("/addAvisdisLikeJSON/", name="addAvisdisLikeJSON")
     */
    public function addAvisdisLikeJSON(Request $request,NormalizerInterface $Normalizer): Response
    {

        $avis=new Avis();
        $avis->setIdProduit($request->get('id_produit'));
        //$avis->setTypeAvis($request->get('type_avis'));
        $avis->setTypeAvis('dislike');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($avis);
        $entityManager->flush();
        $jsonContent=$Normalizer->normalize($avis,'json',['groups'=>'post:read']);
        return new Response(" added successfully".json_encode($jsonContent,JSON_UNESCAPED_UNICODE));
    }

    /**
     * @Route("/{idProduit}/new", name="avis_new_like", methods={"GET","POST"})
     */
    public function new_like(Request $request,Produit $produit): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

         $id=$produit->getIdProduit();

            $avi->setIdProduit($id);
            $avi->setTypeAvis('like');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avi);
            $entityManager->flush();


        $nblikes = $this->getDoctrine()->getRepository(Avis::class)->numberoflikes($id);
        $nbdislikes = $this->getDoctrine()->getRepository(Avis::class)->numberofdislikes($id);

        return $this->render('produit/singleproduct.html.twig', [
            "produits" => $produit,  'like' => $nblikes, 'dislike' => $nbdislikes
        ]);
    }


    /**
     * @Route("/{idProduit}/neww", name="avis_new_dislike", methods={"GET","POST"})
     */
    public function new_dislike(Request $request,Produit $produit): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);


        $id=$produit->getIdProduit();


        $avi->setIdProduit($id);
        $avi->setTypeAvis('dislike');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($avi);
        $entityManager->flush();


        $nblikes = $this->getDoctrine()->getRepository(Avis::class)->numberoflikes($id);
        $nbdislikes = $this->getDoctrine()->getRepository(Avis::class)->numberofdislikes($id);

        return $this->render('produit/singleproduct.html.twig', [
            "produits" => $produit,  'like' => $nblikes, 'dislike' => $nbdislikes
        ]);
    }

    /**
     * @Route("/{idAvis}", name="avis_show", methods={"GET"})
     */
    public function show(Avis $avi): Response
    {
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
        ]);
    }

    /**
     * @Route("/{idAvis}/edit", name="avis_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Avis $avi): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('avis_index');
        }

        return $this->render('avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idAvis}", name="avis_delete", methods={"POST"})
     */
    public function delete(Request $request, Avis $avi): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getIdAvis(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($avi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('avis_index');
    }
    /**
     * @Route("/stat/t/t", name="stat", methods={"GET"})
     */
    public function chartAction()
    { $repository = $this->getDoctrine()->getRepository(Avis::class);
        $prog = $repository->findAll();
        $em = $this->getDoctrine()->getManager();

        $likes=0;
        $dislikes=0;



        foreach ($prog as $prog)
        {
            if (  $prog->getTypeAvis()=="like")  :

                $likes+=1;
            elseif ($prog->getTypeAvis()=="dislike"):

                $dislikes+=1;
            endif;

        }
        $pieChart = new PieChart();
        $pieChart->getOptions()->setTitle("       Store : Products Notices added by Users  ");
        $pieChart->getData()->setArrayToDataTable(
            [
                ['Notice', '4'],
                ['Likes on our Products ',  (int)$likes],
                ['Dislikes on our Products', (int)$dislikes],


            ]
        );
        $pieChart->getOptions()->setPieSliceText('label');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getLegend()->setPosition('none');
        //


        return $this->render('avis/stat.html.twig', [
            'piechart' => $pieChart
        ]);
    }
}

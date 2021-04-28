<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{


    /**
     * @Route("/tee", name="produite", methods={"GET"})
     */
    public function indeex (Request $request, PaginatorInterface $paginator)
    {

        $produits = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findAll();

        $produits = $paginator->paginate(
            $produits, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('produit/store.html.twig', [
            'produits' => $produits,
        ]);


    }
    /**
     * @Route("showpro/{idProduit}", name="showpro", methods={"GET"})
     */
    public function showsingleProduct(Produit $produit): Response

    {
        $id = $produit->getIdProduit();
        $nblikes = $this->getDoctrine()->getRepository(Avis::class)->numberoflikes($id);
        $nbdislikes = $this->getDoctrine()->getRepository(Avis::class)->numberofdislikes($id);

        return $this->render('produit/singleproduct.html.twig', [
            "produits" => $produit,  'like' => $nblikes, 'dislike' => $nbdislikes
        ]);
    }
   /**
     * @Route("/{idProduit}/genpro/qr", name="genpro", methods={"GET"})
     */
    public function genpro (Produit $produit): Response

    {

        return $this->render('produit/qrcode.html.twig', [
            "produits" => $produit,


        ]);
    }
    /**
     * @Route("/{idProduit}/zoom", name="zoomProduct", methods={"GET"})
     */
    public function zoomProduct(Produit $produit): Response

    {

        return $this->render('produit/zoom.html.twig', [
            "produits" => $produit,


        ]);
    }
    /**
     * @Route("/{idProduit}/share/pro", name="sharepro", methods={"GET"})
     */
    public function sharepro (Produit $produit): Response

    {

        return $this->render('produit/facebook.html.twig', [
            "produits" => $produit,


        ]);
    }


    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(): Response
    {
        $produits = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFile = $form->get('image')->getData();
            if ($ImageFile) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()).'.'.$ImageFile->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/produit';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $produit->setImage($newFilename);
            }
            $bad_words = array("fuck","bitch","damn");//hethom hot fiih lkelmeeet eli methebhomch
            $test=$form->get('description')->getData();
            $test1=str_ireplace($bad_words,"****",$test);
            $produit->setDescription($test1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'service' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idProduit}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }



    /**
     * @Route("/{idProduit}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFile = $form->get('image')->getData();
            if ($ImageFile) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()).'.'.$ImageFile->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/produit';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $produit->setImage($newFilename);}
            $bad_words = array("validation","Suivi","integration");//hethom hot fiih lkelmeeet eli methebhomch
            $test=$form->get('description')->getData();
            $test1=str_ireplace($bad_words,"****",$test);
            $produit->setDescription($test1);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idProduit}", name="produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getIdProduit(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }
    /**
     * @Route ("/test/test/tri",name="tri", methods={"POST"})
     */
    public function tri(ProduitRepository $repository , Request $request)
    {

        if (isset($_POST['tri']))
        {
            $choix = $_POST['tri'];
            if ($choix=='nom')
            {
                $produits=$repository->OrderBynom();
                return $this->render('produit/index.html.twig', [
                'produits' => $produits,
            ]);
            }
            elseif ($choix=='prix')
            {
                $produits=$repository->OrderByprix();
                return $this->render('produit/index.html.twig', [
                    'produits' => $produits,
                ]);
            }
            elseif ($choix=='couleur')
            {
                $produits=$repository->OrderBycouleur();
                return $this->render('produit/index.html.twig', [
                    'produits' => $produits,
                ]);
            }

        }

    }
    /**
     * @Route ("/test/test/sto",name="trii", methods={"POST"})
     */
    public function trii(ProduitRepository $repository , Request $request)
    {

        if (isset($_POST['tri']))
        {
            $choix = $_POST['tri'];
            if ($choix=='nom')
            {
                $produits=$repository->OrderBynom();
                return $this->render('produit/store.html.twig', [
                    'produits' => $produits,
                ]);
            }
            elseif ($choix=='prix')
            {
                $produits=$repository->OrderByprix();
                return $this->render('produit/store.html.twig', [
                    'produits' => $produits,
                ]);
            }
            elseif ($choix=='couleur')
            {
                $produits=$repository->OrderBycouleur();
                return $this->render('produit/store.html.twig', [
                    'produits' => $produits,
                ]);
            }

        }

    }

}

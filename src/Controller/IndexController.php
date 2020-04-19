<?php
namespace App\Controller;



use App\Entity\Plat;
use App\Entity\Score;

use App\Form\PlatType;
use App\Form\ScoreType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\CategorySearch;
use App\Entity\PropertySearch;
use App\Form\CategorySearchType;
use App\Form\PropertySearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class IndexController extends AbstractController
{



    /**
 *@Route("/",name="plat_list")
 */
 public function home(Request $request)
 {
 $propertySearch = new PropertySearch();
 $form = $this->createForm(PropertySearchType::class,$propertySearch);
 $form->handleRequest($request);
 //initialement le tableau des plats est vide,
 //c.a.d on affiche les plats que lorsque l'utilisateur
 //clique sur le bouton rechercher
 $plats= [];

 if($form->isSubmitted() && $form->isValid()) {
 //on récupère le nom d'plat tapé dans le formulaire
 $titre = $propertySearch->getTitre();
 if ($titre!="")
 //si on a fourni un nom d'plat on affiche tous les plats ayant ce nom
 $plats= $this->getDoctrine()->getRepository(Plat::class)->findBy(['titre' => $titre] );
 else
 //si si aucun nom n'est fourni on affiche tous les plats
 $plats= $this->getDoctrine()->getRepository(Plat::class)->findAll();
 }
 return $this->render('plats/index.html.twig',[ 'form' =>$form->createView(), 'plats' => $plats]);
 }

/**
 * @Route("/plat/save")
 */
public function save() {
    $entityManager = $this->getDoctrine()->getManager();
    $plat = new Plat();
    $plat->setTitre('Plat 1');
    $plat->setIngredients('Plat 1');
    $plat->setTempsDeCuissant('Plat 1');
  
    
   
    $entityManager->persist($plat);
    $entityManager->flush();
    return new Response('Plat enregisté avec id '.$plat->getId());
    }
   

    /**
     * @IsGranted("ROLE_EDITOR")
 * @Route("/plat/new", name="new_plat")
 * Method({"GET", "POST"})
 */
 public function new(Request $request) {
    $plat = new Plat();
    $form = $this->createForm(PlatType::class,$plat);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
    $plat = $form->getData();
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($plat);
    $entityManager->flush();
    return $this->redirectToRoute('plat_list');
    }
    return $this->render('plats/new.html.twig',['form' => $form->createView()]);
 }


/**
 * @Route("/plat/{id}", name="plat_show")
 */
public function show($id) {
    $plat = $this->getDoctrine()->getRepository(Plat::class)
    ->find($id);
    return $this->render('plats/show.html.twig',
    array('plat' => $plat));
     }


     /**
     * @IsGranted("ROLE_EDITOR")
 * @Route("/plat/edit/{id}", name="edit_plat")
 * Method({"GET", "POST"})
 */
public function edit(Request $request, $id) {
    $plat = new Plat();
    $plat = $this->getDoctrine()->getRepository(Plat::class)->find($id);
   
    $form = $this->createForm(PlatType::class, $plat);
   
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
   
        return $this->redirectToRoute('plat_list');
    }
   
    return $this->render('plats/edit.html.twig', ['form' =>$form->createView()]);
}
    
 

 /**
  * @IsGranted("ROLE_EDITOR")
     * @Route("/plat/delete/{id}",name="delete_plat")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id) {
        $plat = $this->getDoctrine()->getRepository(Plat::class)->find($id);
  
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($plat);
        $entityManager->flush();
  
        $response = new Response();
        $response->send();

        return $this->redirectToRoute('plat_list');
      }

      /**
 * @Route("/score/newCat", name="new_score")
 * Method({"GET", "POST"})
 */
 public function newScore(Request $request) {
  $score = new score();
  $form = $this->createForm(ScoreType::class,$score);
  $form->handleRequest($request);
  if($form->isSubmitted() && $form->isValid()) {
  $plat = $form->getData();
  $entityManager = $this->getDoctrine()->getManager();
  $entityManager->persist($score);
  $entityManager->flush();
  }
 return $this->render('plats/newScore.html.twig',['form'=>
 $form->createView()]);
  }




  /**
 * @Route("/category/newCat", name="new_category")
 * Method({"GET", "POST"})
 */
 public function newCategory(Request $request) {
    $category = new Category();
    $form = $this->createForm(CategoryType::class,$category);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
    $plat = $form->getData();
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($category);
    $entityManager->flush();
    }
   return $this->render('plats/newCategory.html.twig',['form'=>
   $form->createView()]);
    }


    /**
 * @Route("/art_cat/", name="plat_par_cat")
 * Method({"GET", "POST"})
 */
 public function platsParCategorie(Request $request) {
    $categorySearch = new CategorySearch();
    $form = $this->createForm(CategorySearchType::class,$categorySearch);
    $form->handleRequest($request);
    $plats= [];
    if($form->isSubmitted() && $form->isValid()) {
        $category = $categorySearch->getCategory();
       
        if ($category!="")
       $plats= $category->getPlats();
        else
        $plats= $this->getDoctrine()->getRepository(Plat::class)->findAll();
        }
       
        return $this->render('plats/platsParCategorie.html.twig',['form' => $form->createView(),'plats' => $plats]);
        }
   
 
}
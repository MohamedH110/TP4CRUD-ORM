<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;  
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;

class IndexController extends AbstractController
{

 

    #[Route('/', name: 'article_list', methods:['GET'])]
    public function home(PersistenceManagerRegistry $managerRegistry)  {
        $articles = $managerRegistry->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig',['articles' => $articles]); 

      
    }
    // public function home() {
    //     $articles = ['Artcile1', 'Article 2','Article 3'];
    //      return $this->render('articles/index.html.twig',['articles' => $articles]); 
    //    }
    
      
     #[Route('/save', name: 'save-article', methods:['GET','POST'])]
     public function save(PersistenceManagerRegistry $managerRegistry) {

      $entityManager = $managerRegistry->getManager();
      $article = new Article();
      $article->setNom('Article 2'); 
      $article->setPrix(4000); 
       $entityManager->persist($article); 
      $entityManager->flush(); 
      return new Response('Article enregisté avec id '.$article->getId());
       }
        
    
    
    #[Route('/new', name: 'new article', methods:['GET','POST'])]
    public function new(PersistenceManagerRegistry $managerRegistry,Request $request)  {
      $article = new Article();
      $form=$this->createFormBuilder($article)
      ->add('nom', TextType::class) 
      ->add('prix', TextType::class) 
      ->add('save', SubmitType::class, array('label' => 'Créer') )
      ->getForm();
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()) 
      { 
        $article = $form->getData();
        $entityManager =$managerRegistry->getManager();
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/new.html.twig',['form' => $form->createView()]);
  }

  #[Route('/article/{id}', name:"article_show")]
  public function show(PersistenceManagerRegistry $managerRegistry,$id)  {
    $article=$managerRegistry->getRepository(Article::class)->find($id);
    return $this->render('articles/show.html.twig', array('article' => $article)); 
  }



  #[Route('/article/edit/{id}',name:"edit_article",methods:['GET','POST'])]
  public function edit(PersistenceManagerRegistry $managerRegistry,Request $request,$id)  {
    $article = new Article();
    $article=$managerRegistry->getRepository(Article::class)->find($id);
    $form=$this->createFormBuilder($article)
    ->add('nom', TextType::class) 
    ->add('prix', TextType::class) 
    ->add('save', SubmitType::class, array('label' => 'Modifier') )
    ->getForm();
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) 
    { 
      $entityManager = $managerRegistry->getManager(); 
      $entityManager->flush(); 
      return $this->redirectToRoute('article_list');
  }
  return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
}



#[Route('/article/delete/{id}',name:"delete_article")]
public function delete(PersistenceManagerRegistry $managerRegistry,Request $request,$id): Response  {
  $article=$managerRegistry->getRepository(Article::class)->find($id);
  $entityManager = $managerRegistry->getManager(); 
  $entityManager->remove($article);
  $entityManager->flush();
  $response = new Response();
  $response->send();
  return $this->redirectToRoute('article_list');

}
}

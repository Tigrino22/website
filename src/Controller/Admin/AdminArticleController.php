<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/article', name: 'app_admin_article_')]
class AdminArticleController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {

        return $this->render('admin/admin_article/index.html.twig', [
            'controller_name' => 'AdminArticleController',
            'current_admin_menu' => 'app_admin_article',
        ]);
    }

    #[Route('/insert', name: 'insert')]
    public function create(Request $request): Response
    {

        $article = new Article;

        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm->add('submit', SubmitType::class, [
            'label' => 'Enregistrer'
        ]);

        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Un article a été ajouté !');

            return $this->redirect($this->generateUrl('app_admin_article_home'));
            
        }


        
        return $this->render('admin/admin_article/insert.html.twig', [
            'controller_name' => 'AdminArticleController',
            'current_admin_menu' => 'app_admin_article',
            'articleForm' => $articleForm->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article', name: 'app_article_')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ArticleRepository $articleRepository): Response
    {

        $articles = $articleRepository->findDescAll();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'current_menu' => 'app_article_index',
            'articles' => $articles
        ]);
    }

    #[Route('/show/{slug}-{id}', name: 'show')]
    public function show(ArticleRepository $articleRepository): Response
    {

        $articles = $articleRepository->findDescAll();

        return $this->render('article/show.html.twig', [
            
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Image;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/article', name: 'app_admin_article_')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArticleRepository $repoArticles): Response
    {

        return $this->render('admin/admin_article/liste.html.twig', [
            'current_admin_menu' => 'app_admin_article',
            'articles' => $repoArticles->findDescAll()
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

            // On récupère les images transmisent
            $images = $articleForm->get('images')->getData();
            foreach ($images as $image) {
                //On génére un nouveau nom pour chaque image
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier Uploads/images
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On stock l'image dans la base de donnée
                $img = new Image;
                $img->setName($fichier);
                $article->addImage($img);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Un article a été ajouté !');

            return $this->redirect($this->generateUrl('app_admin_article_home'));
        }



        return $this->render('admin/admin_article/insert.html.twig', [
            'current_admin_menu' => 'app_admin_article',
            'articleForm' => $articleForm->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, Article $article): Response
    {

        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm->add('submit', SubmitType::class, [
            'label' => 'Modifier'
        ]);

        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {

            // On récupère les images transmisent
            $images = $articleForm->get('images')->getData();
            foreach ($images as $image) {
                //On génére un nouveau nom pour chaque image
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier Uploads/images
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On stock l'image dans la base de donnée
                $img = new Image;
                $img->setName($fichier);
                $article->addImage($img);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'L\'article a été modifié !');

            return $this->redirect($this->generateUrl('app_admin_article_home'));
        }



        return $this->render('admin/admin_article/edit.html.twig', [
            'current_admin_menu' => 'app_admin_article',
            'articleForm' => $articleForm->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Request $request, Article $article): Response
    {

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->get('_token'))){

            $images = $article->getImages();
            foreach($images as $image) {

                $nom = $image->getName();
                unlink($this->getParameter('images_directory') . '/' . $nom);
    
                $em = $this->getDoctrine()->getManager();
                $em->remove($image);

                $this->addFlash('success', 'Les images de l\'article ont été supprimées avec succès !');
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
    
            $this->addFlash('success', 'L\'article a été supprimé avec succès !');
    
            return $this->redirect($this->generateUrl('app_admin_article_home'));

        } else {

            $this->addFlash('error', 'Le token est invalide');

            return $this->redirect($this->generateUrl('app_admin_article_home'), 302);

        }


    }

    #[Route('/setActive/{id}', name: 'SetActive')]
    public function setActive(Article $article): void
    {
        $article->setIsActive(($article->getIsActive()) ? false : true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
    }

    #[Route("/remove/image/{id}", name: "remove_image", methods: ['DELETE'])]
    public function deleteImage(Image $image, Request $request)
    {

        //On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->get('_token'))) {

            $nom = $image->getName();
            unlink($this->getParameter('images_directory') . '/' . $nom);

            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            return $this->redirect($this->generateUrl('app_admin_article_edit', [
                'id' => $image->getArticle()->getId()
            ]));

        } else {

            $this->addFlash('error', 'Le token est invalide');

            return $this->redirect($this->generateUrl('app_admin_article_home'), 302);

        }
    }
}

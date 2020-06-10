<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('blog/blog.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }
}

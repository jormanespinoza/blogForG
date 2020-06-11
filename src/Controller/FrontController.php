<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class FrontController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
        $this->postRepository = $this->entityManager->getRepository(Post::class);
        $this->timezone = 'America/Argentina/Buenos_Aires';
        date_default_timezone_set($this->timezone);
    }
    /**
     * @Route("/", name="blog", methods={"GET"})
     */
    public function blog()
    {
        return $this->render('blog_front/blog.html.twig', [
            'posts' => $this->postRepository->findAll(),
        ]);
    }

    /**
     * @Route("/blog/{url}", name="post", methods={"GET"})
     */
    public function post($url)
    {
        // Checks if post exits in the database
        $post = $this->postRepository->findOneBy([
            'url' => $url,
            'rejected' => false,
            'visible' => true
        ]);

        if (!$post instanceof Post) {
            if ($this->getUser()) {
                return $this->redirectToRoute('dashboard');
            }

            return $this->redirectToRoute('blog');
        }

        $keywords = explode(' ', $post->getTitle());
        array_unshift($keywords, 'g', 'blog');
        $keywords = implode(',', $keywords);

        return $this->render('blog_front/post.html.twig', [
            'post' => $post,
            'keywords' =>$keywords
        ]);
    }

    /**
     * @Route("/{url}/vista-previa", name="post_preview", methods={"GET"})
     */
    public function postPreview($url)
    {
        // Checks if post exits in the database
        $post = $this->postRepository->findOneBy(['url' => $url]);

        if (!$post instanceof Post) {
            if ($this->getUser()) {
                return $this->redirectToRoute('dashboard');
            }

            return $this->redirectToRoute('blog');
        }

        $keywords = explode(' ', $post->getTitle());
        array_unshift($keywords, 'g', 'blog');
        $keywords = implode(',', $keywords);

        return $this->render('blog_front/post.html.twig', [
            'post' => $post,
            'keywords' =>$keywords
        ]);
    }
}


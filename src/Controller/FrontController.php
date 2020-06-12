<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Like;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class FrontController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManagerInterface;
        $this->request = $requestStack->getCurrentRequest();
        $this->postRepository = $this->entityManager->getRepository(Post::class);
        $this->likeRepository = $this->entityManager->getRepository(Like::class);
        $this->timezone = 'America/Argentina/Buenos_Aires';
        date_default_timezone_set($this->timezone);
    }
    /**
     * @Route("/", name="blog", methods={"GET"})
     */
    public function blog()
    {
        return $this->render('blog_front/blog.html.twig', [
            'posts' => $this->postRepository->findBy([
                'rejected' => false, 'visible' => true
            ],
            [
                'id' => 'DESC'
            ])
        ]);
    }

    /**
     * @Route("/articulo/{url}", name="post", methods={"GET"})
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

        $likeByUser = $this->likeRepository->findOneBy(['user' => $this->getUser(), 'post' => $post, 'liked' => true]);

        return $this->render('blog_front/post.html.twig', [
            'post' => $post,
            'keywords' =>$keywords,
            'likes' => count($this->likeRepository->findBy(['post' => $post, 'liked' => true])),
            'likedByUser' => $likeByUser instanceof Like
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

    public function getPostComments(Post $post)
    {
        return $this->render("blog_front/includes/comments.html.twig", [
            'post' =>  $post
        ]);
    }

    /**
     * @Route("/add-new-comment", name="add_new_comment", methods={"POST"})
     */
    public function addNewComment()
    {
        // Get parameters
        $post = $this->postRepository->findOneBy(['id' => $this->request->request->get('post')]);
        $message = filter_var ($this->request->request->get('comment'), FILTER_SANITIZE_STRING);

        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setPost($post);
        $comment->setMessage($message);
        $comment->setCreatedAt(new \DateTime());
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->render("blog_front/includes/comments.html.twig", [
            'post' =>  $post
        ]);
    }

    /**
     * @Route("/count-comments", name="count_comment", methods={"POST"})
     */
    public function countComments()
    {
        // Get parameter
        $post = $this->postRepository->findOneBy(['id' => $this->request->request->get('post')]);

        return new Response(count($post->getComments()));
    }

    /**
     * @Route("/like-post", name="like_post", methods={"POST"})
     */
    public function likePost()
    {
        // Get parameters
        $post = $this->postRepository->findOneBy(['id' => $this->request->request->get('post')]);
        $liked = $this->request->request->get('liked') === 'true' ? true : false;

        $like = $this->likeRepository->findOneBy(['user' => $this->getUser(), 'post' => $post]);

        if ($like instanceof Like) {
            $like->setLiked($liked);
            $like->setUpdatedAt(new \DateTime());
        } else {
            $like = new Like();
            $like->setUser($this->getUser());
            $like->setPost($post);
            $like->setLiked($liked);
            $like->setCreatedAt(new \DateTime());
        }

        $this->entityManager->persist($like);
        $this->entityManager->flush();

        $postTotalLikes = count($this->likeRepository->findBy(['post' => $post, 'liked' => true]));

        return new Response($postTotalLikes);
    }
}


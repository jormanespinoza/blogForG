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
        // Get posts
        $basePosts = $this->postRepository->findBy(['rejected' => false, 'visible' => true ], ['id' => 'DESC']);

        $posts = array();
        // Formating posts
        foreach ($basePosts as $post) {
            // Likes
            $likedByUser = false;
            if ($this->getUser()) {
                $likedByUser = $this->likeRepository->findOneBy(['userId' => $this->getUser()->getId(), 'postId' => $post->getId(), 'liked' => true]);
                $likedByUser = $likedByUser instanceof Like;
            }

            // Generate item post with its info
            $post = [
                'id'                  => $post->getId(),
                'title'               => $post->getTitle(),
                'firstName'           => $post->getUser()->getFirstName(),
                'lastName'            => $post->getUser()->getLastName(),
                'profileImage'        => $post->getUser()->getProfile()->getProfileImage(),
                'mainImage'           => $post->getMainImage(),
                'body'                => $post->getBody(),
                'url'                 => $post->getUrl(),
                'createdAt'           => $post->getCreatedAt(),
                'comments'            => $post->getComments(),
                'likes'               => count($this->likeRepository->findBy(['postId' => $post->getId(), 'liked' => true])),
                'likedByUser'         => $likedByUser
            ];

            // Add to array
            array_push($posts, $post);
        }

        return $this->render('blog_front/blog.html.twig', [
            'posts' => $posts
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

        // Likes
        $likeByUser = false;
        if ($this->getUser()) {
            $likeByUser = $this->likeRepository->findOneBy(['userId' => $this->getUser()->getId(), 'postId' => $post->getId(), 'liked' => true]);
            $likeByUser = $likeByUser instanceof Like;
        }

        return $this->render('blog_front/post.html.twig', [
            'post' => $post,
            'keywords' =>$keywords,
            'likes' => count($this->likeRepository->findBy(['postId' => $post->getId(), 'liked' => true])),
            'likedByUser' => $likeByUser
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

        $like = $this->likeRepository->findOneBy(['userId' => $this->getUser()->getId(), 'postId' => $post->getId()]);

        if ($like instanceof Like) {
            $like->setLiked($liked);
            $like->setUpdatedAt(new \DateTime());
        } else {
            $like = new Like();
            $like->setUserId($this->getUser()->getId());
            $like->setPostId($post->getId());
            $like->setLiked($liked);
            $like->setCreatedAt(new \DateTime());
        }

        $this->entityManager->persist($like);
        $this->entityManager->flush();

        $postTotalLikes = count($this->likeRepository->findBy(['postId' => $post->getId(), 'liked' => true]));

        return new Response($postTotalLikes);
    }
}

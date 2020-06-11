<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Form\UserType;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/gestion")
 */
class BackController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManagerInterface;
        $this->request = $requestStack->getCurrentRequest();
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->postRepository = $this->entityManager->getRepository(Post::class);
        $this->timezone = 'America/Argentina/Buenos_Aires';
        date_default_timezone_set($this->timezone);
    }

    /**
     * @Route("/mi-cuenta", name="dashboard", methods={"GET"})
     */
    public function index()
    {
        // Checks if the user is admin
        $posts = $this->postRepository->findBy(['user' => $this->getUser()], ['id' => 'DESC']);

        return $this->render('blog_back/dashboard.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/publicaciones", name="post_manage", methods={"GET"})
     */
    public function postManage()
    {
        // Checks if the user is admin
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            // TODO: send notification error 'This user is not allow to change another user\'s post'
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('blog_back/post/manage.html.twig', [
            'posts' => $this->postRepository->findBy([], ['id' => 'DESC'])
        ]);
    }

     /**
     * @Route("/generar-publicacion", name="post_new", methods={"GET","POST"})
     */
    public function new()
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData()->setCreatedAt(new \DateTime);
            $form->getData()->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('blog_back/post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-publicacion/{id}", name="post_edit", methods={"GET","POST"})
     */
    public function edit(Post $post)
    {
        // Checks if the postId is in the database
        $checkedPost = $this->postRepository->findOneBy(['id' => $post]);
        if (!$checkedPost instanceof Post) {
            // TODO: send notification error 'Post does not exist'
            return $this->redirectToRoute('dashboard');

        } elseif (!($this->getUser()->getId() === $checkedPost->getUser()->getId())) {
            // Previous line checks if the current user is the actual owner of the post
            // TODO: send notification error 'This user is not allow to change another user\'s post'
            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData()->setUpdatedAt(new \DateTime);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('blog_back/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/publicacion/{id}", name="post_show", methods={"GET"})
     */
    public function show(Post $post)
    {
        return $this->render('blog_back/post/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/eliminar-publicacion/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete(Post $post)
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $this->request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/rechazar-publicacion/{id}", name="post_reject", methods={"POST"})
     */
    public function reject(Post $post)
    {
        if ($this->request->request->get('_token')) {
            $entityManager = $this->getDoctrine()->getManager();
            $post->setUpdatedAt(new \DateTime);
            $post->setRejected(true);
            $entityManager->persist($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_manage');
    }

    /**
     * @Route("/aprobar-publicacion/{id}", name="post_unreject", methods={"POST"})
     */
    public function unreject(Post $post)
    {
        if ($this->request->request->get('_token')) {
            $entityManager = $this->getDoctrine()->getManager();
            $post->setUpdatedAt(new \DateTime);
            $post->setRejected(false);
            $entityManager->persist($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_manage');
    }
}

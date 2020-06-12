<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profile;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\UserType;
use App\Form\EditPasswordType;
use App\Form\ProfileType;
use App\Form\ProfilePermissionType;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/gestion")
 */
class BackController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        RequestStack $requestStack,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManagerInterface;
        $this->request = $requestStack->getCurrentRequest();
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->profileRepository = $this->entityManager->getRepository(Profile::class);
        $this->postRepository = $this->entityManager->getRepository(Post::class);
        $this->commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->timezone = 'America/Argentina/Buenos_Aires';
        date_default_timezone_set($this->timezone);
    }

    /**
     * @Route({"/", "/mi-cuenta"}, name="dashboard", methods={"GET"})
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
            $this->addFlash('danger', 'Acción no permitida');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('blog_back/post/manage.html.twig', [
            'posts' => $this->postRepository->findBy([], ['id' => 'DESC'])
        ]);
    }

     /**
     * @Route("/nueva-publicacion", name="post_new", methods={"GET","POST"})
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

            $this->addFlash('success', 'Nueva publicación generada con éxito!');

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
            $this->addFlash('danger', 'No existen publicaciones con ese identificador');

            return $this->redirectToRoute('dashboard');

        } elseif (!($this->getUser()->getId() === $checkedPost->getUser()->getId())) {
            // Previous line checks if the current user is the actual owner of the post
            $this->addFlash('danger', 'Acción no permitida: solo el autor de la publicación puede editarla');

            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData()->setUpdatedAt(new \DateTime);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Publicación actualizada con éxito!');

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

            $this->addFlash('success', 'Publicación eliminada con éxito!');
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

            $this->addFlash('success', 'Publicación rechazada');
        }

        return $this->redirectToRoute('post_manage');
    }

    /**
     * @Route("/aceptar-publicacion/{id}", name="post_unreject", methods={"POST"})
     */
    public function unreject(Post $post)
    {
        if ($this->request->request->get('_token')) {
            $entityManager = $this->getDoctrine()->getManager();
            $post->setUpdatedAt(new \DateTime);
            $post->setRejected(false);
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Publicación aceptada');
        }

        return $this->redirectToRoute('post_manage');
    }

    /**
     * @Route("/mi-perfil/{id}", name="profile", methods={"GET"})
     */
    public function profile(User $user)
    {
        // Checks if the user exists
        $checkedUser = $this->userRepository->findOneBy(['id' => $this->getUser()]);

        if (!$checkedUser instanceof User) {
            $this->addFlash('danger', 'El usuario no existe');

            return $this->redirectToRoute('dashboard');

        } elseif (!($this->getUser()->getId() === $checkedUser->getId())) {
            // Previous line checks if the current user is the actual owner of the post
            $this->addFlash('danger', 'Acción no permitida');

            return $this->redirectToRoute('dashboard');
        }

        $comments = $this->commentRepository->findBy(['user' => $user]);

        return $this->render('blog_back/user/show.html.twig', [
            'user' => $user,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/editar-perfil/{id}", name="profile_edit", methods={"GET","POST"})
     */
    public function editProfile(User $user)
    {
        // Checks if the user is in the database
        $checkedUser = $this->userRepository->findOneBy(['id' => $user]);
        if (!$checkedUser instanceof User) {
            $this->addFlash('danger', 'El usuario no existe');

            return $this->redirectToRoute('dashboard');

        } elseif (!($this->getUser()->getId() === $checkedUser->getId())) {
            // Previous line checks if the current user is the actual owner of the post
            $this->addFlash('danger', 'Acción no permitida');

            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if username or email is on use
            $checkEmail = $this->userRepository->findOneBy(['email' =>  $form->getData()->getEmal()]);
            if ($checkEmail instanceof User) {
                $this->addFlash('danger', 'El email esta en uso');
            }
            $checkUsername = $this->userRepository->findOneBy(['username' =>  $form->getData()->getUsername()]);
            if ($checkUsername instanceof User) {
                $this->addFlash('danger', 'El nombre de usuario esta en uso');
            }

            $form->getData()->setUpdatedAt(new \DateTime);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Perfil actualizado con éxito!');

            return $this->redirectToRoute('profile', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('blog_back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/editar-contrasena-usuario/{id}", name="profile_edit_password", methods="GET|POST")
     */
    public function editPassword(User $user)
    {
        // Checks if the usser is in the database
         if (!$user instanceof User) {
            $this->addFlash('danger', 'El usuario no existe');

            return $this->redirectToRoute('dashboard');

         } elseif (!($this->getUser()->getId() === $user->getId())) {
            // Previous line checks if the current user is the actual owner of the post
            $this->addFlash('danger', 'Acción no permitida');

            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(EditPasswordType::class, $user);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Contraseña actualizada!');

            return $this->redirectToRoute('profile', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('blog_back/user/edit_password.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/editar-imagen-perfil/{id}", name="profile_edit_image", methods="GET|POST")
     */
    public function editImage(User $user)
    {
        // Checks if the usser is in the database
        $checkedUser = $this->userRepository->findOneBy(['id' => $user]);
         if (!$checkedUser instanceof User) {
            $this->addFlash('danger', 'El usuario no existe');

            return $this->redirectToRoute('dashboard');

         } elseif (!($this->getUser()->getId() === $checkedUser->getId())) {
            // Previous line checks if the current user is the actual owner of the post
            $this->addFlash('danger', 'Acción no permitida');

            return $this->redirectToRoute('dashboard');
        }

        $profile = $this->profileRepository->findOneBy(['user' => $user]);

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Imagen actualizada!');

            return $this->redirectToRoute('profile', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('blog_back/user/edit_image.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/perfiles", name="profiles_manage", methods={"GET"})
     */
    public function profiles()
    {
        // Checks if the user is admin
        $users = $this->userRepository->findBy([], ['id' => 'DESC']);

        return $this->render('blog_back/user/profiles.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/administrar/perfil/{id}", name="edit_permission", methods={"GET","POST"})
     */
    public function profilePermissions(User $user)
    {
        // Checks if the usser is in the database
        if (!$user instanceof User) {
            $this->addFlash('danger', 'El usuario no existe');

            return $this->redirectToRoute('dashboard');

        } elseif (
            // Checking user access
            // Is allowed when:
            // User has the ROLE_SUPER_ADMIN
            // The user that is beeing editing does not have a ROLE_SUPER_ADMIN
            // * and if it has it can only be the current logged user
            !in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles()) ||
            (in_array('ROLE_SUPER_ADMIN', $user->getRoles()) && $this->getUser()->getId() != $user->getId())
        ) {
            // Previous line checks if the current user is the actual owner of the post
            $this->addFlash('danger', 'Acción no permitida');

            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(ProfilePermissionType::class, $user);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Cambios actualizados');

            return $this->redirectToRoute('profiles_manage');
        }

        return $this->render('blog_back/user/edit_permission.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}

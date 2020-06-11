<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\RegisterType;
use App\Entity\User;

class SecurityController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        AuthenticationUtils $authenticationUtils,
        RequestStack $requestStack,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManagerInterface;
        $this->authenticationUtils = $authenticationUtils;
        $this->request = $requestStack->getCurrentRequest();
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login() {
        if ($this->getUser()) {
            return $this->redirectToRoute('blog');
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('blog');
    }

     /**
     * @Route("/registro", name="register")
     */
    public function register() {
        if ($this->getUser()) {
            return $this->redirectToRoute('blog');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get username
            $username = $form->getData()->getUsername();
            // Checks is username is already taken
            $checkUsername = $this->userRepository->findOneBy(['username' => $username]);
            // Get email
            $email = $form->getData()->getEmail();
            // Checks is email is already taken
            $checkEmail = $this->userRepository->findOneBy(['email' => $email]);
            // Validating username and email uniqueness
            if ($checkUsername === null && $checkEmail === null) {
                $user->setRoles(['ROLE_USER']);
                $password = $form->getData()->getPassword();
                $form->getData()->setCreatedAt(new \DateTime);
                $password = $this->passwordEncoder->encodePassword($form->getData(), $password);
                $form->getData()->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Te regístraste con éxito!.');

                return $this->redirectToRoute('app_login');
            } elseif ($checkUsername !== null) {
                $this->addFlash('error', 'Este nombre de usuario ya se encuentra en uso.');
            } else {
                $this->addFlash('error', 'Este email ya se encuentra en uso.');
            }
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

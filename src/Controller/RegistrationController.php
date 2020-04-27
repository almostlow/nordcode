<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_feed');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Registration successfull'
            );
            return $this->redirectToRoute('app_feed');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
    * @Route("/validate", name="app_validate", methods={"POST"})
    */
    public function validate(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $user = new User();
        $email = $request->get('email');
        $user->setEmail($email);
        $errors = $validator->validate($user);
        $errorArr = [];
        if (!empty($errors)) {
            foreach ($errors as $error) {
                if ($error->getPropertyPath() !== 'email') {
                    continue;
                }
                $errorArr[$error->getPropertyPath()][] = $error->getMessage();
            }
        }
        return new JsonResponse(['errors' => $errorArr]);
    }
}

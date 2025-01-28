<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UtilisateurController extends AbstractController
{
    private UtilisateurRepository $utilisateurRepository;
    private EntityManagerInterface $entityManager;  

    public function __construct(UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $users = $this->utilisateurRepository->findAll();

        return $this->render('utilisateur/utilisateur.html.twig', [
            'users' => $users,
        ]);
    }

    public function delete(int $id, EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(Utilisateur::class)->find($id);
    if (!$user) {
        throw $this->createNotFoundException('user non trouvé.');
    }

    $imagePath = $user->getProfileImage();
    if ($imagePath && file_exists($this->getParameter('uploads_directory') . '/' . $imagePath)) {
        unlink($this->getParameter('uploads_directory') . '/' . $imagePath);
    }

    $entityManager->remove($user);
    $entityManager->flush();


    return $this->redirectToRoute('utilisateurs');  
}
}

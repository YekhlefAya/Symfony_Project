<?php

namespace App\Controller;

use App\Entity\Screening;
use App\Repository\RoomRepository;
use App\Repository\MovieRepository;
use App\Repository\ScreeningRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ScreeningController extends AbstractController
{
    private ScreeningRepository $screeningRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ScreeningRepository $screeningRepository, EntityManagerInterface $entityManager)
    {
        $this->screeningRepository = $screeningRepository;
        $this->entityManager = $entityManager;
    }
    public function index(ScreeningRepository $screeningRepository, MovieRepository $movieRepository, RoomRepository $roomRepository): Response
    {
        $screenings = $screeningRepository->findAll(); 

        $movies = $movieRepository->findAll();
        $rooms = $roomRepository->findAll();

        return $this->render('screening/screening.html.twig', [
            'screenings' => $screenings,
            'movies' => $movies,
            'rooms' => $rooms,
        ]);
    }
    public function insert(Request $request, MovieRepository $movieRepository, RoomRepository $roomRepository): Response
{
    $movieId = $request->request->get('movie');  
    $date = new \DateTime($request->request->get('date'));  
    $time = new \DateTime($request->request->get('time'));  
    $roomId = $request->request->get('room');  
    $version = $request->request->get('version');  
    $price = (float) $request->request->get('price'); 
    $status = $request->request->get('status');  

    $movie = $movieRepository->find($movieId);
    $room = $roomRepository->find($roomId);

    if (!$movie || !$room) {
        throw $this->createNotFoundException('Film ou Salle non trouvé.');
    }

    $screening = new Screening();
    $screening->setMovie($movie);
    $screening->setDate($date);
    $screening->setTime($time);
    $screening->setRoom($room);
    $screening->setVersion($version);
    $screening->setPrice($price);
    $screening->setStatus($status);

    $this->entityManager->persist($screening);
    $this->entityManager->flush();

    return $this->redirectToRoute('screenings'); 
}
public function edit(int $id, MovieRepository $movieRepository, RoomRepository $roomRepository): Response
    {
        $screening = $this->screeningRepository->find($id);

        if (!$screening) {
            throw $this->createNotFoundException('Séance non trouvée');
        }

        $movies = $movieRepository->findAll();
        $rooms = $roomRepository->findAll();

        return $this->render('screening/edit-screening.html.twig', [
            'screening' => $screening,
            'movies' => $movies,
            'rooms' => $rooms,
        ]);
    }
    public function update(int $id, Request $request, MovieRepository $movieRepository, RoomRepository $roomRepository): Response
    {
        $screening = $this->screeningRepository->find($id);

        if (!$screening) {
            throw $this->createNotFoundException('Séance non trouvée');
        }

        $movieId = $request->request->get('movie');
        $date = new \DateTime($request->request->get('date'));
        $time = new \DateTime($request->request->get('time'));
        $roomId = $request->request->get('room');
        $version = $request->request->get('version');
        $price = (float) $request->request->get('price');
        $status = $request->request->get('status');

        $movie = $movieRepository->find($movieId);
        $room = $roomRepository->find($roomId);

        if (!$movie || !$room) {
            throw $this->createNotFoundException('Film ou Salle non trouvée.');
        }

        // Mise à jour des informations de la séance
        $screening->setMovie($movie);
        $screening->setDate($date);
        $screening->setTime($time);
        $screening->setRoom($room);
        $screening->setVersion($version);
        $screening->setPrice($price);
        $screening->setStatus($status);

        $this->entityManager->persist($screening);
        $this->entityManager->flush();

        return $this->redirectToRoute('screenings');
    }

        
    public function delete(int $id, EntityManagerInterface $entityManager): Response
{
    $screening = $entityManager->getRepository(Screening::class)->find($id);
    if (!$screening) {
        throw $this->createNotFoundException('seance non trouvé.');
    }

    $entityManager->remove($screening);
    $entityManager->flush();


    return $this->redirectToRoute('screenings');  
}
    
}

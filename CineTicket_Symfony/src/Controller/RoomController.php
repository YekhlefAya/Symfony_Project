<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

final class RoomController extends AbstractController
{
    private RoomRepository $roomRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(RoomRepository $roomRepository, EntityManagerInterface $entityManager)
    {
        $this->roomRepository = $roomRepository;
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $rooms = $this->roomRepository->findAll();

        return $this->render('room/room.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    public function insert(Request $request): Response
    {
        $equipments = $request->request->all()['equipment'];        // Si aucun équipement n'est sélectionné (case vide ou null), on initialise un tableau vide
        if (!is_array($equipments)) {
            $equipments = [];
        }
    
        $room = new Room();
        $room->setNumber((int)$request->request->get('roomNumber'));
        $room->setCapacity((int)$request->request->get('capacity'));
        $room->setStatus((string)$request->request->get('status'));
        $room->setEquipment($equipments); 
    
        $this->entityManager->persist($room);
        $this->entityManager->flush();
    
        return $this->redirectToRoute('rooms');
    }
    public function edit(int $id): Response
{
    $room = $this->roomRepository->find($id);

    if (!$room) {
        throw $this->createNotFoundException('Room non trouvé.');
    }
    $screenings = $room->getScreenings(); // Assurez-vous que la relation est définie dans l'entité Room
    foreach ($screenings as $screening) {
        // Supprimer les réservations associées à chaque projection
        $reservations = $screening->getReservations(); // Assurez-vous que la relation est définie dans Screening
        foreach ($reservations as $reservation) {
            $this->entityManager->remove($reservation);
        }
        // Supprimer la projection
        $this->entityManager->remove($screening);
    }
    return $this->render('room/edit-room.html.twig', [
        'room' => $room,
    ]);
}

public function update(Request $request, Room $room): Response
{
    if (!$room) {
        throw $this->createNotFoundException('La salle demandée n\'existe pas.');
    }

    $room->setNumber((int)$request->request->get('roomNumber'));
    $room->setCapacity((int)$request->request->get('capacity'));
    $room->setStatus((string)$request->request->get('status'));
    
    $equipments = $request->request->all()['equipment'];        // Si aucun équipement n'est sélectionné (case vide ou null), on initialise un tableau vide
    $room->setEquipment($equipments);

    $this->entityManager->flush();

    return $this->redirectToRoute('rooms');
}

public function delete(int $id, EntityManagerInterface $entityManager): Response
{
    $room = $entityManager->getRepository(Room::class)->find($id);
    if (!$room) {
        throw $this->createNotFoundException('salle non trouvé.');
    }

    $entityManager->remove($room);
    $entityManager->flush();


    return $this->redirectToRoute('rooms');  
}
    
}

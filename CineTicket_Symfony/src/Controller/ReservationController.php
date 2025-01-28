<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Screening;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    private ReservationRepository $reservationRepository;
    private EntityManagerInterface $entityManager;  

    public function __construct(ReservationRepository $reservationRepository, EntityManagerInterface $entityManager)
    {
        $this->reservationRepository = $reservationRepository;
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $reservations = $this->reservationRepository->findAll();

        return $this->render('reservation/reservation.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    public function edit(int $id): Response
    {
        // Retrieve the reservation
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }
    
        // Retrieve the list of movies
        $movies = $this->entityManager->getRepository(Movie::class)->findAll();
    
        $screenings = $this->entityManager->getRepository(Screening::class)->findAll();
    
    
        return $this->render('reservation/edit-reservation.html.twig', [
            'reservation' => $reservation,
            'movies' => $movies,
            'screenings' => $screenings,
        ]);
    }
    

    
    /**
     * @Route("/reservations/update/{id}", name="update-reservation", methods={"POST"})
     */
   /**
 * @Route("/reservations/update/{id}", name="update-reservation", methods={"POST"})
 */
public function update(int $id, Request $request): Response
{
    $reservation = $this->reservationRepository->find($id);

    if (!$reservation) {
        throw $this->createNotFoundException('Réservation non trouvée');
    }

    // Récupérer et mettre à jour les valeurs du formulaire
    $reservation->setMovie($this->entityManager->getRepository(Movie::class)->find($request->request->get('movie')));
    $reservation->setScreening($this->entityManager->getRepository(Screening::class)->find($request->request->get('screening')));
    $reservation->setStatus($request->request->get('status'));
    $reservation->setComment($request->request->get('comment'));

    // Mettre à jour les places
    $seats = $request->request->all('seats');
    $reservation->setSeats($seats);

    // Sauvegarder les changements en base de données
    $this->entityManager->flush();

    // Rediriger l'utilisateur
    return $this->redirectToRoute('reservations');
}

    /**
     * @Route("/reservations/delete/{id}", name="delete-reservation", methods={"POST"})
     */
    public function delete(int $id): Response
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->redirectToRoute('reservations');
    }
}


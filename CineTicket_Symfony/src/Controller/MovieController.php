<?php
namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;  

final class MovieController extends AbstractController
{
    private MovieRepository $movieRepository;
    private EntityManagerInterface $entityManager;  

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $entityManager)
    {
        $this->movieRepository = $movieRepository;
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();

        return $this->render('movie/movie.html.twig', [
            'movies' => $movies,
        ]);
    }

    public function insert(Request $request): Response
    {
        $movie = new Movie();

        $movie->setTitle($request->request->get('title'));
        $movie->setDirector($request->request->get('director'));
        $movie->setGenre($request->request->get('genre'));
        $movie->setSynopsis($request->request->get('synopsis'));
        $movie->setStatus($request->request->get('status')); 

        $durationString = $request->request->get('duration'); 
        $movie->setDuration(new \DateTime($durationString));

        $imageFile = $request->files->get('image');
        if ($imageFile) {
            $uploadsDirectory = $this->getParameter('uploads_directory');  // assurez-vous d'avoir bien configuré ce paramètre dans les settings de Symfony
            $imageFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $uploadsDirectory,
                $imageFilename
            );
            
            $movie->setImage('uploads/' . $imageFilename);
        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        return $this->redirectToRoute('movies');
    }
}
public function edit(int $id): Response
{
    $movie = $this->movieRepository->find($id);

    if (!$movie) {
        throw $this->createNotFoundException('Film non trouvé.');
    }

    return $this->render('movie/edit-movie.html.twig', [
        'movie' => $movie,
    ]);
}


public function update(Request $request, int $id): Response
{
    $movie = $this->movieRepository->find($id);

    if (!$movie) {
        throw $this->createNotFoundException('Film non trouvé.');
    }

    $title = $request->request->get('title');
    if ($title) {
        $movie->setTitle($title);
    }

    $director = $request->request->get('director');
    if ($director) {
        $movie->setDirector($director);
    }

    $genre = $request->request->get('genre');
    if ($genre) {
        $movie->setGenre($genre);
    }

    $synopsis = $request->request->get('synopsis');
    if ($synopsis) {
        $movie->setSynopsis($synopsis);
    }

    $status = $request->request->get('status');
    if ($status) {
        $movie->setStatus($status);
    }

    $durationString = $request->request->get('duration');
    if ($durationString) {
        try {
            $movie->setDuration(new \DateTime($durationString));
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Durée non valide.');
        }
    }

    $imageFile = $request->files->get('image');
    if ($imageFile) {
        $uploadsDirectory = $this->getParameter('uploads_directory');
        $imageFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();

        $imageFile->move($uploadsDirectory, $imageFilename);

        $movie->setImage('uploads/' . $imageFilename);
    }

    $this->entityManager->flush();

    return $this->redirectToRoute('movies');
}

public function delete(int $id): Response
{
    $movie = $this->movieRepository->find($id);

    if (!$movie) {
        throw $this->createNotFoundException('Film non trouvé.');
    }

    

    // Supprimer l'image associée au film
    $imagePath = $movie->getImage();
    if ($imagePath && file_exists($this->getParameter('uploads_directory') . '/' . $imagePath)) {
        unlink($this->getParameter('uploads_directory') . '/' . $imagePath);
    }

    // Supprimer le film
    $this->entityManager->remove($movie);
    $this->entityManager->flush();

    return $this->redirectToRoute('movies');
}



}
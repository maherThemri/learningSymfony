<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

#[
    Route('/personne'),
    IsGranted("ROLE_USER")
]
class PersonneController extends AbstractController
{

    public function __construct(
        private LoggerInterface $logger,
        private Helpers $helper,
        private EventDispatcherInterface $dispatcher
    ) {
    }
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();
        return $this->render(
            'personne/index.html.twig',
            ['personnes' => $personnes]
        );
    }
    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function personneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);

        return $this->render(
            'personne/index.html.twig',
            ['personnes' => $personnes]
        );
    }
    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.stats.age')]
    public function satatsPersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax, Helpers $logger): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonneByAgeInterval($ageMin, $ageMax);
        return $this->render(
            'personne/stats.html.twig',
            ['stats' => $stats[0], 'ageMin' => $ageMin, 'ageMax' => $ageMax]
        );
    }
    #[
        Route('/alls/{page?1}/{nbr?12}', name: 'personne.list.alls'),
        IsGranted("ROLE_USER")
    ]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbr): Response
    {

        echo ($this->helper->sayCc());
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        $nbrPage = ceil($nbPersonne / $nbr);

        $personnes = $repository->findBy([], ['id' => 'ASC'], $nbr, ($page - 1) * $nbr);
        $listAllPersonneEvent = new ListAllPersonneEvent(count($personnes));
        $this->dispatcher->dispatch($listAllPersonneEvent, ListAllPersonneEvent::LIST_ALL_PERSONNE_EVENT);
        return $this->render(
            'personne/index.html.twig',
            [
                'personnes' => $personnes,
                'isPaginated' => true,
                'nbrPage' => $nbrPage,
                'page' => $page,
                'nbr' => $nbr
            ]
        );
    }
    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personne = $repository->find($id);
        if (!$personne) {
            $this->addFlash('error', "la personne d'id $id n'existe pas");
            return $this->redirectToRoute('personne.list');
        }
        return $this->render(
            'personne/detail.html.twig',
            ['personne' => $personne]
        );
    }
    #[Route('/edit/{id?0}', name: 'personne.edit')]
    #[ParamConverter('personne', class: 'App\Entity\Personne', options: ['id' => 'id'])]

    public function addPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailerService $mailer
    ): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $new = false;
        if (!$personne) {
            $personne = new Personne();
            $new = true;
        }

        // $personne est l'image de notre formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        // Mon formulaire va traiter la requete
        $form->handleRequest($request);
        // est ce que le formulaire a été soumis

        if ($form->isSubmitted()) {
            $image = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $directory = $this->getParameter('personne_directory');
                $personne->setImage($uploaderService->UploadedFile($image, $directory));
            }
            // si oui on va l'ajouter l'objet personne dans la base de données
            // $this->getDoctrine(): version sf <=5
            $manager = $doctrine->getManager();


            $manager->persist($personne);
            $manager->flush();

            if ($new) {
                $message = 'a été ajout avec succès';
                $personne->setCreatedBy($this->getUser());
            } else {
                $message = 'a été mis a jour avec succès';
            }
            if ($new) {
                // on a creer notre evenement
                $addEventPersonne = new AddPersonneEvent($personne);
                // on va maintenant dispatcher cet évenemnt
                $this->dispatcher->dispatch($addEventPersonne, AddPersonneEvent::ADD_PERSONNE_EVENT);
            }
            // Affichier message de succès
            $this->addFlash('success',  $message);
            // Rediriger vers la liste personne 
            return $this->redirectToRoute('personne.list');
        } else {

            // sinon on affiche notre formulaire
            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }
    #[
        Route('/delete/{id}', name: 'personne.delete'),
        IsGranted("ROLE_ADMIN")
    ]
    #[ParamConverter('personne', class: 'App\Entity\Personne', options: ['id' => 'id'])]

    public function deletePersonne(ManagerRegistry $doctrine, Personne $personne = null): RedirectResponse
    {
        // Récupérer le personne 
        if ($personne) {
            // Si la personne existe => le supprimer et retourner un flashMessage de succès
            $manager = $doctrine->getManager();
            // Ajoute la fonction de suppression dans la transaction
            $manager->remove($personne);
            // Exécuter la transaction
            $manager->flush();
            $this->addFlash('success', "la personne a été supprimé avec succès");
        } else {
            // sinon retourne un flashMessage d'erreur
            $this->addFlash('error', "Personne innexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }
    #[Route('/update/{id}/{firstName}/{name}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $name, $firstName, $age): Response
    {
        // Vérifier que la personne à mettre a jour existe
        if ($personne) {
            // si la personne existe => mettre a jour notre personne + message de success
            $personne->setName($name);
            $personne->setFirstName($firstName);
            $personne->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success', "la personne a été mis à jour avec succès");
        } else {
            // sinon personne n'esxite pas => message d'erreur
            $this->addFlash('error', "Personne innexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }
}

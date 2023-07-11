<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/to_do')]
class ToDoController extends AbstractController
{
    /**
     *  @Route("/todo",name="todo")
     */
    #[Route('/', name: 'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        // afficher notre tableau de todo
        // si je l'initialise puis j'affiche
        if (!$session->has(name: 'todos')) {
            $todos = [
                'achat' => 'acheter clè usb',
                'cours' => 'Finaliser mon cours',
                'correction' => 'corriger mes examen'
            ];
            $session->set('todos', $todos);
            $this->addFlash('info', 'La liste des todos viens d etre initialisée');
        }
        // sinon j ai mon tableau de todo dans ma session je ne fait que l'afficher

        return $this->render('to_do/index.html.twig');
    }
    #[Route(
        '/add/{name}/{content}',
        name: 'todo.add',
        defaults: ['content' => 'sf6']
    )]
    public function addTodo(Request $request, $name, $content): RedirectResponse
    {
        $session = $request->getSession();
        // vérifier si j'ai mon tableau de todo dans la session
        if ($session->has('todos')) {
            // si oui 
            // vérifier si on a deja un todo avec le meme name
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                // si oui afficher erreur
                $this->addFlash('info', "le todo d id " . $name . " existe deja dans a liste");
            } else {
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash('success', "Le todo d id " .  $name . " a été ajouté avec succes");
            }
            // si non on l'ajouter et on affiche un message de succés
        } else {
            // si non 
            // afficher une erreur et on va redirger vers le controlleur index
            $this->addFlash('error', 'La liste des todos viens n est pas encore initialisée');
        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo(Request $request, $name, $content): RedirectResponse
    {
        $session = $request->getSession();
        // vérifier si j'ai mon tableau de todo dans la session
        if ($session->has('todos')) {
            // si oui 
            // vérifier si on a deja un todo avec le meme name
            $todos = $session->get('todos');
            if (!isset($todos[$name])) {
                // si oui afficher erreur
                $this->addFlash('info', "le todo d id " . $name . " n'existe pas dans a liste");
            } else {
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash('success', "Le todo d id " .  $name . " a été modifié avec succes");
            }
            // si non on l'ajouter et on affiche un message de succés
        } else {
            // si non 
            // afficher une erreur et on va redirger vers le controlleur index
            $this->addFlash('error', 'La liste des todos viens n est pas encore initialisée');
        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteTodo(Request $request, $name): RedirectResponse
    {
        $session = $request->getSession();
        // vérifier si j'ai mon tableau de todo dans la session
        if ($session->has('todos')) {
            // si oui 
            // vérifier si on a deja un todo avec le meme name
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                // si oui afficher erreur
                $this->addFlash('info', "le todo d id " . $name . " existe deja dans a liste");
            } else {
                // si non on l'ajouter et on affiche un message de succés
                unset($todos[$name]);
                $session->set('todos', $todos);
                $this->addFlash('success', "Le todo d id " .  $name . " a été supprimé avec succes");
            }
        } else {
            // si non 
            // afficher une erreur et on va redirger vers le controlleur index
            $this->addFlash('error', 'La liste des todos viens n est pas encore initialisée');
        }
        return $this->redirectToRoute('todo');
    }
    #[Route('/reset', name: 'todo.reset')]
    public function resetTodo(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $session->remove('todos');
        return $this->redirectToRoute('todo');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/first', name: 'first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig', [
            'name' => 'maher',
            'firstName' => 'thamri',
        ]);
    }
    #[Route('/sayHello/{name}/{lastName}', name: 'say.hello')]
    public function sayHello(Request $request, $name, $lastName): Response
    {
        //    $rand=rand(0,10);
        //    echo $rand;
        //     // $rand=3;
        //     if ($rand%2==0) {
        //         return $this->redirectToRoute('first');
        //     }
        //     return $this->forward('App\Controller\FirstController::index', [
        //         'name' => 'amin',
        //         'firstName' => 'amin',
        //     ]);
        // return $this->render('first/hello.html.twig', [
        //     'name' => 'maher',
        //     'firstName' => 'thamri',
        // ]);
        // dd($request);
        return $this->render(
            'first/hello.html.twig',
            [
                'nom' => $name,
                'prenom' => $lastName
            ]
        );
    }
    #[Route(
        'multi/{entier1<\d>}/{entier2<\d>}',
        name: 'multiplication'
    )]
    public function multiplication($entier1, $entier2)
    {
        $resultat = $entier1 * $entier2;
        return new Response("<h1>$resultat</h1>");
    }
}

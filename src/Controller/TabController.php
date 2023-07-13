<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TabController extends AbstractController
{

    #[Route('/tab/{nb<\d+>?5}', name: 'app_tab')]
    public function index(int $nb): Response
    {
        $notes = [];
        for ($i = 0; $i < $nb; $i++) {
            $notes[] = rand(0, 20);
        }

        return $this->render('tab/index.html.twig', [
            'notes' => $notes,
        ]);
    }
    #[Route('/tab/users', name: 'tab.users')]
    public function users(): Response
    {
        $users = [
            ['firstName' => 'maher', 'name' => 'thamri', 'age' => '28'],
            ['firstName' => 'skander', 'name' => 'chaben', 'age' => '38'],
            ['firstName' => 'ahmed', 'name' => 'thamri', 'age' => '29'],
            ['firstName' => 'amine', 'name' => 'thamri', 'age' => '16'],
        ];
        return $this->render('tab/users.html.twig', ['users' => $users]);
    }
}

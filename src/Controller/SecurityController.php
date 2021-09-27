<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    #[Route('/api/login', name:'api_login', methods:['POST'])] /*ajouter path si ça fonctionne pas*/
    public function login(){
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUsername(),
            'roles'=> $user->getRoles()
        ]);
    }

    #[Route('/api/logout', name:'api_logout', methods:['POST'])]
    public function logout(){

    }
}
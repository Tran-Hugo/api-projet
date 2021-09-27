<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        
    }

    public function __invoke(Request $request)
    {
        $user = $request->get('data');
        $user->setPassword($this->hasher->hashPassword($user,$user->getPassword()));
        $data = $user;
        return $data;
        
    }
}
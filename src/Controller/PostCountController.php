<?php

namespace App\Controller;


use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;

class PostCountController
{
    
    
    public function __invoke(PostRepository $postRepository, Request $request): int
    {
        $onlineQuery = $request->get('online');
        $conditions=[];
        if($onlineQuery){
            $conditions = ['online'=>$onlineQuery == '1' ? true : false];
        }
        return $postRepository->count($conditions);
    }
    
}
<?php

namespace App\Controller;

use App\Entity\Post;
use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostImageController
{
    public function __invoke(Post $post, Request $request){

        if(!($post instanceof Post)) {
            throw new RuntimeException('Article attendu');
        }
        $file = $request->files->get('file');
        $post->setFile($file);
        $post->setUpdatedAt(new DateTimeImmutable());
        return $post;
        
    }
}
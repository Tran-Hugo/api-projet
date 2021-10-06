<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostCompletController extends AbstractController
{
    public function __invoke(Request $request, CategoryRepository $repo)
    {
        $post = new Post;
        $title = $request->request->get('title');
        $slug = $request->request->get('slug');
        $content = $request->request->get('content');
        $category = $request->request->get('category');
        $cat= $repo->findOneBy(['id'=>$category]);
        $file = $request->files->get('file');

        $post->setTitle($title);
        $post->setSlug($slug);
        $post->setContent($content);
        $post->setCategory($cat);
        $post->setFile($file);
        
        return $post;

    }
}
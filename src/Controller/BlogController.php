<?php

namespace App\Controller;

use App\Service\Greeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("blog")
 */
class BlogController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[1] = [
            'id' => 1,
            'title' => 'a random title' . rand(1, 500),
            'text' => 'random text' . rand(1, 500),
            'date'=> new \DateTime(),
        ];
        $this->session->set('posts', $posts);

        return $this->redirectToRoute('blog_index');
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'posts' => $this->session->get('posts')
        ]);
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');

        if (!$posts || !isset($posts[$id])) {
            throw  new NotFoundHttpException('post not found');
        }

        return $this->render('blog/post.html.twig', [
                'post' => $posts[$id],
        ]);
    }
}

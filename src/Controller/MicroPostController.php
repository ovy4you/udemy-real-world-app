<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    private $microPostRepository;
    private $formFactory;
    private $entityManager;
    private $flashBag;

    public function __construct(
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag
    )
    {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/", name="micro_post")
     */
    public function index()
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $this->microPostRepository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request)
    {
        $microPost = new MicroPost();
        $microPost->setTime(new \DateTime());
        $microPost->setUser($this->getUser());

        $form = $this->formFactory->create(MicroPostType::class, $microPost);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();
            return $this->redirectToRoute('micro_post');
        }

        return $this->render('micro_post/add.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/{id}", name="micro_post_post")
     */
    public function post(MicroPost $microPost)
    {
        $this->denyAccessUnlessGranted('micro_post_view', $microPost);

        return $this->render('micro_post/post.html.twig',
            ['post' => $microPost]
        );
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     */
    public function edit(MicroPost $microPost, Request $request)
    {
        $this->denyAccessUnlessGranted('micro_post_edit', $microPost);

        $form = $this->formFactory->create(MicroPostType::class, $microPost);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();
            return $this->redirectToRoute('micro_post');
        }

        return $this->render('micro_post/add.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     */
    public function delete(MicroPost $microPost)
    {
        $this->denyAccessUnlessGranted('micro_post_delete', $microPost);

        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->flashBag->add('notice','micropost was deleted');

        return $this->redirectToRoute('micro_post');
    }
}

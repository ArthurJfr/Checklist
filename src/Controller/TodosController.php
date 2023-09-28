<?php

namespace App\Controller;

use App\Entity\Todos;
use App\Form\TodosType;
use App\Repository\TodosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todos')]
class TodosController extends AbstractController
{
    #[Route('/', name: 'app_todos_index', methods: ['GET'])]
    public function index(TodosRepository $todosRepository): Response
    {
        return $this->render('todos/index.html.twig', [
            'todos' => $todosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_todos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $todo = new Todos();
        $form = $this->createForm(TodosType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->redirectToRoute('app_todos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todos/new.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_todos_show', methods: ['GET'])]
    public function show(Todos $todo): Response
    {
        return $this->render('todos/show.html.twig', [
            'todo' => $todo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_todos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Todos $todo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TodosType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_todos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todos/edit.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_todos_delete', methods: ['POST'])]
    public function delete(Request $request, Todos $todo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($todo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_todos_index', [], Response::HTTP_SEE_OTHER);
    }
}

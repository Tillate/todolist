<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="task")
     */
    public function tasks(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/task/add", methods={"GET","POST"}, name="task_add")
     * 
     */
    public function addTask(): Response
    {
        
        return $this->render('tasks/addTask.html.twig') ;
    }

    /**
     * @Route("/task/add/save", methods={"POST"}, name="task_add_save")
     */
    public function addSaveTask(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $task = new Task();
        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setStartDate(new \DateTime($request->request->get('start_date')));
        $task->setEndDate(new \DateTime($request->request->get('end_date')));
        
        
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('task');
    }

}

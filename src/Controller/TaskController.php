<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("project/{id}/task", name="task")
     */
    public function tasks(Project $project): Response
    {
        $tasks = $project->getTasks();

        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasks,
            'project' => $project
        ]);
    }

    /**
     * @Route("/project/{id}/task/add", methods={"GET","POST"}, name="task_add")
     * 
     */
    public function addTask( Project $project): Response
    {
        return $this->render('tasks/addTask.html.twig', [
            'project' => $project
        ]);
    }

    /**
     * @Route("/task/{id}/add/save", methods={"POST"}, name="task_add_save")
     */
    public function addSaveTask(ManagerRegistry $doctrine, Request $request,Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $task = new Task();

        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setStartDate(new \DateTime($request->request->get('start_date')));
        $task->setEndDate(new \DateTime($request->request->get('end_date')));
        $task->setProject($project);
        
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('task', ['id'=> $project->getId()]);
    }

    /**
     * @Route("/project/{project_id}/task/{task_id}/edit", name="task_edit")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function editTask(Task $task,Project $project): Response
    {

        return $this->render('tasks/editTask.html.twig', [
            'task' => $task,
            'project'=> $project,
        ]);
    }

    /**
     * @Route("/task/{project_id}/saveEdit/{task_id}", methods={"POST"}, name="task_edit_save")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function saveEditTask(ManagerRegistry $doctrine, Request $request,Task $task,Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setStartDate(new \DateTime($request->request->get('start_date')));
        $task->setEndDate(new \DateTime($request->request->get('end_date')));
        $task->setProject($project);
        
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('task', ['id'=> $project->getId()]);
    }

    /**
     * @Route("/task/{project_id}/delete/{task_id}", methods={"GET", "DELETE"}, name="task_delete")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function deleteTask(ManagerRegistry $doctrine, Task $task, Project $project): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('task', ['id'=> $project->getId()]);
    }

}

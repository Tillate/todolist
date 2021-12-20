<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project", name="project")
     */
    public function projects(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/project/add", methods={"GET","POST"}, name="project_add")
     * 
     */
    public function addProject(UserRepository $userRepository): Response
    {
        
        return $this->render('project/addProject.html.twig') ;
    }

    /**
     * @Route("/project/add/save", methods={"POST"}, name="project_add_save")
     */
    public function projectsAddSave(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $project = new Project();
        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
        $project->setStartDate(new \DateTime($request->request->get('start_date')));
        $project->setEndDate(new \DateTime($request->request->get('end_date')));
        
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('project');
    }

    /**
     * @Route("/project/edit/{id}", name="project_edit")
     */
    public function editProject(Project $project): Response
    {
        return $this->render('project/editProject.html.twig', [
            'project' => $project,
        ]);
    }

    

}

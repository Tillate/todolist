<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function addProject(): Response
    {
        
        return $this->render('project/addProject.html.twig') ;
    }

    /**
     * @Route("/project/add/save", methods={"POST"}, name="project_add_save")
     */
    public function addSaveProject(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): Response
    {
        $entityManager = $doctrine->getManager();

        $format = 'd/m/Y';

        $project = new Project();
        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
        $project->setStartDate(new \DateTime($request->request->get('start_date')));
        $project->setEndDate(new \DateTime($request->request->get('end_date')));
        //$project->setStartDate(\DateTime::createFromFormat($format,$request->request->get('start_date')));
        //$project->setEndDate(\DateTime::createFromFormat($format,$request->request->get('end_date')));
        
        $errors = $validator->validate($project);

        if (count($errors) > 0) {
            // $errorsString = (string) $errors;
            $this->addFlash('error', $errors);

            return $this->render('project/addProject.html.twig');
        }

        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('project');
    }

    /**
     * @Route("/project/edit/{id}", name="project_edit")
     */
    public function editProject(Project $project): Response
    {
        $users = $project->getUsers();

        return $this->render('project/editProject.html.twig', [
            'project' => $project,
            'users'=> $users
        ]);
    }

    /**
     * @Route("/project/save/{id}", methods={"POST"}, name="project_save_id")
     */
    public function saveIdProject(ManagerRegistry $doctrine, Request $request, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
        $project->setStartDate(new \DateTime($request->request->get('start_date')));
        $project->setEndDate(new \DateTime($request->request->get('end_date')));
        
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('project');
    }

    /**
     * @Route("/project/delete/{id}", methods={"GET"}, name="project_delete_id")
     */
    public function deleteIdProject(ManagerRegistry $doctrine, Project $project): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($project);
        $entityManager->flush();

        return $this->redirectToRoute('project');
    }

    /**
     * @Route("/project/{id}/user", methods={"GET"}, name="project_addUser")
     */
    public function addUserProject(UserRepository $userRepository, Project $project): Response
    {
        $users = $userRepository->findAll();

        $projectUsers = $project->getUsers();

        return $this->render('project/addUserProject.html.twig', [
            'project' => $project,
            'users' => $users,
            'projectUsers' => $projectUsers,
        ]);
    }

    /**
     * @Route("/project/{id}/user/save", methods={"POST"}, name="project_addUser_save")
     */
    public function addUserSaveProject(LoggerInterface $logger, ManagerRegistry $doctrine, UserRepository $userRepository, Request $request, Project $project): Response
    {

        $entityManager = $doctrine->getManager();

        $project->clearUsers();

        $listIdUser = $request->request->get('user_id', []);

        $logger->debug("valeur userId", ["userID"=>$listIdUser]);

        foreach($listIdUser as $userId) {
            $user= $userRepository->find($userId);
            $project->addUser($user);
        }

        $entityManager->persist($project);
        $entityManager->flush();


        return $this->redirectToRoute('project');
    }

}

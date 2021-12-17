<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;


class UserController extends AbstractController
{
    /**
     * @Route("/user", methods={"GET"}, name="users")
     * 
     */
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }


    /**
     * @Route("/user/add", methods={"GET","POST"}, name="user")
     * 
     */
    public function addUser(UserRepository $userRepository): Response
    {
        
        return $this->render('user/adduser.html.twig') ;
    }


    /**
     * @Route("/user/save", methods={"POST"}, name="user_save")
     */
    public function saveUser(Request $request, ManagerRegistry $doctrine):Response
    {

        $user = new User();
        $user -> setFirstName($request->request->get('first_name'));
        $user -> setLastName($request->request->get('last_name'));
        $user -> setEmail($request->request->get('email'));
        
        $entityManager = $doctrine->getManager();

        $entityManager->persist($user);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        

        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/user/edit/{id}", methods={"GET","POST"}, name="user_edit")
     * 
     */
    public function editUser(UserRepository $userRepository, User $user): Response
    {
        
        return $this->render('user/edituser.html.twig',[
            'userId' => $user->getId(),
            'user' => $user
        ]) ;
    }

     /**
     * @Route("/user/save2/{id}", methods={"POST"}, name="save_user")
     */
    public function saveUser2(Request $request, ManagerRegistry $doctrine, User $user):Response
    {   
        $user -> setFirstName($request->request->get('first_name'));
        $user -> setLastName($request->request->get('last_name'));
        $user -> setEmail($request->request->get('email'));
        
        $entityManager = $doctrine->getManager();

        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/user/delete/{id}", methods={"GET", "DELETE"}, name="user_delete")
     * 
     */
    public function deleteUser(Request $request, User $user, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('users');
    }

}
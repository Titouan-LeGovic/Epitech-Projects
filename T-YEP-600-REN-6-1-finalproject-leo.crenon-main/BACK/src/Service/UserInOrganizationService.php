<?php


namespace App\Service;

use App\Entity\Organization;
use App\Entity\User;
use App\Entity\UserInOrganization;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class UserInOrganizationService {

    private ValidatorInterface $validator;
    private ManagerRegistry $doctrine;
    private UserService $userService;

    //public function __construct(ValidatorInterface $validator, ManagerRegistry $doctrine, UserService $userService)
    public function __construct(ManagerRegistry $doctrine, UserService $userService)
    {
        //$this->validator = $validator;
        $this->doctrine = $doctrine;
        $this->userService = $userService;
    }

    public function prepare(User $user, Organization $organization): UserInOrganization{
        $userInOrganization = new UserInOrganization();
        $userInOrganization->setIdUser($user);
        $userInOrganization->setIdOrganization($organization);
        return $userInOrganization;
    }

    public function save(UserInOrganization $userInOrganization){
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($userInOrganization);
        $entityManager->flush();
    }

    public function delete(UserInOrganization $userInOrganization){
        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($userInOrganization);
        $entityManager->flush();
    }

    public function checkAlreadyInOrganization(User $user, Organization $organization){
        return $this->doctrine->getRepository(UserInOrganization::class)->findBy(["idUser" => $user->getId(), "idOrganization" => $organization->getId()]);
    }

    public function getUserInOrganization(User $user, Organization $organization){
        return $this->doctrine->getRepository(UserInOrganization::class)->findOneBy(
            ["idUser" => $user->getId(), "idOrganization" => $organization->getId()]
        );
    }

    public function getOrganizationList(User $user){
        return $this->doctrine->getRepository(UserInOrganization::class)->findBy(["idUser" => $user->getId()]);
    }

    public function getUserList(Organization $organization){
        return $this->doctrine->getRepository(UserInOrganization::class)->findBy(["idOrganization" => $organization->getId()]);
    }

}

?>
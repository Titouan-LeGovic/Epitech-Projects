<?php

namespace App\Service;

use App\Entity\Organization;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class OrganizationService {

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

    public function validate(Organization $organization) {
        $errors = $this->validator->validate($organization);
        return $errors;
    }

    public function getAll(): array {
        $organizations = $this->doctrine->getRepository(Organization::class)->findAll();
        
        $result = array();

        foreach ($organizations as $key => $organization) {
            $owner = $this->doctrine->getRepository(User::class)->find($organization->getOwner());

            $result[$key]["id"] = $organization->getId();
            $result[$key]["name"] = $organization->getName();
            $result[$key]["owner"]["id"] = $owner->getId();
            $result[$key]["owner"]["firstname"] = $owner->getFirstName();
            $result[$key]["owner"]["lastname"] = $owner->getLastName();
            $result[$key]["owner"]["email"] = $owner->getEmail();
        }
        return $result;
    }

    public function getById(int $id_organization){
        $organization = $this->doctrine->getRepository(Organization::class)->find((int) $id_organization);
        if (!$organization) return false;

        return $organization;
    }

    public function getOrganizationAsArray(Organization $organization) {
        return array(
            'id' => $organization->getId(),
            'name' => $organization->getName(),
            'owner' => [
                "id" => $organization->getOwner()->getId(),
                "firstname" => $organization->getOwner()->getFirstName(),
                "lastname" => $organization->getOwner()->getLastName(),
                "email" => $organization->getOwner()->getEmail(),
            ],
        );
    }

    public function save(Organization $organization) {
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($organization);
        $entityManager->flush();
    }

    public function prepare(Request $request, Organization $organization, User $owner) {
        $content = json_decode($request->getContent(), true);
        $organization->setName($content['name']);
        
        if (!isset($content["userId"])){
            $organization->setOwner($owner);
            return $organization;
        };
        $newOwnerId = $content["userId"];
        if($newOwnerId !== $organization->getOwner()->getId()){
            $newOwner = $this->userService->getUserById($newOwnerId);
            if(!$newOwner) return null;
            $organization->setOwner($newOwner);
        }
        return $organization;
    }

    public function canDo(User $user, Organization $targetOrganization){
        if($targetOrganization->getOwner()->getId() !== $user->getId()) return false;
        return true;
    }

    public function delete(Organization $organization){
        $organization->setVisible(false);
        $this->save($organization);
    }
}
?>
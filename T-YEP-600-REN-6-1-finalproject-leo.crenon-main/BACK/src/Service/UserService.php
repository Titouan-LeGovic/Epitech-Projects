<?php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Role\Role;

use App\Entity\User;
use App\Entity\Roles;

class UserService{

    private ValidatorInterface $validator;
    private ManagerRegistry $doctrine;
    private UserPasswordHasherInterface $userPasswordHasher;

    //public function __construct(ValidatorInterface $validator, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher)
    public function __construct(ManagerRegistry $doctrine)
    {
        //$this->validator = $validator;
        $this->doctrine = $doctrine;
        //$this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Validate User entity
     */
    public function validate(User $user){ 
        $errors = $this->validator->validate($user);
        return $errors;
    }

    /**
     * Return All Users
     */
    public function getAllUsers(){

        $users = $this->doctrine->getRepository(User::class)->findAll();
        $result = array();

        foreach ($users as $key => $user) {
            $result[$key]["id"] = $user->getId();
            $result[$key]["firstname"] = $user->getFirstName();
            $result[$key]["lastname"] = $user->getLastName();
            $result[$key]["email"] = $user->getEmail();

        } 

        return $result;
    }
    /**
     * Return an User by Id
     */
    public function getUserById(int $id_user){
        $user = $this->doctrine->getRepository(User::class)->find((int) $id_user);
        if (!$user) return false;

        return $user;
    }
    /**
     * Return an User by email
     */
    public function getUserByEmail(Request $request){
        $content = json_decode($request->getContent(), true);

        $user = $this->doctrine->getRepository(User::class)->findOneBy(["email" => $content["email"]]);
        return $user;
    }
    /**
     * Return User representation as Array
    */
    public function getUserAsArray(User $user): array{
        $roles = [];
        $role = $this->doctrine->getRepository(Roles::class)->findAll();
        for($r=0;$r<sizeof($role);$r++)
        {
            if($role[$r]->getIdUser() == $user)
            array_push($roles, [$role[$r]->toString(), "folder" => $role[$r]->getIdFolder()->toString()]);
        }

        return array(
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastName(),
            'email' => $user->getEmail(),
            'roles' => $roles,
        );
    }

    /**
     * Formats variable data of an user 
     */
    public function prepare(Request $request, User $user): User{
        $content = json_decode($request->getContent(), true);
        
        $user->setFirstName($content['firstname']);
        $user->setLastName($content['lastname']);
        $user->setEmail($content['email']);


        $password = $content['password'];
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user,$password)
        );

        return $user;
    }

    /**
     * Save an user in database
     */
    public function save(User $user){
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }
    /**
     * Delete an user in database
     */
    public function delete(User $user){
        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
    }

    /**
     * Check if current logged user can perform actions on a target user
     */
    public function canDo(User $user, User $targetUser){
        if($user->getId() === $targetUser->getId()) return true;
        return false;

    }
}

?>
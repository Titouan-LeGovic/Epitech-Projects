<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;

use App\Entity\Roles;
use App\Entity\User;
use App\Entity\Folder;
use App\Utils\CustomFireWall;
use Symfony\Component\HttpFoundation\JsonResponse;

class RolesServices
{
    private ManagerRegistry $doctrine;
    private Roles $roles;
    private User $user;
    private $erreur;

    public function __construct(ManagerRegistry $doc, User $usr)
    {
        $this->user = $usr;
        $this->doctrine = $doc;
        $this->roles = new Roles;
        $this->erreur = [];
    }

    // return errors with codes
    public function getErrors()
    {
        return $this->erreur;
    }

    // assigne Roles
    public function getRolesById(int $id_roles)
    {
        // affectation
        $this->roles = $this->doctrine->getRepository(Roles::class)->find($id_roles);
        //return $this->roles->toString();
        // verif existe
        //$this->rolesRolesExist($this->roles);

        return $this->roles;
    }

    public function getRoles()
    {
        $roles = ["Roles" => $this->roles->toString(), "User" => $this->roles->getIdUser()->toString(), "Folder" => $this->roles->getIdFolder()->toString()];
        return $roles;
    }

    // assigne User and get his Roles
    public function getUserById(int $id_user)
    {
        return $this->doctrine->getRepository(User::class)->find($id_user);
    }

    // assigne Folder and get his Roles
    public function getFolderById(int $id_folder)
    {
        return $this->doctrine->getRepository(Folder::class)->find($id_folder);
    }

    // get User roles
    public function rolesGetUserRoles(User $user)
    {
        $rolesList = ["User" => $user->toString()];
        $userRoles = $this->doctrine->getRepository(Roles::class)->findAll();
        for($role=0; $role<sizeof($userRoles); $role++)
        {
            if($userRoles[$role]->getIdUser() == $user)
            array_push($rolesList, [$userRoles[$role]->toString(), ["Folder" => $userRoles[$role]->getIdFolder()->toString()]]);
        }
        return $rolesList;
    }

    // get Folder roles
    public function rolesGetFolderRoles(Folder $folder)
    {
        $rolesList = ["Folder" => $folder->toString()];
        $folderRoles = $this->doctrine->getRepository(Roles::class)->findAll();
        for($role=0; $role<sizeof($folderRoles); $role++)
        {
            if($folderRoles[$role]->getIdFolder() == $folder)
            array_push($rolesList, [$folderRoles[$role]->toString(), ["User" => $folderRoles[$role]->getIdUser()->toString()]]);
        }
        return $rolesList;
    }

    // control entry data
    public function rolesVerifEntry(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $RolesToVerif = new Roles();
        $RolesToVerif->setValue($content["value"]);
        $RolesToVerif->setIdUser($content["id_user"]);
        $RolesToVerif->setIdFolder($content["id_folder"]);

        // verif synthaxe and existe
        $userToVerif = $this->doctrine->getRepository(User::class)->find($RolesToVerif->getIdUser());
        $folderToVerif = $this->doctrine->getRepository(Folder::class)->find($RolesToVerif->getIdFolder());
        $this->rolesValueAllowed($RolesToVerif->getValue());
        $this->rolesUserExist($userToVerif);
        $this->rolesUserHadRoles($userToVerif);
        $this->rolesFolderExist($folderToVerif);
        $this->rolesFolderHadRoles($folderToVerif);
    }

    // control entry data and verify is free
    public function rolesVerifEntryFree(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $RolesToVerif = new Roles();
        $RolesToVerif->setValue($content["value"]);
        $RolesToVerif->setIdUser($content["id_user"]);
        $RolesToVerif->setIdFolder($content["id_folder"]);

        // verif synthaxe and existe
        $userToVerif = $this->doctrine->getRepository(User::class)->find($RolesToVerif->getIdUser());
        $folderToVerif = $this->doctrine->getRepository(Folder::class)->find($RolesToVerif->getIdFolder());
        $this->rolesValueAllowed($RolesToVerif->getValue());
        $this->rolesUserExist($userToVerif);
        if($this->rolesUserHadRoles($userToVerif)) array_push($this->erreur, ["User is already affected", 400]);
        $this->rolesFolderExist($folderToVerif);
        if($this->rolesFolderHadRoles($folderToVerif)) array_push($this->erreur, ["Folder is already affected", 400]);
    }


    // verif role in bdd
    public function rolesRolesExist(Roles $roles)
    {
        if(!$roles) array_push($this->erreur, ["Roles not found", 404]);
    }

    // verif user in bdd
    public function rolesUserExist(User $user)
    {
        if(!$user) array_push($this->erreur, ["User not found", 404]);
    }

    // verif user had the role
    public function rolesUserHadRoles(User $user)
    {
        $userRoles = $user->getRoles();
        $folderRoles = new Roles();
        for($i=0; $i<sizeOf($userRoles);$i++)
        {
            if($userRoles[$i]->getIdFolder() == $this->roles->getIdFolder())
            {
                $folderRoles = $userRoles[$i];
            }
        }

        return $folderRoles;
    }

    // verif folder in bdd
    public function rolesFolderExist(Folder $folder)
    {
        if(!$folder) array_push($this->erreur, ["Folder not found", 404]);
    }

    // verif folder had the role
    public function rolesFolderHadRoles(Folder $folder)
    {
        $folderRoles = $folder->getRoles();
        $userRoles = new Roles();
        for($i=0; $i<sizeOf($folderRoles);$i++)
        {
            if($folderRoles[$i]->getIdFolder() == $this->roles->getIdFolder())
            {
                $userRoles = $folderRoles[$i];
            }
        }

        return $userRoles;
    }

    //verif value allowed
    public function rolesValueAllowed(string $value)
    {
        if(!isset($value) || strlen($value) !== 4) {array_push($this->erreur, ["synthaxe of value is not available", 400]); return false;}
        else
        {
            for($i=0; $i<4; $i++)
            {
                if($value[$i] == 0 || $value[$i] == 1) {array_push($this->erreur, ["synthaxe of value is not available", 400]); return false;}
            }
        }
        return true;
    }

    // delete roles
    public function rolesDelete(Roles $roles)
    {
        $entityManager = $this->doctrine->getManager();
        $user = $this->doctrine->getRepository(User::class)->find($this->roles->getIdUser());
        $folder = $this->doctrine->getRepository(Folder::class)->find($this->roles->getIdFolder());
        $user->removeRole($this->roles);
        $folder->removeRole($this->roles);

        $user->removeRole($this->roles);
        $folder->removeRole($this->roles);

        $entityManager->remove($this->roles);
        $entityManager->persist($user);
        $entityManager->persist($folder);
        
        $entityManager->flush();
    }

    // verif user authorisation (action == CRUD)
    public function rolesUserAuthorized(string $action, CustomFireWall $customFirewall) 
    {
        // verif action synthaxe
        if($action != "create" && $action != "read" && $action != "update" && $action != "delete") array_push($this->erreur, ["value of action is not available in (rolesUserAuthorized)", 400]);
        
        // verif user can use folder
        return $this->user;
        $userRoles = $this->user->getRoles();
        $folderRoles = $this->rolesUserHadRoles($this->user);
        if(!$folderRoles->getValue()) array_push($this->erreur, ["User not allowed", 401]);

        // verif action allowed
        switch($action)
        {
            case "create":
                $rule = $this->doctrine->getRepository(Rule::class)->find($this->rule->getId());
                if(!$rule) array_push($this->erreur, (["Rule not found", 404]));
                $folder = $rule->getIdFolder();
                $organization = $folder->getIdOrganization();
                if(!$customFirewall->preventOrganization($this->user, $organization)) array_push($this->erreur, (["Access forbidden", 403]));
                if($folderRoles[0] != 1) array_push($this->erreur, ["User not allowed to create a role", 401]);
                break;
            case "read":
                if($folderRoles[1] != 1) array_push($this->erreur, ["User not allowed to read the role", 401]);
                break;
            case "update":
                $rule = $this->doctrine->getRepository(Rule::class)->find($this->rule->getId());
                if(!$rule) array_push($this->erreur, (["Rule not found", 404]));
                $folder = $rule->getIdFolder();
                $organization = $folder->getIdOrganization();
                if(!$customFirewall->preventOrganization($this->user, $organization)) array_push($this->erreur, (["Access forbidden", 403]));
                if($folderRoles[2] != 1) array_push($this->erreur, ["User not allowed to update the role", 401]);
                break;
            case "delete":
                $rule = $this->doctrine->getRepository(Rule::class)->find($this->rule->getId());
                if(!$rule) array_push($this->erreur, (["Rule not found", 404]));
                $folder = $rule->getIdFolder();
                $organization = $folder->getIdOrganization();
                if(!$customFirewall->preventOrganization($this->user, $organization)) array_push($this->erreur, (["Access forbidden", 403]));
                if($folderRoles[3] != 1) array_push($this->erreur, ["User not allowed to delete the role", 401]);
                break;
        }
    }

    // update bdd (action == CRUD)
    public function rolesUpdateBdd(string $action, Request $request = null)
    {
        // verif action synthaxe
        if($action != "create" || $action != "read" || $action != "update" || $action != "delete") array_push($this->erreur, ["value of action is not available in (rolesUpdateBdd)", 400]);
        
        $entityManager = $this->doctrine->getManager();
        $content = json_decode($request->getContent(), true);

        switch($action)
        {
            case "create":
                if($request == null) array_push($this->erreur, ["Request is missing", 404]);
                $user = $this->doctrine->getRepository(User::class)->find($content['idUser']);
                $folder = $this->doctrine->getRepository(Folder::class)->find($content['idFolder']);
                $this->roles->setValue($content["value"]);
                $this->roles->setIdUser($user);
                $this->roles->setIdFolder($folder);
                
                $user->addRole($this->roles);
                $folder->addRole($this->roles);
        
                $entityManager->persist($this->roles);
                $entityManager->persist($user);
                $entityManager->persist($folder);
                break;
            case "read":
                break;
            case "update":
                if($request == null) array_push($this->erreur, ["Request is missing", 404]);
                $user = $this->doctrine->getRepository(User::class)->find($content['idUser']);
                $folder = $this->doctrine->getRepository(Folder::class)->find($content['idFolder']);
                $this->roles->setValue($content["value"]);
                $this->roles->setIdUser($user);
                $this->roles->setIdFolder($folder);

                $user->removeRole($this->roles);
                $folder->removeRole($this->roles);
                $user->addRole($this->roles);
                $folder->addRole($this->roles);
        
                $entityManager->persist($this->roles);
                $entityManager->persist($user);
                $entityManager->persist($folder);
                break;
            case "delete":
                $this->rolesDelete($this->roles);
                break;
        }

        $entityManager->flush();
    }
}
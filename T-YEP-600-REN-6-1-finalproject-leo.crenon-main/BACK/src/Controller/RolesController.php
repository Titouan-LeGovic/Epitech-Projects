<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;

use App\Entity\Roles;
use App\Entity\Folder;
use App\Entity\User;
use App\Utils\CustomFireWall;
use App\Service\RolesServices;

class RolesController extends AbstractController
{
    /**
     * @Route("/roles/{id_roles}", name="show_roles", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a secific roles.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="id_user",type="integer"),
     *          @OA\Property(property="id_folder",type="integer"),
     *          @OA\Property(property="value",type="string"),
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Roles not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @OA\Tag(name="roles")
     * @Security(name="Bearer")
     */
    public function show(ManagerRegistry $doctrine, int $id_roles, CustomFireWall $customFirewall): Response
    {
        $service = new RolesServices($doctrine, $this->getUser());
        $roles = $service->getRolesById($id_roles);
        $service->rolesRolesExist($roles);
        $service->rolesUserAuthorized("read", $customFirewall);
        if(sizeof($service->getErrors()) > 0) return $this->json($service->getErrors());
        else return $this->json(["Roles" => $service->getRoles()], 200);
    }
    
    /**
     * @Route("/roles/user/{id_user}", name="show_user_roles", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a secific roles.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="id_user",type="integer"),
     *          @OA\Property(property="id_folder",type="integer"),
     *          @OA\Property(property="value",type="string"),
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Roles not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @OA\Tag(name="roles")
     * @Security(name="Bearer")
     */
    public function showUserRoles(ManagerRegistry $doctrine, int $id_user, CustomFireWall $customFirewall): Response
    {
        $service = new RolesServices($doctrine, $this->getUser());
        $user = $service->getUserById($id_user);
        $service->rolesUserExist($user);
        $rolesList = $service->rolesGetUserRoles($user);
        $service->rolesUserAuthorized("read", $customFirewall);
        if(sizeof($service->getErrors()) > 0) return $this->json($service->getErrors());
        else return $this->json(["Roles" => $rolesList], 200);
    }
    
    /**
     * @Route("/roles/folder/{id_folder}", name="show_folder_roles", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a secific roles.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="id_folder",type="integer"),
     *          @OA\Property(property="value",type="string"),
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Roles not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @OA\Tag(name="roles")
     * @Security(name="Bearer")
     */
    public function showFolderRoles(ManagerRegistry $doctrine, int $id_folder, CustomFireWall $customFirewall): Response
    {
        $service = new RolesServices($doctrine, $this->getUser());
        $folder = $service->getFolderById($id_folder);
        $service->rolesFolderExist($folder);
        $rolesList = $service->rolesGetFolderRoles($folder);
        $service->rolesUserAuthorized("read", $customFirewall);
        if(sizeof($service->getErrors()) > 0) return $this->json($service->getErrors());
        else return $this->json(["Roles" => $rolesList], 200);
    }

    /**
     * @Route("/roles/{id_roles}", name="update_roles", methods={"PUT"})
     * 
     * @OA\RequestBody(
     *   description="Update roles object",
     *   required=true,
     *   @OA\JsonContent(
     *          @OA\Property(property="id_user",type="integer"),
     *          @OA\Property(property="id_folder",type="integer"),
     *          @OA\Property(property="value",type="string"),
     *   )
     * )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Update a roles",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="id_user",type="integer"),
     *          @OA\Property(property="id_folder",type="integer"),
     *          @OA\Property(property="value",type="string"),
     *     )
     * )
     * 
     * @OA\Response(
     *     response=403,
     *     description="User not allowed.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * 
     * @OA\Response(
     *     response=404,
     *     description="Roles not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="roles")
     * @Security(name="Bearer")
     */
    public function update(int $id_roles, Request $request, ManagerRegistry $doctrine, CustomFireWall $customFirewall): Response
    {
        $service = new RolesServices($doctrine, $this->getUser());
        $service->getRolesById($id_roles);
        $service->rolesVerifEntry($request);
        $service->rolesUserAuthorized("update", $customFirewall);
        if($service->getErrors() > 0) return $this->json($service->getErrors());
        else $service->rolesUpdateBdd("update", $request); 
        if($service->getErrors() > 0) return $this->json($service->getErrors());
        else return $this->json(["Roles" => "Roles has been updated"], 200);
    }
    /**
     * @Route("/roles", name="create_roles", methods={"POST"})
     * 
     * @OA\RequestBody(
     *   description="Create roles object",
     *   required=true,
     *   @OA\JsonContent(
     *          @OA\Property(property="id_user",type="integer"),
     *          @OA\Property(property="id_folder",type="integer"),
     *          @OA\Property(property="value",type="string"),
     *   )
     * )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Create a roles",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="id_user",type="integer"),
     *          @OA\Property(property="id_folder",type="integer"),
     *          @OA\Property(property="value",type="string"),
     *     )
     * )
     * 
     * @OA\Response(
     *     response=403,
     *     description="User not allowed.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * 
     * @OA\Response(
     *     response=404,
     *     description="Folder not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="roles")
     * @Security(name="Bearer")
     */
    public function create(Request $request, ManagerRegistry $doctrine, CustomFireWall $customFirewall): Response
    {
        $service = new RolesServices($doctrine, $this->user);
        $service->rolesVerifEntryFree($request);
        $service->rolesUserAuthorized("create", $customFirewall);
        if($service->getErrors() > 0) return $this->json($service->getErrors());
        else $service->rolesUpdateBdd("create", $request); 
        if($service->getErrors() > 0) return $this->json($service->getErrors());
        else return $this->json(["Roles" => "Roles has been created"], 200);
    }
    /**
     * @Route("/roles/{id_roles}", name="delete_roles", methods={"DELETE"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Delete a roles",
     *     @OA\JsonContent(
     *          @OA\Property(property="msg",type="string"),
     *     )
     * )
     * 
     * @OA\Response(
     *     response=403,
     *     description="User not allowed.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * 
     * @OA\Response(
     *     response=404,
     *     description="Roles not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="roles")
     * @Security(name="Bearer")
     */
    public function delete(int $id_roles, ManagerRegistry $doctrine, CustomFireWall $customFirewall): Response
    {
        $service = new RolesServices($doctrine, $this->getUser());
        $service->getRolesById($id_roles);
        $service->rolesUserAuthorized("delete", $customFirewall);
        if($service->getErrors() > 0) return $this->json($service->getErrors());
        else $service->rolesUpdateBdd("delete"); 
        if($service->getErrors() > 0) return $this->json($service->getErrors());
        else return $this->json(["Roles" => "Roles successfully deleted"], 200);
    }
}

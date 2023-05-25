<?php

namespace App\Controller;

use App\Entity\UserInOrganization;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

use App\Utils\CustomFireWall;
use App\Entity\Organization;
use App\Entity\User;
use App\Service\OrganizationService;
use App\Service\UserInOrganizationService;
use App\Service\UserService;

class UserInOrganizationController extends AbstractController
{
    /**
     * @Route("/organization/list/{id_organization}", name="show_organization_user", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns user list of an organization.",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="firstname",type="string"),
     *              @OA\Property(property="lastname",type="string"),
     *              @OA\Property(property="email",type="string"),
     *          )
     *      )   
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not allowed.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Organization not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="organization")
     */
    public function listFromOrganization(int $id_organization, UserService $userService, OrganizationService $organizationService, UserInOrganizationService $userInOrganizationService): Response
    {
        $organization = $organizationService->getById($id_organization);
        if (!$organization) return $this->json(["msg" => "Organization not found"], 404);

        $userInOrganizations = $userInOrganizationService->getUserList($organization);        
        $result = array();

        foreach ($userInOrganizations as $row) {
            $user = $userService->getUserById($row->getIdUser()->getId());
            array_push($result,$user->toString());
        }

        return $this->json($result);
    }
    /**
     * @Route("/user/list/{id_user}", name="show_user_organization", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns organization list of an user.",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="name",type="string"),
     *              @OA\Property(property="owner",type="object",
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="firstname",type="string"),
     *                  @OA\Property(property="lastname",type="string"),
     *                  @OA\Property(property="email",type="string"),
     *              )
     *          )
     *      )   
     * )
     * 
     * @OA\Response(
     *     response=403,
     *     description="User not allowed.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="User not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @Security(name="Bearer")
     * @OA\Tag(name="users")
     */
    // public function listFromUser(int $id_user, UserService $userService, OrganizationService $organizationService, UserInOrganizationService $userInOrganizationService): Response
    public function listFromUser(int $id_user, ManagerRegistry $doctrine, CustomFireWall $customFirewall): Response
    {
        $userService = new UserService($doctrine, $customFirewall);
        $organizationService = new OrganizationService($doctrine, $userService);
        $userInOrganizationService = new UserInOrganizationService($doctrine, $userService);

        $user = $userService->getUserById($id_user);
        //tests user & getUser()
        return $this->json($userService->getUserAsArray($user));
        //return $this->json($userService->getUserAsArray($this->getUser()));

        if (!$user) return $this->json(["msg" => "User not found",], 404);

        if(!$userService->canDo($this->getUser(), $user)) return $this->json(["msg" => "Access forbidden"], 403);

        $userInOrganizations = $userInOrganizationService->getOrganizationList($user);
        $result = array();

        foreach ($userInOrganizations as $row) {
            $organization = $organizationService->getById($row->getIdOrganization()->getId());
            array_push($result,$organization->toString());
        }

        return $this->json($result);
    }

    /**
     * @Route("/join/{id_organization}", name="join_user_organization", methods={"POST"})
     * 
     * @OA\RequestBody(
     *   description="Make an user join an Organization",
     *   required=true,
     *   @OA\JsonContent(
     *     @OA\Property(property="email",type="string"),
     *   )
     * )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Make user join an organization.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not allowed.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found or Organization not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="organization")
     */
    public function join(
        Request $request, 
        int $id_organization, 
        UserService $userService,
        OrganizationService $organizationService,
        UserInOrganizationService $userInOrganizationService,
    ){
        $user = $userService->getUserByEmail($request);
        if (!$user) return $this->json(["msg" => "User not found",], 404);

        $organization = $organizationService->getById($id_organization);
        if (!$organization) return $this->json(["msg" => "Organization not found",], 404);


        if(!$organizationService->canDo($this->getUser(), $organization)) return $this->json(["msg" => "Access forbidden"], 403);


        if($userInOrganizationService->checkAlreadyInOrganization($user, $organization)) return $this->json(["msg" => "User Already in organization"], 403);

        $userInOrganization = $userInOrganizationService->prepare($user, $organization);
        $userInOrganizationService->save($userInOrganization);

        return $this->json([
            "msg" => "user added succesfully",
        ]);
    }

    /**
     * @Route("/leave/{id_organization}/{id_user}", name="leave_user_organization", methods={"DELETE"})
     * 
     * 
     * @OA\Response(
     *     response=200,
     *     description="Make user leave an organization.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not allowed.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found or Organization not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="organization")
     */
    public function leave(
        int $id_user, 
        int $id_organization,
        UserService $userService,
        OrganizationService $organizationService,
        UserInOrganizationService $userInOrganizationService,
    ): Response
    {

        

        $user = $userService->getUserById($id_user);
        if (!$user) return $this->json(["msg" => "User not found",], 404);

        $organization = $organizationService->getById($id_organization);
        if (!$organization) return $this->json(["msg" => "Organization not found",], 404);

        //if(!$organizationService->canDo($this->getUser(), $organization)) return $this->json(["msg" => "Access forbidden"], 403);

        $userInOrganization = $userInOrganizationService->getUserInOrganization($user, $organization);
        if(!$userInOrganizationService->checkAlreadyInOrganization($user, $organization)) return $this->json(["msg" => "User Already out of organization"], 403);


        $userInOrganizationService->delete($userInOrganization);

        return $this->json([
            "msg" => "User succesfully removed from organization",
        ]);
    }
}

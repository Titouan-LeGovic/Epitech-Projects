<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\UserInOrganizationController;

use App\Controller\getContent;
use App\Controller\TokenAuthenticatedController;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use App\Utils\CustomFireWall;
use App\Entity\Organization;
use App\Entity\User;
use App\Service\OrganizationService;
use App\Service\UserInOrganizationService;
use App\Service\UserService;

class OrganizationController extends AbstractController
{
    /**
     * @Route("/organizations", name="index_organization", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of organization.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="owner",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="firstname",type="string"),
     *              @OA\Property(property="lastname",type="string"),
     *              @OA\Property(property="email",type="string"),
     *          )
     *        ),
     *     )
     * )
     * @OA\Tag(name="organization")
     */
    public function index(OrganizationService $organizationService) : Response {
        return $this->json(["Organizations" => $organizationService->getAll()], 200);
    }

    /**
     * @Route("/organization/{id_organization}", name="show_organization", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of organization.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="owner",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="firstname",type="string"),
     *              @OA\Property(property="lastname",type="string"),
     *              @OA\Property(property="email",type="string"),
     *          )
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Organization not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="organization")
     */
    public function show(ManagerRegistry $doctrine, int $id_organization, OrganizationService $organizationService): Response
    {
        
        $organization = $organizationService->getById($id_organization);
        if (!$organization) return $this->json(["msg" => "Organization not found",], 404);

        $owner = $doctrine->getRepository(User::class)->find($organization->getOwner());
        if (!$owner) return $this->json(["msg" => "Owner not found",], 404);


        return $this->json(
            ["Organization" => $organizationService->getOrganizationAsArray($organization)], 200
        );
    }

    /**
     * @Route("/organization/{id_organization}", name="update_organization", methods={"PUT"})
     * @OA\Response(
     *     response=200,
     *     description="Update an Organization.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="owner",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="firstname",type="string"),
     *              @OA\Property(property="lastname",type="string"),
     *              @OA\Property(property="email",type="string"),
     *          )
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Organization or new Owner not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not unauthorized.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\RequestBody(
     *   description="Update an Organization",
     *   required=true,
     *   @OA\JsonContent(
     *     @OA\Property(property="name",type="string"),
     *     @OA\Property(property="newOwnerId",type="integer"),
     *   )
     * )
     * @OA\Tag(name="organization")
     * @Security(name="Bearer")
     */
    public function update(OrganizationService $organizationService, int $id_organization, Request $request): Response
    {
        // id exist ?
        $organization = $organizationService->getById($id_organization);
        if (!$organization) return $this->json(["msg" => "Organization not found",], 404);

        // Récup les infos à update
        $organization = $organizationService->prepare($request, $organization, $this->getUser());
        if(!$organization) return $this->json(["msg" => "New owner not found"], 404);
        // validation
        $errors = $organizationService->validate($organization);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
            } 
            return $this->json($messages, 400);
        }

        // Can perform ?
        /*if(!$organizationService->canDo($this->getUser(), $organization)){
            return $this->json(["msg" => "Access forbidden"], 403);
        }*/
        // Save en base
        $organizationService->save($organization);
        // retours
        return $this->json($organizationService->getOrganizationAsArray($organization), 200);
    }

    /**
     * @Route("/organization", name="create_organization", methods={"POST"})
     * @OA\Response(
     *     response=200,
     *     description="Create an Organization.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="owner",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="firstname",type="string"),
     *              @OA\Property(property="lastname",type="string"),
     *              @OA\Property(property="email",type="string"),
     *          )
     *     )
     * )
     * @OA\RequestBody(
     *   description="Create an Organization",
     *   required=true,
     *   @OA\JsonContent(
     *     @OA\Property(property="name",type="string"),
     *   )
     * )
     * @OA\Tag(name="organization")
     * @Security(name="Bearer")
     */
    public function create(
        OrganizationService $organizationService, 
        Request $request, 
        UserInOrganizationService $userInOrganizationService
    ): Response {
        $organization = new Organization();
        $organization = $organizationService->prepare($request, $organization, $this->getUser());

        $errors = $organizationService->validate($organization);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
            } 
            return $this->json($messages, 400);
        }

        $organization->setVisible(true);
        $organizationService->validate($organization);
        $organizationService->save($organization);

        // Add owner into new organization
        $userInOrganization = $userInOrganizationService->prepare($this->getUser(), $organization);
        $userInOrganizationService->save($userInOrganization);

        return $this->json($organizationService->getOrganizationAsArray($organization), 200);
    }

    /**
     * @Route("/organization/{id_organization}", name="remove_organization", methods={"DELETE"})
     * @OA\Response(
     *     response=200,
     *     description="Delete an Organization.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Organization or new Owner not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not unauthorized.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="organization")
     * @Security(name="Bearer")
     */
    public function remove(int $id_organization, OrganizationService $organizationService): Response
    {
        $organization = $organizationService->getById($id_organization);

        if (!$organization) return $this->json(["msg" => "Organization not found",], 404);

        if(!$organizationService->canDo($this->getUser(), $organization)){
            return $this->json(["msg" => "Access forbidden"], 403);
        }
        
        $organizationService->delete($organization);

        return $this->json(["msg" => "success"], 200);
    }
}

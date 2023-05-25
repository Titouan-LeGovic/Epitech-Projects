<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

use App\Entity\Folder;
use App\Entity\Organization;
use App\Service\FolderServices;
use App\Utils\CustomFireWall;
use App\Entity\User;
use App\Service\DocumentServices;
use App\Service\IndicesService;
use App\Service\OrganizationService;
use App\Service\RulesServices;

class FolderController extends AbstractController
{

    /**
     * @Route("/folders/{id_organization}", name="index_folder_organization", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of folders in an organization.",
     *     @OA\JsonContent(
     *           @OA\Property(property="organization",type="object",
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="name",type="string"),
     *                  @OA\Property(property="owner",type="object",
     *                      @OA\Property(property="id",type="integer"),
     *                      @OA\Property(property="firstname",type="string"),
     *                      @OA\Property(property="lastname",type="string"),
     *                      @OA\Property(property="email",type="string"),         
     *                  ),
     *             ),
     *         @OA\Property(property="list",type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="name",type="string"),
     *              )
     *         )
     *      )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Organization not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @Security(name="Bearer")
     * @OA\Tag(name="folders")
     *
     */
    public function index(int $id_organization, FolderServices $folderServices, OrganizationService $organizationService): Response
    {
        $organization = $organizationService->getById($id_organization);
        if(!$organization) return $this->json(["msg" => "organization not found"], 404);

        $folders = $folderServices->getAllByOrganization($organization);

        return $this->json($folders);

    }
    /**
     * @Route("/folder/{id_folder}", name="show_folder", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns document object.",
     *     @OA\JsonContent(
     *         @OA\Property(property="id",type="integer"),
     *         @OA\Property(property="name",type="string"),
     *         @OA\Property(property="organization",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="name",type="string"),
     *              @OA\Property(property="owner",type="object",
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="firstname",type="string"),
     *                      @OA\Property(property="lastname",type="string"),
     *                      @OA\Property(property="email",type="string"),         
     *              ),
     *         ),
     *      )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Folder not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="folders")
     */
    public function show(int $id_folder, FolderServices $folderServices, DocumentServices $documentServices,
    IndicesService $indicesService, RulesServices $rulesServices): Response
    {
        $folder = $folderServices->getById($id_folder);
        if (!$folder) return $this->json(["msg" => "Folder not found",], 404);



        return $this->json(
            $folderServices->getInfo($folder, $documentServices, $rulesServices, $indicesService), 200
        );
    }


    /**
     * @Route("/folder/{id_folder}", name="update_older", methods={"PUT"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns document object.",
     *     @OA\JsonContent(
     *         @OA\Property(property="id",type="integer"),
     *         @OA\Property(property="name",type="string"),
     *         @OA\Property(property="organization",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="name",type="string"),
     *              @OA\Property(property="owner",type="object",
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="firstname",type="string"),
     *                      @OA\Property(property="lastname",type="string"),
     *                      @OA\Property(property="email",type="string"),         
     *              ),
     *         ),
     *      )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Folder not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="folders")
     */
    public function update(int $id_folder, Request $request, FolderServices $folderServices): Response
    {
        $folder = $folderServices->getById($id_folder);
        if(!$folder) return $this->json(["msg" => "Folder not found"], 404);

        $folder = $folderServices->prepare($request, $folder, $folder->getIdOrganization());
        $folderServices->save($folder);
        return $this->json($folderServices->getFolderAsArray($folder));
    }
    
    /**
     * @Route("/folder/{id_organization}", name="create_folder", methods={"POST"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns document object.",
     *     @OA\JsonContent(
     *         @OA\Property(property="id",type="integer"),
     *         @OA\Property(property="name",type="string"),
     *         @OA\Property(property="organization",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="name",type="string"),
     *              @OA\Property(property="owner",type="object",
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="firstname",type="string"),
     *                      @OA\Property(property="lastname",type="string"),
     *                      @OA\Property(property="email",type="string"),         
     *              ),
     *         ),
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Organization not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @Security(name="Bearer")
     * @OA\Tag(name="folders")
     */
    public function create(Request $request, int $id_organization, FolderServices $folderServices, OrganizationService $organizationService): Response
    {
        $organization = $organizationService->getById($id_organization);
        if(!$organization) return $this->json(["msg" => "Organization not found"], 404);

        $folder = new Folder();
        $folder->setVisible(true);
        $folder = $folderServices->prepare($request, $folder, $organization);
        $folderServices->save($folder);

        return $this->json($folderServices->getFolderAsArray($folder), 200);
    }


    /**
     * @Route("/folder/{id_folder}", name="remove_folder", methods={"DELETE"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Delete a folder.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @OA\Response(
     *     response=403,
     *     description="User not allowed.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Folder not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @Security(name="Bearer")
     * @OA\Tag(name="folders")
     */
    public function remove(int $id_folder, FolderServices $folderServices): Response
    {
        $folder = $folderServices->getById($id_folder);

        if (!$folder) return $this->json(["msg" => "Folder not found",], 404);

        if(!$folderServices->canDo($this->getUser(), $folder)){
            return $this->json(["msg" => "Access forbidden"], 403);
        }
        
        $folderServices->delete($folder);

        return $this->json(["msg" => "success"], 200);
    }
}

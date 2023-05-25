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

use App\Entity\Rule;
use App\Entity\Folder;
use App\Entity\UserInOrganization;
use App\Service\RulesServices;
use App\Service\FolderServices;
use App\Service\DocumentServices;
use App\Service\IndicesService;
use App\Entity\Indices;

class RuleController extends AbstractController
{

    /**
     * @Route("/rules/{id_folder}", name="index_rules", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of rules in a folder",
     *     @OA\JsonContent(
     *          @OA\Property(property="folder",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="name",type="string"),
     *              @OA\Property(property="organization",type="object",
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="name",type="string"),
     *                  @OA\Property(property="owner",type="object",
     *                      @OA\Property(property="id",type="integer"),
     *                      @OA\Property(property="firstname",type="string"),
     *                      @OA\Property(property="lastname",type="string"),
     *                      @OA\Property(property="email",type="string"),         
     *                  ),
     *             ),
     *         ),
     *          @OA\Property(property="list",type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="name",type="string"),
     *                  @OA\Property(property="type",type="string"),
     *                  @OA\Property(property="mandatory",type="boolean"),
     *              )       
     *          )
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not allowed",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Folder not found",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="rules")
     * @Security(name="Bearer")
     */
    public function index(int $id_folder, FolderServices $folderServices, RulesServices $rulesServices): Response
    {
        $folder = $folderServices->getById($id_folder);
        if(!$folder) return $this->json(["msg" => "Folder not found"], 404);
        
        $rules = $rulesServices->getAll($folder);
        return $this->json($rules);
    }

    /**
     * @Route("/rule/{id_rule}", name="show_rule", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a secific rule.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="type",type="string"),
     *          @OA\Property(property="mandatory",type="boolean"),
     *          @OA\Property(property="id_folder",type="integer"),
     *     )
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not allowed",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Rule not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @OA\Tag(name="rules")
     * @Security(name="Bearer")
     */
    public function show(int $id_rule, RulesServices $rulesServices): Response
    {
        $rule = $rulesServices->getById($id_rule);
        if(!$rule) return $this->json(["msg" => "Rule not found"], 404);

        return $this->json($rulesServices->getAsArray($rule));
    }


    /**
     * @Route("/rule/{id_rule}", name="update_rule", methods={"PUT"})
     * 
     * @OA\RequestBody(
     *   description="Update rule object",
     *   required=true,
     *   @OA\JsonContent(
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="type",type="string"),
     *          @OA\Property(property="mandatory",type="boolean"),
     *   )
     * )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Update a rule",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="type",type="string"),
     *          @OA\Property(property="mandatory",type="boolean"),
     *          @OA\Property(property="id_folder",type="integer"),
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
     *     description="Rule not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="rules")
     * @Security(name="Bearer")
     */
    public function update(int $id_rule, Request $request, RulesServices $rulesServices): Response
    {
        $rule = $rulesServices->getById($id_rule);
        if(!$rule) return $this->json(["msg" => "Rule not found"]);

        $document = $rulesServices->prepare($request, $rule, $rule->getIdFolder());

        $rulesServices->save($document);
        return $this->json($rulesServices->getAsArray($rule));
    }
    /**
     * @Route("/rule/{id_folder}", name="create_rule", methods={"POST"})
     * 
     * @OA\RequestBody(
     *   description="Create rule object",
     *   required=true,
     *   @OA\JsonContent(
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="type",type="string"),
     *          @OA\Property(property="mandatory",type="boolean"),
     *   )
     * )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Create a rule",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="type",type="string"),
     *          @OA\Property(property="mandatory",type="boolean"),
     *          @OA\Property(property="id_folder",type="integer"),
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
     * @OA\Tag(name="rules")
     * @Security(name="Bearer")
     */
    public function create(Request $request, RulesServices $rulesServices, IndicesService $indexService, FolderServices $folderServices, DocumentServices $documentServices, $id_folder): Response
    {
        $folder = $folderServices->getById($id_folder);
        if(!$folder) return $this->json(["msg" => "Folder not found"], 404);
    
        $rule = new Rule();

        $rule = $rulesServices->prepare($request, $rule, $folder);
        $rule->setVisible(true);
        $rulesServices->save($rule);

        /** Create new empty indexes foreach documents according to the new rule*/
        $documents = $documentServices->getAllDocumentObjects($folder);

        foreach ($documents as $document) {
            $index = new Indices();
            $index->setValue("");
            $index->setIdRule($rule);
            $index->setIdDocument($document);
            $index->setVisible(true);
            $indexService->save($index);
        }


        return $this->json($rulesServices->getAsArray($rule));
    }
    /**
     * @Route("/rule/{id_rule}", name="delete_rule", methods={"DELETE"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Delete a rule",
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
     *     description="Rule not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="rules")
     * @Security(name="Bearer")
     */
    public function delete(int $id_rule, RulesServices $rulesServices): Response
    {
        $rule = $rulesServices->getById($id_rule);
        if(!$rule) return $this->json(["msg" => "Rule not found"], 404);
        $rulesServices->delete($rule);
        return $this->json(["msg" => "success"]);
    }
}

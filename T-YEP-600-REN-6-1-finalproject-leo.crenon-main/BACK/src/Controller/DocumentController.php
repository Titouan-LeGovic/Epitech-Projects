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

use App\Entity\Document;
use App\Entity\Folder;
use App\Entity\Organization;
use App\Service\DocumentServices;
use App\Service\FolderServices;

class DocumentController extends AbstractController
{
    /**
     * @Route("/documents/{id_folder}", name="index_document", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns document list in a folder.",
     *     @OA\JsonContent(
     * 
     *         @OA\Property(property="folder",type="object",
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
     *         @OA\Property(property="list",type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="name",type="string"),
     *                  @OA\Property(property="type",type="string"),
     *                  @OA\Property(property="size",type="string"),
     *                  @OA\Property(property="path",type="string"),
     *                  @OA\Property(property="toIndex",type="boolean"),
     *              )
     *         )
     *      )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Folder not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @Security(name="Bearer")
     * @OA\Tag(name="documents")
     */
    public function index(int $id_folder, DocumentServices $documentServices, FolderServices $folderServices): Response
    {
        $folder = $folderServices->getById($id_folder);
        if(!$folder) return $this->json(["msg" => "Folder not found"], 404);

        $documents = $documentServices->getByFolder($folder);
        return $this->json($documents);

    }

    /**
     * @Route("/document/{id_document}", name="show_document", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns document object.",
     *     @OA\JsonContent(
     *      @OA\Property(property="id",type="integer"),
     *      @OA\Property(property="name",type="string"),
     *      @OA\Property(property="type",type="string"),
     *      @OA\Property(property="size",type="string"),
     *      @OA\Property(property="path",type="string"),
     *      @OA\Property(property="toIndex",type="boolean"),
     *      @OA\Property(property="folder",type="object",
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="organization",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="name",type="string"),
     *              @OA\Property(property="owner",type="object",
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="firstname",type="string"),
     *                  @OA\Property(property="lastname",type="string"),
     *                  @OA\Property(property="email",type="string"),    
     *              ),
     *          ),
     *      )
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Document not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @Security(name="Bearer")
     * @OA\Tag(name="documents")
     */
    public function show(int $id_document, DocumentServices $documentServices): Response
    {
        $document = $documentServices->getById($id_document);
        if(!$document) return $this->json(["msg" => "Document not found"], 404);

        return $this->json($documentServices->getDocumentAsArray($document));
    }

    /**
     * @Route("/document/{id_document}", name="update_document", methods={"PUT"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Update document object.",
     *     @OA\JsonContent(
     *      @OA\Property(property="id",type="integer"),
     *      @OA\Property(property="name",type="string"),
     *      @OA\Property(property="type",type="string"),
     *      @OA\Property(property="size",type="string"),
     *      @OA\Property(property="path",type="string"),
     *      @OA\Property(property="toIndex",type="boolean"),
     *      @OA\Property(property="folder",type="object",
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="organization",type="object",
     *              @OA\Property(property="id",type="integer"),
     *              @OA\Property(property="name",type="string"),
     *              @OA\Property(property="owner",type="object",
     *                  @OA\Property(property="id",type="integer"),
     *                  @OA\Property(property="firstname",type="string"),
     *                  @OA\Property(property="lastname",type="string"),
     *                  @OA\Property(property="email",type="string"),    
     *              ),
     *          ),
     *      )
     *     )
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
     *     description="Document not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @Security(name="Bearer")
     * @OA\Tag(name="documents")
     */
    public function update(int $id_document, Request $request, DocumentServices $documentServices): Response
    {
        $document = $documentServices->getById($id_document);
        if(!$document) return $this->json(["msg" => "Document not found"]);

        $document = $documentServices->prepare($request, $document);

        $documentServices->save($document);
        return $this->json($documentServices->getDocumentAsArray($document));
    }
    
    /**
     * @Route("/document/{id_document}", name="delete_document", methods={"DELETE"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Delete a document.",
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
     *     description="Document not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * 
     * @Security(name="Bearer")
     * @OA\Tag(name="documents")
     */
    public function delete(int $id_document, DocumentServices $documentServices): Response
    {
        $document = $documentServices->getById($id_document);
        if(!$document) return $this->json(["msg" => "Document not found"]);
        $documentServices->delete($document);
        return $this->json(["msg" => "success"]);
    }
}

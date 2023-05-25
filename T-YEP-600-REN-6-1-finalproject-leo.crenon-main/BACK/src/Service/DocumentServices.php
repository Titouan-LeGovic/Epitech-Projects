<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;

use App\Entity\Document;
use App\Entity\User;
use App\Entity\Folder;
use App\Entity\Roles;
use App\Entity\Indices;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocumentServices
{
    private ManagerRegistry $doctrine;
    private FolderServices $folderServices;
    public function __construct(ManagerRegistry $doctrine, FolderServices $folderServices)
    {
        $this->doctrine = $doctrine;
        $this->folderServices = $folderServices;
    }

    public function getByFolder(Folder $folder){
        $documents = $this->doctrine->getRepository(Document::class)->findBy(["idFolder" => $folder->getId(), "visible" => true]);

        $result = array();

        foreach ($documents as $key => $document) {
            $result[$key]["id"] = $document->getId();
            $result[$key]["name"] = $document->getName();
            $result[$key]["type"] = $document->getType();
            $result[$key]["path"] = $document->getType();
            $result[$key]["size"] = $document->getSize();
            $result[$key]["toIndex"] = $document->isToIndex();
        }

        return array(
            "list" => $result,
            "folder" => $folder->toString(),
        );
    }

    public function getByFolderWithoutInfo(Folder $folder, IndicesService $indicesService){
        $documents = $this->doctrine->getRepository(Document::class)->findBy(["idFolder" => $folder->getId(), "visible" => true]);

        $result = array();

        foreach ($documents as $key => $document) {
            $result[$key]["id"] = $document->getId();
            $result[$key]["name"] = $document->getName();
            $result[$key]["type"] = $document->getType();
            $result[$key]["path"] = $document->getType();
            $result[$key]["size"] = $document->getSize();
            $result[$key]["toIndex"] = $document->isToIndex();

            $array_of_index = array();
            $indexes = $indicesService->getByDocumentId($document->getId());
            foreach ($indexes as  $key2 => $index){
                $array_of_index[$key2] = $indicesService->indiceToArray($index);
            }
            $result[$key]["indexes"] = $array_of_index;
        }

        return $result;
    }

    public function getDocumentAsArray(Document $document){
        return array(
            'id' => $document->getId(),
            'name' => $document->getName(),
            'type' => $document->getType(),
            'path' => $document->getPath(),
            'size' => $document->getSize(),
            'toIndex' => $document->isToIndex(),
            'folder' => $this->folderServices->getFolderAsArray($document->getIdFolder()),
        );
    }

    public function getById(int $id_document){
        $document = $this->doctrine->getRepository(Document::class)->find((int) $id_document);
        if (!$document) return false;

        return $document;
    }

    public function getAllDocumentObjects(Folder $folder){
        $documents = $this->doctrine->getRepository(Document::class)->findBy(["idFolder" => $folder->getId(), "visible" => true]);
        return $documents;
    }

    public function prepare(Request $request, Document $document): Document{
        $content = json_decode($request->getContent(), true);
        $document->setName($content['name']);
        return $document;
    }

    public function save(Document $document){
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($document);
        $entityManager->flush();
    }

    public function delete(Document $document){
        $document->setVisible(false);
        $this->save($document);
    }
}

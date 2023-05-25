<?php

namespace App\Service;

use App\Entity\Document;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Folder;
use App\Entity\Indices;
use App\Entity\Roles;
use App\Entity\Organization;
use App\Entity\User;
use App\Entity\Rule;

use App\Service\DocumentService;
use App\Service\RolesService;
use App\Service\RulesService;
use App\Utils\CustomFireWall;


class FolderServices
{

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    public function getAllByOrganization(Organization $organization) {
        $folders = $this->doctrine->getRepository(Folder::class)->findBy(["idOrganization" => $organization->getId(), "visible" => true]);
        $result = array();

        foreach ($folders as $key => $folder) {
            $result[$key]["id"] = $folder->getId();
            $result[$key]["name"] = $folder->getName();
            $result[$key]["icon"] = $folder->getIcon();
        }

        return array(
            "list" => $result,
            "organization" => $organization->toString(),
        );
    }

    public function getById(int $id_folder){
        $folder = $this->doctrine->getRepository(Folder::class)->find($id_folder);
        if (!$folder) return false;

        return $folder;
    }

    public function getFolderAsArray(Folder $folder){
        return array(
            'id' => $folder->getId(),
            'name' => $folder->getName(),
            'icon' => $folder->getIcon(),
            'organization' => $folder->getIdOrganization()->toString(),
        );
    }

    public function getFolderAsArrayWithoutInfo(Folder $folder){
        return array(
            'id' => $folder->getId(),
            'name' => $folder->getName(),
            'icon' => $folder->getIcon(),
        );
    }

    public function prepare(Request $request, Folder $folder, Organization $organization){
        $content = json_decode($request->getContent(), true);
        $folder->setName($content['name']);
        $folder->setIdOrganization($organization);
        $folder->setIcon($content["icon"]);
        return $folder;
    }

    public function canDo(User $user, Folder $targetFolder){
        $organization = $targetFolder->getIdOrganization();
        if($organization->getOwner()->getId() !== $user->getId()) return false;
        return true;
    }


    public function save(Folder $folder){
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($folder);
        $entityManager->flush();
    }

    public function delete(Folder $folder){
        $folder->setVisible(false);
        $this->save($folder);
    }

    public function getInfo(Folder $folder, DocumentServices $documentServices,
    RulesServices $rulesServices, IndicesService $indicesService){
        $result = array(
            "folder" => $this->getFolderAsArrayWithoutInfo($folder),
            "documents" => $documentServices->getByFolderWithoutInfo($folder, $indicesService),
            "rules" => $rulesServices->getAllWithoutInfo($folder)
        );
        return $result;

    }
}
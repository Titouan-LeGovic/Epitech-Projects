<?php // RoleService does already existe

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;

use App\Entity\Rule;
use App\Entity\Roles;
use App\Entity\User;
use App\Entity\Folder;
use App\Entity\Document;
use App\Entity\Indices;
use App\Utils\CustomFireWall;
use Symfony\Component\HttpFoundation\JsonResponse;

class RulesServices
{
    private ManagerRegistry $doctrine;
    private FolderServices $folderServices;

    public function __construct(ManagerRegistry $doctrine, FolderServices $folderServices)
    {
        $this->doctrine = $doctrine;
        $this->folderServices = $folderServices;
    }

    public function getById(int $id_rule) {
        $rule = $this->doctrine->getRepository(Rule::class)->find((int) $id_rule);
        if (!$rule) return false;

        return $rule;
    }

    public function getAll(Folder $folder) {
        $rules = $this->doctrine->getRepository(Rule::class)->findBy(["idFolder" => $folder->getId(), "visible" => true]);

        $result = array();

        foreach ($rules as $key => $rule) {
            $result[$key]["id"] = $rule->getId();
            $result[$key]["name"] = $rule->getName();
            $result[$key]["type"] = $rule->getType();
            $result[$key]["mandatory"] = $rule->isMandatory();
        }

        return array(
            "list" => $result,
            "folder" => $folder->toString(),
        );
    }

    public function getAllObjects(Folder $folder) {
        $rules = $this->doctrine->getRepository(Rule::class)->findBy(["idFolder" => $folder->getId(), "visible" => true]);
        return $rules;
    }

    public function getAllWithoutInfo(Folder $folder){
        $rules = $this->doctrine->getRepository(Rule::class)->findBy(["idFolder" => $folder->getId(), "visible" => true]);

        $result = array();

        foreach ($rules as $key => $rule) {
            $result[$key]["id"] = $rule->getId();
            $result[$key]["name"] = $rule->getName();
            $result[$key]["type"] = $rule->getType();
            $result[$key]["mandatory"] = $rule->isMandatory();
        }

        return $result;
    }

    public function getAsArray(Rule $rule) {
        $folder = $this->folderServices->getFolderAsArray($rule->getIdFolder());
        return array(
            'id' => $rule->getId(),
            'name' => $rule->getName(),
            'type' => $rule->getType(),
            'mandatory' => $rule->isMandatory(),
            'folder' => $folder ,
        );
    }

    public function prepare(Request $request, Rule $rule, Folder $folder): Rule{
        $content = json_decode($request->getContent(), true);
        $rule->setName($content['name']);
        $rule->setType($content['type']);
        $rule->setMandatory($content['mandatory']);
        $rule->setIdFolder($folder);
        return $rule;
    }

    public function save(Rule $rule){
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($rule);
        $entityManager->flush();
    }

    public function delete(Rule $rule){
        $rule->setVisible(false);
        $this->save($rule);
    }

    
}

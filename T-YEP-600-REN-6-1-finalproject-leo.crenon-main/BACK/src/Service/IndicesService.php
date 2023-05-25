<?php

namespace App\Service;

use App\Entity\Indices;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class IndicesService{

    private ValidatorInterface $validator;
    private ManagerRegistry $doctrine;

    public function __construct(ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $this->validator = $validator;
        $this->doctrine = $doctrine;
    }  
    
    public function getById($id_index){
        return $this->doctrine->getRepository(Indices::class)->find($id_index);
    }

    public function getByDocumentId($id_document){
        return $this->doctrine->getRepository(Indices::class)->findBy(["idDocument" => $id_document, "visible" => true]);
    }

    public function indiceToArray(Indices $index){
        return array(
            "id" => $index->getId(),
            "value" => $index->getValue(),
            "rule_id" => $index->getIdRule()->getId(),
        );

    }

    public function prepare(Request $request, Indices $index){
        $content = json_decode($request->getContent(), true);
        $index->setValue($content["value"]);
        return $index;
    }

    public function save(Indices $index){
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($index);
        $entityManager->flush();
    }

    public function delete(Indices $index){
        $index->setVisible(false);
        $this->save($index);
    }
}
?>
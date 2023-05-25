<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use App\Service\UserService;
use App\Utils\CustomFireWall;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\User;
use App\Entity\UserInOrganization;

class UserController extends AbstractController
{

    /**
     * @Route("/users", name="index_users", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of users.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="firstname",type="string"),
     *          @OA\Property(property="lastname",type="string"),
     *          @OA\Property(property="email",type="string"),
     *        )
     *     )
     * )
     * @OA\Tag(name="users")
     */
    public function index(ManagerRegistry $doctrine, UserService $userService) : JsonResponse {
        
        $result = $userService->getAllUsers($doctrine);
       
                
        return $this->json(["Users" => $result]);
    }

    /**
     * @Route("/user/{id_user}", name="show_user", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns specific user info.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="firstname",type="string"),
     *          @OA\Property(property="lastname",type="string"),
     *          @OA\Property(property="email",type="string")))
     * @OA\Response(
     *     response=404,
     *     description="User not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="users")
     */
    public function show(UserService $userService, int $id_user): Response
    {
        $user = $userService->getUserById($id_user);
        if (!$user) return $this->json(["msg" => "User not found",], 404);

        return $this->json(["User" => $userService->getUserAsArray($user)], 200);
    }

    /**
     * @Route("/user/{id_user}", name="update_user", methods={"PUT"})
     *   
     * @OA\Response(
     *     response=200,
     *     description="Update specific user info.",
     *     @OA\JsonContent(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="firstname",type="string"),
     *          @OA\Property(property="lastname",type="string"),
     *          @OA\Property(property="email",type="string")))
     * @OA\Response(
     *     response=404,
     *     description="User not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not unauthorized.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\RequestBody(
     *   description="Update user object",
     *   required=true,
     *   @OA\JsonContent(
     *     @OA\Property(property="firstname",type="string"),
     *     @OA\Property(property="lastname",type="string"),
     *     @OA\Property(property="email",type="string"),
     *     @OA\Property(property="password",type="string"),
     *   )
     * )
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function update(int $id_user, UserService $userService, Request $request): Response
    {
        $user = $userService->getUserById($id_user); 
        if (!$user) return $this->json(["msg" => "User not found",], 404);

        $user = $userService->prepare($request, $user);

        $errors = $userService->validate($user);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
            } 
            return $this->json($messages, 400);
        }
        if(!$userService->canDo($this->getUser(), $user)) return $this->json(["msg" => "Access forbidden",], 403);

        $userService->save($user);

        return $this->json($userService->getUserAsArray($user));

    }

    /**
     * @Route("/register", name="add_user", methods={"POST"})
     * @OA\Response(
     *     response=200,
     *     description="Register an user in database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(
     *          @OA\Property(property="id",type="integer"),
     *          @OA\Property(property="firstname",type="string"),
     *          @OA\Property(property="lastname",type="string"),
     *          @OA\Property(property="email",type="string"),
     *        )
     *     )
     * )
     * @OA\RequestBody(
     *   description="Create user object",
     *   required=true,
     *   @OA\JsonContent(
     *     @OA\Property(property="firstname",type="string"),
     *     @OA\Property(property="lastname",type="string"),
     *     @OA\Property(property="email",type="string"),
     *     @OA\Property(property="password",type="string"),
     *   )
     * )
     * @OA\Tag(name="users")
     */
    public function create(UserService $userService, Request $request): Response
    {
        $user = $userService->getUserByEmail($request);
        if($user) return $this->json(["msg" => "This email already exists"], 403);

        $user = new User();
        $user = $userService->prepare($request, $user);
        
        $errors = $userService->validate($user);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
            } 
            return $this->json($messages, 400);
        }

        $userService->save($user);

        return $this->json($userService->getUserAsArray($user), 200);
    }
     
    /**
     * @Route("/user/{id_user}", name="dremove_user", methods={"DELETE"})
     * @OA\Response(
     *     response=200,
     *     description="Remove an user from database",
     *     @OA\JsonContent(
     *          @OA\Property(property="msg",type="string"),
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Response(
     *     response=403,
     *     description="User not unauthorized.",
     *     @OA\JsonContent(@OA\Property(property="msg",type="string"))
     * )
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function remove(int $id_user, UserService $userService): Response
    {
        $user = $userService->getUserById($id_user);
        if(!$userService->canDo($this->getUser(), $user)) return $this->json(["msg" => "Access forbidden"], 403);

        $userService->delete($user);

        return $this->json(["msg" => "User successfully deleted"], 200);
    }
}

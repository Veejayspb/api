<?php

namespace veejay\api\example\controller;

use OpenApi\Attributes as OA;
use veejay\api\component\Code;
use veejay\api\component\Controller;
use veejay\api\component\Exception;
use veejay\api\example\App;
use veejay\api\example\model\User;

#[OA\Tag(name: 'user', description: 'Операции с пользователями')]
class UserController extends Controller
{
    const TOKEN = 'example-token';

    protected function __access(string $action, array $arguments): bool
    {
        $token = App::instance()->request->getBearerToken();

        return match ($action) {
            'index',
            'view',
            'create',
            'update' => true,
            'delete' => $token == static::TOKEN,
            default => parent::__access($action, $arguments),
        };
    }

    #[OA\Get(path: '/user', description: 'Описание метода со списком пользователей.', summary: 'Список пользователей', tags: ['user'])]
    #[OA\Response(response: '200', description: 'Список пользователей', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/User')))]
    protected function index()
    {
        return User::getModels();
    }

    #[OA\Get(path: '/user/{id}', description: 'Описание метода с просмотром пользователя.', summary: 'Просмотр пользователя', tags: ['user'])]
    #[OA\Parameter(name: 'id', description: 'ID пользователя', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'))]
    #[OA\Response(response: '200', description: 'Данные пользователя', content: new OA\JsonContent(ref: '#/components/schemas/User'))]
    #[OA\Response(response: '404', description: 'Пользователь не найден')]
    protected function view()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->findModel($id);
        return (array)$user;
    }

    #[OA\Post(path: '/user', description: 'Описание метода с добавлением пользователя.', summary: 'Добавление пользователя', tags: ['user'])]
    #[OA\RequestBody(ref: '#/components/requestBodies/User')]
    #[OA\Response(response: '201', description: 'Добавлено', content: new OA\JsonContent(ref: '#/components/schemas/User'))]
    #[OA\Response(response: '400', description: 'Ошибка при добавлении')]
    protected function create()
    {
        $attributes = App::instance()->request->getHeaderPayload();
        $user = new User;
        $user->setAttributes($attributes);

        if (!$user->save()) {
            throw new Exception('Operation error', Code::BAD_REQUEST);
        }

        App::instance()->response->code = Code::CREATED;
        return get_object_vars($user);
    }

    #[OA\Patch(path: '/user/{id}', description: 'Описание метода с изменением пользователя.', summary: 'Изменение пользователя', tags: ['user'])]
    #[OA\Parameter(name: 'id', description: 'ID пользователя', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'))]
    #[OA\RequestBody(ref: '#/components/requestBodies/User')]
    #[OA\Response(response: '200', description: 'Изменено', content: new OA\JsonContent(ref: '#/components/schemas/User'))]
    #[OA\Response(response: '400', description: 'Ошибка при изменении')]
    #[OA\Response(response: '404', description: 'Пользователь не найден')]
    protected function update()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->findModel($id);

        $attributes = App::instance()->request->getHeaderPayload();
        $user->setAttributes($attributes);

        if (!$user->save()) {
            throw new Exception('Operation error', Code::BAD_REQUEST);
        }

        return get_object_vars($user);
    }

    #[OA\Delete(path: '/user/{id}', description: 'Описание метода с удалением пользователя.', summary: 'Удаление пользователя', tags: ['user'])]
    #[OA\Parameter(name: 'id', description: 'ID пользователя', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'int64'))]
    #[OA\Response(response: '204', description: 'Удалено')]
    #[OA\Response(response: '400', description: 'Ошибка при удалении')]
    #[OA\Response(response: '404', description: 'Пользователь не найден')]
    protected function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->findModel($id);

        if (!$user->delete()) {
            throw new Exception('Operation error', Code::BAD_REQUEST);
        }

        return null;
    }

    /**
     * @param int $id
     * @return User
     * @throws Exception
     */
    protected function findModel(int $id): User
    {
        $user = User::getModel($id);

        if (!$user) {
            throw new Exception('User not found', Code::NOT_FOUND);
        }

        return $user;
    }
}

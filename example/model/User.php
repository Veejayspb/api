<?php

namespace veejay\api\example\model;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'Пользователь', description: 'Данные пользователя', required: ['id'])]
#[OA\RequestBody(description: 'Добавление нового пользователя', content: new OA\JsonContent(ref: '#components/schemas/User'))]
class User
{
    const ITEMS = [
        [
            'id' => 1,
            'name' => 'Нортон',
            'surname' => 'Нимнул',
        ],
        [
            'id' => 2,
            'name' => 'Дональд',
            'surname' => 'Дрейк',
        ],
        [
            'id' => 3,
            'name' => 'Алдрин',
            'surname' => 'Клордейн',
        ],
        [
            'id' => 4,
            'name' => 'Рокки',
            'surname' => 'Рокфор',
        ],
    ];

    #[OA\Property(title: 'ID', description: 'Идентификатор', format: 'int64', example: 1)]
    public $id;

    #[OA\Property(title: 'Имя', description: 'Имя пользователя', format: 'string', example: 'Рокки')]
    public $name;

    #[OA\Property(title: 'Фамилия', description: 'Фамилия пользователя', format: 'string', example: 'Рокфор')]
    public $surname;

    /**
     * @param array $attributes
     * @return void
     */
    public function setAttributes(array $attributes): void
    {
        foreach ($attributes as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        // Здесь должен быть процесс сохранения
        return true;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        // Здесь должен быть процесс удаления
        return true;
    }

    /**
     * @return static[]
     */
    public static function getModels(): array
    {
        $models = [];

        foreach (static::ITEMS as $item) {
            $model = new static;
            $model->setAttributes($item);
            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param int $id
     * @return static|null
     */
    public static function getModel(int $id): ?static
    {
        $models = static::getModels();

        foreach ($models as $model) {
            if ($model->id == $id) {
                return $model;
            }
        }

        return null;
    }
}

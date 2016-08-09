<?php

namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;

class BulkUpdateRoomsValidator extends AbstractSymfonyValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data)
    {
        $validators = [
            'room_type' => [new Assert\NotNull(), new Assert\Choice(['choices' => ['single_room', 'double_room']])],
            'date_from' => [new Assert\NotNull(), new Assert\Date()],
            'date_to' => [new Assert\NotNull(), new Assert\Date()],
            'day_refine' => [new Assert\Type(['type' => 'integer'])],
            'price' => [new Assert\Type(['type' => 'numeric'])],
            'availability' => [new Assert\Type(['type' => 'integer'])]
        ];

        return parent::validate($data, $validators);
    }
}

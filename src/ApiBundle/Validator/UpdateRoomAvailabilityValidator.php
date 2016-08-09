<?php

namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateRoomAvailabilityValidator extends AbstractSymfonyValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data)
    {
        $validators = [
            'room_type' => [new Assert\NotNull(), new Assert\Choice(['choices' => ['single_room', 'double_room']])],
            'date' => [new Assert\NotNull(), new Assert\Date()],
            'availability' => [new Assert\NotNull(), new Assert\Type(['type' => 'integer'])]
        ];

        return parent::validate($data, $validators);
    }
}
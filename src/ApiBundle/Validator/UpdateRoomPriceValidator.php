<?php

namespace ApiBundle\Validator;

use ApiBundle\Repository\RoomTypeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

class UpdateRoomPriceValidator extends AbstractSymfonyValidator
{
    /** @var RoomTypeRepository */
    private $roomTypeRepo;

    /**
     * UpdateRoomPriceValidator constructor.
     * @param SymfonyValidatorInterface $validator
     * @param RoomTypeRepository $roomTypeRepo
     */
    public function __construct(SymfonyValidatorInterface $validator, RoomTypeRepository $roomTypeRepo)
    {
        $this->roomTypeRepo = $roomTypeRepo;
        parent::__construct($validator);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data)
    {
        $roomTypes = [];
        foreach ($this->roomTypeRepo->findAll() as $roomType) {
            $roomTypes[] = $roomType->getType();
        }

        $validators = [
            'room_type' => [new Assert\NotNull(), new Assert\Choice(['choices' => $roomTypes])],
            'date' => [new Assert\NotNull(), new Assert\Date()],
            'price' => [new Assert\NotNull(), new Assert\Type(['type' => 'numeric'])]
        ];

        return parent::validate($data, $validators);
    }
}

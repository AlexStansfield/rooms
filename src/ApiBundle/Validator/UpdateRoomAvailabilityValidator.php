<?php

namespace ApiBundle\Validator;

use ApiBundle\Entity\RoomType;
use ApiBundle\Repository\RoomTypeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

class UpdateRoomAvailabilityValidator extends AbstractSymfonyValidator
{
    /** @var RoomTypeRepository */
    private $roomTypeRepo;

    /**
     * UpdateRoomAvailabilityValidator constructor.
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
        /** @var RoomType[] $roomTypes */
        $roomTypes = [];
        foreach ($this->roomTypeRepo->findAll() as $roomType) {
            $roomTypes[$roomType->getType()] = $roomType;
        }

        $validators = [
            'room_type' => [new Assert\NotNull(), new Assert\Choice(['choices' => array_keys($roomTypes)])],
            'date' => [new Assert\NotNull(), new Assert\Date()],
            'availability' => [new Assert\NotNull(), new Assert\Type(['type' => 'integer'])]
        ];

        $isValid = parent::validate($data, $validators);

        if (!$isValid) {
            return false;
        }

        // Check Availability not great than default
        $defaultAvailability = $roomTypes[$data['room_type']]->getDefaultAvailability();
        if ((null !== $data['availability']) && ($defaultAvailability < $data['availability'])) {
            $this->errors['availability'] = 'Availability cannot be greater than ' . $defaultAvailability;
            $isValid = false;
        }

        return $isValid;
    }
}

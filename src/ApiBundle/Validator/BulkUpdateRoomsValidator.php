<?php

namespace ApiBundle\Validator;

use ApiBundle\Helper\DateHelper;
use ApiBundle\Repository\RoomTypeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

class BulkUpdateRoomsValidator extends AbstractSymfonyValidator
{
    /** @var DateHelper */
    private $dateHelper;

    /** @var RoomTypeRepository */
    private $roomTypeRepo;

    /**
     * BulkUpdateRoomsValidator constructor.
     * @param SymfonyValidatorInterface $validator
     * @param RoomTypeRepository $roomTypeRepo
     * @param DateHelper $dateHelper
     */
    public function __construct(
        SymfonyValidatorInterface $validator,
        RoomTypeRepository $roomTypeRepo,
        DateHelper $dateHelper
    ) {
        $this->roomTypeRepo = $roomTypeRepo;
        $this->dateHelper = $dateHelper;
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
            'date_from' => [new Assert\NotNull(), new Assert\Date()],
            'date_to' => [new Assert\NotNull(), new Assert\Date()],
            'day_refine' => [new Assert\Type(['type' => 'numeric'])],
            'price' => [new Assert\Type(['type' => 'numeric'])],
            'availability' => [new Assert\Type(['type' => 'numeric'])]
        ];

        $isValid = parent::validate($data, $validators);

        if (!$isValid) {
            return false;
        }

        // Check the dates are not in the past
        $today = $this->dateHelper->createDate((new \DateTime())->format('Y-m-d'));
        $dateFrom = $this->dateHelper->createDate($data['date_from']);
        if ($dateFrom < $today) {
            $this->errors['date_from'] = 'Date From cannot be in the past';
            $isValid = false;
        }

        $dateTo = $this->dateHelper->createDate($data['date_to']);
        if ($dateTo < $today) {
            $this->errors['date_to'] = 'Date To cannot be in the past';
            $isValid = false;
        }

        if ($dateTo < $dateFrom) {
            $this->errors['date_to'] = 'Date To cannot be before Date From';
            $isValid = false;
        }

        return $isValid;
    }
}

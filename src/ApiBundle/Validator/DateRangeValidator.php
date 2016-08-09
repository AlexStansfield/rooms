<?php

namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;

class DateRangeValidator extends AbstractSymfonyValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data)
    {
        $validators = [
            'date_from' => [new Assert\Date()],
            'date_to' => [new Assert\Date()]
        ];

        return parent::validate($data, $validators);
    }
}

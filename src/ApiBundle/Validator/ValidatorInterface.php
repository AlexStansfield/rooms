<?php

namespace ApiBundle\Validator;

/**
 * Interface ValidatorInterface
 * @package ApiBundle\Validator
 */
interface ValidatorInterface
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data);

    /**
     * @return array
     */
    public function getErrors();
}

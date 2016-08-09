<?php


namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

abstract class AbstractSymfonyValidator implements ValidatorInterface
{
    /**
     * @var SymfonyValidatorInterface
     */
    private $validator;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * AbstractSymfonyValidator constructor.
     * @param SymfonyValidatorInterface $validator
     */
    public function __construct(SymfonyValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     * @param array $validators
     * @return bool
     */
    protected function validate(array $data, array $validators)
    {
        $this->errors = [];

        foreach ($validators as $field => $constraints) {
            $errors = [];

            foreach ($constraints as $constraint) {
                // use the validator to validate the value
                $errorList = $this->validator->validate(
                    isset($data[$field]) ? $data[$field] : null,
                    $constraint
                );

                if (0 !== count($errorList)) {
                    foreach ($errorList as $error) {
                        $errors = $error->getMessage();
                    }
                }
            }

            if (0 !== count($errors)) {
                $this->errors[$field] = $errors;
            }
        }

        return (0 === count($this->errors));
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

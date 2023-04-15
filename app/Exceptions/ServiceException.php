<?php

namespace App\Exceptions;

use function __;

abstract class ServiceException extends \Exception
{
    protected array|null $messages;
    protected array|null $exception = null;
    protected string $exception_code = '';

    public abstract function configureExceptions();

    /**
     * ServiceException constructor.
     * @param $exception_const
     * @param array $translateParameter
     */
    public function __construct($exception_const, $translateParameter = [])
    {
        $this->configureExceptions();
        $serviceValidationCode = 'service_validation_error';
        $this->exception = $this->messages[$exception_const] ?? $exception_const;
        $this->exception_code = ($this->exception === $exception_const) ? $serviceValidationCode : $exception_const;
        $language_name = 'exceptions/' . fromCamelCase(class_basename(get_called_class()));
        if (isset(__($language_name)[$exception_const])) {
            $this->message = __($language_name . '.' . $exception_const, $translateParameter);
        } else {
            $this->message = ($this->exception_code === $serviceValidationCode) ? $exception_const : $this->exception[1];
        }
        $this->code = ($this->exception_code === $serviceValidationCode) ? 421 : $this->exception[0];
    }

    /**
     * @return mixed|string
     */
    public function getAppCode()
    {
        return $this->exception_code;
    }

    /**
     * @param string $exceptionConst
     * @param int $responseCode
     * @param string $responseMessage
     * @return void
     */
    public function addException(string $exceptionConst,int $responseCode,string $responseMessage)
    {
        $this->messages[$exceptionConst] = [$responseCode, $responseMessage];
    }
}


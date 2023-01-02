<?php

namespace MediaMonks\RestApi\Exception;

use JetBrains\PhpStorm\ArrayShape;
use MediaMonks\RestApi\Response\Error;
use MediaMonks\RestApi\Util\StringUtil;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class FormValidationException extends AbstractValidationException
{
    const FIELD_ROOT = '#';

    public function __construct(
        private FormInterface $form,
        protected $message = Error::MESSAGE_FORM_VALIDATION,
        protected $code = Error::CODE_FORM_VALIDATION
    ) {
        parent::__construct($message, $code);
    }

    public function getFields(): array
    {
        return $this->getErrorMessages($this->form);
    }

    protected function getErrorMessages(FormInterface $form): array
    {
        $errors = [];
        foreach ($this->getFormErrorMessages($form) as $error) {
            $errors[] = $error;
        }
        foreach ($this->getFormChildErrorMessages($form) as $error) {
            $errors[] = $error;
        }

        return $errors;
    }

    protected function getFormErrorMessages(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors[] = $this->toErrorArray($error);
            } else {
                $errors[] = $this->toErrorArray($error, $form);
            }
        }

        return $errors;
    }

    protected function getFormChildErrorMessages(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->all() as $child) {
            if ($this->shouldAddChildErrorMessage($child)) {
                foreach ($this->getErrorMessages($child) as $error) {
                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    protected function shouldAddChildErrorMessage(FormInterface $child = null): bool
    {
        return !empty($child) && !$child->isValid();
    }

    protected function toErrorArray(FormError $error, FormInterface $form = null): array
    {
        if (is_null($form)) {
            $field = self::FIELD_ROOT;
        } else {
            $field = $form->getName();
        }
        if (!is_null($error->getCause()) && !is_null($error->getCause()->getConstraint())) {
            $code = $this->getErrorCode(StringUtil::classToSnakeCase($error->getCause()->getConstraint()));
        } else {
            $code = $this->getErrorCodeByMessage($error);
        }

        return (new ErrorField($field, $code, $error->getMessage()))->toArray();
    }

    protected function getErrorCodeByMessage(FormError $error): string
    {
        if (stristr($error->getMessage(), Error::FORM_TYPE_CSRF)) {
            return $this->getErrorCode(Error::FORM_TYPE_CSRF);
        }

        return $this->getErrorCode(Error::FORM_TYPE_GENERAL);
    }

    protected function getErrorCode(string $value): string
    {
        return sprintf(Error::ERROR_KEY_FORM_VALIDATION.'.%s', $value);
    }
}

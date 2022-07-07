<?php

namespace MediaMonks\RestApi\Response;

class Error
{
    const CODE_GENERAL = 500;
    const CODE_FORM_VALIDATION = 400;

    const FORM_TYPE_GENERAL = 'general';
    const FORM_TYPE_CSRF = 'csrf';

    const FORM_VALIDATION_KEY = 'validation';
    const GENERAL_ERROR_KEY = 'error.%s';
    const MESSAGE_FORM_VALIDATION = 'Not all fields are filled in correctly.';
}

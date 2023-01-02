<?php

namespace MediaMonks\RestApi\Response;

class Error
{
    const CODE_GENERAL = 500;
    const CODE_FORM_VALIDATION = 400;

    const FORM_TYPE_GENERAL = 'general';
    const FORM_TYPE_CSRF = 'csrf';

    const ERROR_KEY_GENERAL = 'error.%s';
    const ERROR_KEY_HTTP = 'error.http.%s';
    const ERROR_KEY_FORM_VALIDATION = 'error.form.validation';
    const ERROR_KEY_SERIALIZE = 'error.serialize';
    const ERROR_KEY_REST_API_BUNDLE = 'error.rest_api_bundle';
    const MESSAGE_FORM_VALIDATION = 'Not all fields are filled in correctly.';
}

Step 1: Setting up the library in Drupal 8
==========================================

A) Download the library
-----------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    $ composer require mediamonks/rest-api ~1.0

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

B) Setup the library
--------------------

A custom module will be created soon but untill then this will implement the library in your Drupal 8 project:

Create a module "mediamonks_rest_api" with these files:

.. code-block:: yml

    # mediamonks_rest_api.info.yml
    name: MediaMonks Rest API
    description: Transforms any controller result into a Rest API response
    package: Custom
    type: module
    core: 8.x


.. code-block:: yml

    # mediamonks_rest_api.services.yml
    parameters:
        mediamonks_rest_api.rest_api_event_subscriber.class: MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriber
        mediamonks_rest_api.path_request_matcher.class: MediaMonks\RestApi\Request\PathRequestMatcher
        mediamonks_rest_api.regex_request_matcher.class: MediaMonks\RestApi\Request\RegexRequestMatcher
        mediamonks_rest_api.request_transformer.class: MediaMonks\RestApi\Request\RequestTransformer
        mediamonks_rest_api.response_transformer.class: MediaMonks\RestApi\Response\ResponseTransformer
        mediamonks_rest_api.serializer.jms.class: MediaMonks\RestApi\Serializer\JMSSerializer
        mediamonks_rest_api.serializer.json.class: MediaMonks\RestApi\Serializer\JsonSerializer
        mediamonks_rest_api.serializer.msgpack.class: MediaMonks\RestApi\Serializer\MsgpackSerializer
        mediamonks_rest_api.response_model.class: MediaMonks\RestApi\Model\ResponseModel
        mediamonks_rest_api.response_model_factory.class: MediaMonks\RestApi\Model\ResponseModelFactory

    services:
        mediamonks_rest_api.rest_api_event_subscriber:
            class: '%mediamonks_rest_api.rest_api_event_subscriber.class%'
            arguments:
                - '@mediamonks_rest_api.request_matcher'
                - '@mediamonks_rest_api.request_transformer'
                - '@mediamonks_rest_api.response_transformer'
                - '@mediamonks_rest_api.response_model_factory'
            tags:
                - { name: event_subscriber }

        mediamonks_rest_api.request_matcher:
            class: '%mediamonks_rest_api.path_request_matcher.class%'
            arguments: ['/api']

        mediamonks_rest_api.request_transformer:
            class: '%mediamonks_rest_api.request_transformer.class%'
            arguments:
                - '@mediamonks_rest_api.serializer.json'

        mediamonks_rest_api.response_transformer:
            class: '%mediamonks_rest_api.response_transformer.class%'
            arguments:
                - '@mediamonks_rest_api.serializer.json'
                - '@mediamonks_rest_api.response_model_factory'
                - []

        mediamonks_rest_api.serializer.json:
            class: '%mediamonks_rest_api.serializer.json.class%'

        mediamonks_rest_api.response_model:
            class: '%mediamonks_rest_api.response_model.class%'

        mediamonks_rest_api.response_model_factory:
            class: '%mediamonks_rest_api.response_model_factory.class%'
            arguments:
                - '@mediamonks_rest_api.response_model'


Then activate the module, clear caches and start creating controllers which start with /api for it to take effect.

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md

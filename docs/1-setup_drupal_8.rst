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

    services:
      MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriber:
        autowire: true
        tags:
          - { name: event_subscriber }

      MediaMonks\RestApi\Request\PathRequestMatcher:
        public: false
        arguments:
          - '/api'

      MediaMonks\RestApi\Request\RequestTransformer:
        public: false
        autowire: true

      MediaMonks\RestApi\Response\ResponseTransformer:
        public: false
        autowire: true

      MediaMonks\RestApi\Serializer\JsonSerializer:
        public: false

      MediaMonks\RestApi\Model\ResponseModel:
        public: false

      MediaMonks\RestApi\Model\ResponseModelFactory:
        public: false
        autowire: true


Then activate the module, clear caches and start creating controllers which start with /api for it to take effect.

Please note this example uses autowiring which is available since Drupal 8.5

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md

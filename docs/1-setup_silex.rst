Step 1: Setting up the library in Silex
=======================================

A) Download the library
-----------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    $ composer require mediamonks/rest-api

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

B) Setup the library
--------------------

In the `Silex`_ micro-framework you can use this library with just a single line of code:

.. code-block:: php

    require_once __DIR__ . '/../../vendor/autoload.php';

    use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;
    use MediaMonks\RestApi\Response\JsonResponse;

    $app = new Silex\Application();

    $app['dispatcher']->addSubscriber(RestApiEventSubscriberFactory::create());

    $app->get('/api', function() {
        return new JsonResponse('Hello Api');
    });

    $app->run();

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
.. _`Silex`: http://silex.sensiolabs.org/

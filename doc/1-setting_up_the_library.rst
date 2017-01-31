Step 1: Setting up the library
==============================

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

When using Symfony it is highly recommended to use our `Symfony Rest API Bundle`_.

When not using a different framework it is recommended to use the RestApiEventSubscriberFactory to create an instance
of the RestApiEventSubscriber. Make sure to add it to your event dispatcher as a subscriber for it to take effect.

.. code-block:: php

    use MediaMonks\RestApi\EventSubscriber;
    use Symfony\Component\EventDispatcher\EventDispatcher;

    $restApiEventSubscriber = RestApiEventSubscriberFactory::create();

    $dispatcher = new EventDispatcher();
    $dispatcher->addSubscriber($eventSubscriber);

More code and library examples can be found in the example dir.

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
.. _`Symfony Rest API Bundle`: https://github.com/mediamonks/symfony-rest-api-bundle

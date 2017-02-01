Step 2: Configuration
=====================

Path
----

The event subscriber is activated by the path ``/api`` by default, you can override this by passing the 'path' option:

.. code-block:: php

    use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;

    $eventSubscriber = RestApiEventSubscriberFactory::create(['path' => '/my-api']);


Debug Mode
----------

When debug mode is enabled a stack trace will be outputted when an exception is detected.
Debug mode is disabled by default.

.. code-block:: php

    use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;

    $eventSubscriber = RestApiEventSubscriberFactory::create(['debug' => true]);


Request Matcher
---------------

The library uses a Path matcher by default. You can also pass a different matcher if you like, as long as it imlements
the ``MediaMonks\RestApi\Request\RequestMatcherInterface``:

.. code-block:: php

    use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;

    $eventSubscriber = RestApiEventSubscriberFactory::create([
        'request_matcher' => new \My\Custom\RequestMatcher()
    ]);


Serializer
----------

You can configure the serializer which is used. By default a json serializer is used however it is possible to override
this by creating your own class which implements the ``MediaMonks\RestApi\Serializer\SerializerInterface``.

You can then pass it to the create method:

.. code-block:: php

    use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;

    $eventSubscriber = RestApiEventSubscriberFactory::create([
        'serializer' => new \My\Custom\Serializer()
    ]);


Post Message Origin
-------------------

Because of security reasons the default post message origin is empty by default.

You can set it by adding it to your configuration:

.. code-block:: php

    use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;

    $eventSubscriber = RestApiEventSubscriberFactory::create(['post_message_origin' => 'https://www.mediamonks.com']);


Response Model
--------------

Since this bundle was originally created according to the internal api spec of MediaMonks this is the default behavior.
However it is possible to override this by creating your own class which implements the
``MediaMonks\RestApi\Model\ResponseModelInterface``. You can then pass it to the create method:

.. code-block:: php

    use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;

    $eventSubscriber = RestApiEventSubscriberFactory::create([
        'response_model' => new \My\Custom\ResponseModel()
    ]);

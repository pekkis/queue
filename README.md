Pekkis Queue
=============

A simple queue abstraction library. Extracted from Xi Filelib.

What it does?
--------------

Everything implementing interface Enqueueable can be queued. Queue processor listens to a queue. Back comes a Message.
MessageHandlers handle messages. They return a result with a success flag. New enqueueables may be queued from
a result.

See Xi Filelib for a real use case.

Got ideas / wishes? Contact or create a pull request! Cheers!

Supported queues
-----------------

RabbitMQ (via PECL and pure PHP).

Upcoming support
-----------------

IronMQ
Amazon SQS


UPGRADING
==========

From 0.3 to 0.4
----------------

- New messages, while processing, are not added to the result object and then queued. Queue is passed as a second
  argument instead and your handler must enqueue it's new messages.
- There are new features you should consider using (automatic serializing / deserializing).

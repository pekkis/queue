# CHANGELOG

## 1.0

- Throws namespaced exceptions instead of generic RuntimeException ones.
- Handles dequeue errors in processor.
- Fixed segmentation faulting in tests.
- Bumped PHP requirement to 5.5+.

## 0.4

- Queue: Symfony specific stuff was moved to SymfonyBridge. If you rely on Symfony events, use
  the EventDispatchingQueue wrapper for queue.
- Queue: There are new features you should consider using (automatic serializing / deserializing).
- Queue: Enqueueable is gone. Enqueue now takes type (topic) and data instead of an Enqueueable and returns a message.
- Processor: All functionality rely on the SymfonyBridge components.
- Processor: new messages, while processing, are not added to the result object and then queued. Queue is passed as a second
  argument instead and your handler must enqueue it's new messages.

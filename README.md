# Error Component - Mendo Framework

This component catches the exceptions and errors that weren't handled.
It allows you to log errors and to add an error handler that will further handle the error.

```php
(new Mendo\Error\ErrorCatcher())
    ->setErrorHandler($errorhandler)
    ->setLogger($logger)
    ->setDefaultLogLevel('error')
    ->setLogLevel(500, 'error') // if your exceptions contain codes, you can change the log level accordingly
    ->setLogLevel(404, 'info')
    ->setLogLevel(403, 'info')
    ->setLogLevel(401, 'info');
```

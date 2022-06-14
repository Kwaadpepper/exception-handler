# Changelog

All notable changes to `ExceptionHandler` will be documented in this file.


# version 0.0.9
- Multiline exception message in mail.

# version 0.0.8
- Make sure sending mail error is always correctly handled (write in logs)

# version 0.0.7
- Added support for laravel 9

# version 0.0.6
- Added class name to email message

# version 0.0.5
- Fixed Handle cases where exception came from a function instead of a class
# version 0.0.4
- Fixed Try to fix undefined index file error

## version 0.0.3
- Fixed getAnonymizedStackTrace change param type to Throwable
- Fixed remove finally on reportExceptionByEmail to let exceptions bubble

## version 0.0.2
Fix usage with laravel 7

## Version 0.0.1
First release version

# Changelog

All notable changes to `ExceptionHandler` will be documented in this file.

# version 2.0.0
- Changed enhance getAnonymizedMessage (for DB queries)
- Changed Support only for laravel 10
- Added env element EXCEPTION_MAIL_LIST

# version 0.1.1
- Changed better parsing for email recipients
- Changed if no recipients are available, a warning will be emitted, also native Laravel report method will be called instead.
- Changed if mail report fails, native reporting method will be called.

# version 0.1.0
- Add a a method to manually send exception by email (plus the same to call statically).

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

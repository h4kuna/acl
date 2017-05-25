<?php

namespace h4kuna\Acl;

abstract class Exception extends \Exception {}

// Development exceptions
class InvalidArgumentException extends Exception {}

class MethodIsNotImplementedException extends Exception {}

// Authenticator
abstract class AuthenticatorException extends Exception {}

class IdentityNotFoundException extends AuthenticatorException {}

class InvalidPasswordException extends AuthenticatorException {}

class IdentityIsBlockedException extends AuthenticatorException {}


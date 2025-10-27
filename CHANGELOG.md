# Changelog

All notable changes to FormGenerator V2 will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.0] - 2025-10-27

### Added - Laravel-Style Validation System (Phase 1)

#### Core Validation Features
- **25 Built-in Validation Rules**:
  - Type validation: `required`, `string`, `boolean`, `integer`, `numeric`, `array`
  - Pattern validation: `alpha`, `alpha_numeric`, `regex`, `digits`
  - Size validation: `min`, `max`, `between`
  - Format validation: `email`, `url`, `ip`, `json`
  - Date validation: `date`, `date_format`, `before`, `after`
  - Comparison validation: `confirmed`, `in`, `not_in`
  - Database validation: `unique`, `exists` (with PDO support)

- **Validator Class** (`FormGenerator\V2\Validation\Validator`)
  - Laravel-style rule parsing (e.g., `"required|email|min:3"`)
  - Array-based rule definitions
  - Custom error messages per field/rule
  - Custom attribute names for user-friendly errors
  - Bail mode (stop validation on first failure)
  - Returns validated data only (excludes non-validated fields)
  - Event integration (PRE_SUBMIT, VALIDATION_SUCCESS, VALIDATION_ERROR, POST_SUBMIT)

- **ValidatorFactory Class** (`FormGenerator\V2\Validation\ValidatorFactory`)
  - `make()` - Create validator instances
  - `validate()` - Validate and throw exception on failure
  - `makeBail()` - Create validator with bail mode enabled
  - `setDefaultConnection()` - Set default PDO connection for all validators

- **ValidationException Class** (`FormGenerator\V2\Validation\ValidationException`)
  - Error bag with field-specific errors
  - `errors()` - Get all errors
  - `first($field)` - Get first error for a field
  - `all()` - Get all error messages as flat array
  - `toJson()` - Export errors as JSON

- **RuleInterface** (`FormGenerator\V2\Validation\Rules\RuleInterface`)
  - Consistent contract for all validation rules
  - Easy to extend with custom validation rules
  - `passes()`, `message()`, and `name()` methods

#### FormBuilder Integration

- **InputBuilder Validation Methods** (25+ new chainable methods):
  ```php
  ->required()              // Field is required
  ->email()                 // Valid email format
  ->numeric()               // Numeric value
  ->integer()               // Integer value
  ->string()                // String value
  ->boolean()               // Boolean value
  ->array()                 // Array value
  ->url()                   // Valid URL
  ->ip(?string $version)    // Valid IP (optional: 'ipv4' or 'ipv6')
  ->json()                  // Valid JSON
  ->alpha()                 // Only alphabetic characters
  ->alphaNumeric()          // Only alphanumeric characters
  ->digits(?int $length)    // Numeric digits with optional exact length
  ->min(int|float $value)   // Minimum value/length/count
  ->max(int|float $value)   // Maximum value/length/count
  ->between(int|float $min, int|float $max)  // Between min and max
  ->date()                  // Valid date
  ->dateFormat(string $format)  // Match specific date format
  ->before(string $date)    // Date before specified date
  ->after(string $date)     // Date after specified date
  ->confirmed(?string $field)  // Match confirmation field
  ->in(array $values)       // Value in allowed list
  ->notIn(array $values)    // Value not in disallowed list
  ->unique(string $table, ?string $column, mixed $except, string $idColumn)  // Unique in database
  ->exists(string $table, ?string $column)  // Exists in database
  ->regex(string $pattern, ?string $message)  // Match regex pattern
  ->rules(string $rules)    // Laravel-style rule string
  ```

- **FormBuilder::validateData()** Method
  - Validate form data using rules from all inputs
  - Automatic rule extraction from InputBuilder validation methods
  - Custom error messages support
  - Custom attribute names support
  - Database connection support for unique/exists rules
  - Event dispatching integration
  - Returns validated data or throws ValidationException

- **Automatic Rule Extraction**
  - Converts InputBuilder validation methods to Laravel-style rules
  - Supports all 25 validation rules
  - Handles complex rules (e.g., unique with exceptions, between with ranges)

#### Testing

- **ValidatorTest.php** - Comprehensive unit tests
  - Tests for all 25 validation rules
  - Custom error messages testing
  - Custom attribute names testing
  - Bail mode testing
  - Multiple rules combination testing
  - Edge cases and error conditions

- **ValidatorFactoryTest.php** - Factory method tests
  - make() method testing
  - validate() method testing
  - makeBail() method testing
  - Default connection testing

- **DatabaseValidationRulesTest.php** - Database rule tests
  - unique rule with PDO mocking
  - exists rule with PDO mocking
  - confirmed rule testing
  - Error conditions testing

#### Documentation

- **VALIDATION.md** (500+ lines)
  - Quick start guide
  - Complete reference for all 25 validation rules
  - FormBuilder integration guide
  - Database validation setup
  - Custom error messages and attributes
  - ValidatorFactory usage
  - Advanced features (bail mode, events, validated data)
  - Real-world examples (user registration, profile update, API validation)
  - Migration guide for Laravel users
  - Best practices

### Changed

- **FormBuilder.php**
  - Added `validateData()` method for server-side validation
  - Added `extractValidationRules()` private method for rule extraction
  - Enhanced event dispatching for validation lifecycle

- **InputBuilder.php**
  - Added 25+ validation chain methods
  - Enhanced validation rules array structure
  - Improved documentation and type hints

### Technical Details

- **PHP Version**: Requires PHP 8.1+
- **Features Used**: readonly properties, typed properties, union types, enums
- **Architecture**: Event-driven with full backward compatibility
- **Database**: PDO support for database validation rules
- **Design Pattern**: Fluent interface throughout
- **PSR Compliance**: PSR-4 autoloading

### Examples

#### Basic Validation
```php
use FormGenerator\V2\Validation\ValidatorFactory;

$validated = ValidatorFactory::validate($_POST, [
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:8|confirmed',
    'age' => 'required|integer|between:18,100',
]);
```

#### FormBuilder Integration
```php
use FormGenerator\V2\Builder\FormBuilder;

$form = FormBuilder::create('register')
    ->addText('username', 'Username')
        ->required()
        ->alphaNumeric()
        ->minLength(3)
        ->maxLength(20)
        ->unique('users', 'username')
        ->add()
    ->addEmail('email', 'Email')
        ->required()
        ->email()
        ->unique('users', 'email')
        ->add()
    ->addPassword('password', 'Password')
        ->required()
        ->minLength(8)
        ->confirmed()
        ->add()
    ->build();

// Validate on submit
try {
    $pdo = new PDO('mysql:host=localhost;dbname=myapp', 'user', 'pass');
    $validated = $form->validateData($_POST, [], [], $pdo);
} catch (ValidationException $e) {
    $errors = $e->errors();
}
```

### Breaking Changes

None. All changes are backward compatible.

### Migration Notes

If you're familiar with Laravel validation, the API is nearly identical:

```php
// Laravel
$validator = Validator::make($request->all(), [
    'email' => 'required|email|unique:users',
]);

// FormGenerator V2
$validator = ValidatorFactory::make($_POST, [
    'email' => 'required|email|unique:users',
]);
```

---

## [2.0.0] - 2024-10-27

### Added - FormGenerator V2 Initial Release

#### Core Features
- Class-based FormType system (Symfony-style)
- Event-driven architecture with 9 form lifecycle events
- Built-in date/time pickers with multi-language support
- Form-level RTL/LTR direction support
- Form-level locale support
- Multiple output formats (HTML, JSON, XML)
- BaseFormRequest integration for Laravel

#### Events System
- **9 Form Lifecycle Events**:
  - PRE_SET_DATA - Before data is set
  - POST_SET_DATA - After data is set
  - PRE_BUILD - Before form is built
  - POST_BUILD - After form is built
  - PRE_SUBMIT - Before form is submitted
  - SUBMIT - During form submission
  - POST_SUBMIT - After form is submitted
  - VALIDATION_SUCCESS - When validation succeeds
  - VALIDATION_ERROR - When validation fails

- **EventDispatcher** with priority-based execution
- **Event propagation control** (stop propagation)
- **Event subscriber interface** for organized event handling

#### FormType System
- AbstractFormType base class
- FormTypeInterface for custom types
- createFromType() factory method
- Symfony-style form building

#### Pickers & Localization
- DatePicker with multi-language support
- TimePicker with 12/24 hour formats
- DateTimePicker combining date and time
- RangePicker for date ranges
- 10+ language support (en, tr, de, fr, es, etc.)
- Automatic locale propagation to all pickers
- Customizable picker options

#### Direction Support
- TextDirection enum (LTR, RTL)
- Form-level direction setting
- Automatic propagation to all inputs
- RTL-aware UI components

#### Output Formats
- HTML output (default)
- JSON output
- XML output
- OutputFormat enum

#### Laravel Integration
- BaseFormRequest abstract class
- Authorization support
- Custom validation messages
- Event hook support

#### Testing
- 150+ comprehensive unit tests
- Full code coverage for new features
- Integration tests

#### Documentation
- Complete API documentation
- Event system guide
- FormType usage guide
- Picker configuration guide
- Laravel integration guide

### Technical Details

- **PHP Version**: 8.1+
- **Architecture**: Event-driven, OOP, Chain pattern
- **Backward Compatibility**: Fully backward compatible with V1
- **Design Patterns**: Builder, Factory, Observer, Strategy

---

## [1.x] - Legacy Version

For changes in version 1.x, please refer to the git history.

---

## Versioning

This project follows [Semantic Versioning](https://semver.org/):
- **MAJOR** version for incompatible API changes
- **MINOR** version for new functionality in a backward compatible manner
- **PATCH** version for backward compatible bug fixes

## Links

- [Documentation](./docs/V2/)
- [Repository](https://github.com/selcukmart/FormGenerator)
- [Issue Tracker](https://github.com/selcukmart/FormGenerator/issues)

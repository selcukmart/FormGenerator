# Changelog

All notable changes to FormGenerator V2 will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.3.1] - 2025-10-27

### Added - Symfony-Inspired Data Transformation System

Complete data transformation support for converting data between model format (normalized) and view format (denormalized), inspired by Symfony's DataTransformerInterface.

#### Core Data Transformer Features

- **DataTransformerInterface** (`FormGenerator\V2\Contracts\DataTransformerInterface`)
  - `transform($value)` - Convert model → view format
  - `reverseTransform($value)` - Convert view → model format
  - Standard contract for all transformers
  - Exception handling support

- **AbstractDataTransformer** (`FormGenerator\V2\DataTransformer\AbstractDataTransformer`)
  - Base class with common transformation utilities
  - Null value handling
  - Empty value handling
  - Type validation helpers
  - Error handling and logging

#### Built-in Transformers

- **DateTimeToStringTransformer** (`FormGenerator\V2\DataTransformer\DateTimeToStringTransformer`)
  - Convert DateTime/DateTimeImmutable ↔ formatted string
  - Customizable date format (Y-m-d, d/m/Y, etc.)
  - Timezone support (input/output timezones)
  - Error handling for invalid dates
  - Example: `new DateTime('2024-01-15')` ↔ `'2024-01-15'`

- **StringToArrayTransformer** (`FormGenerator\V2\DataTransformer\StringToArrayTransformer`)
  - Convert array ↔ delimited string
  - Customizable delimiter (comma, semicolon, pipe, etc.)
  - Automatic trimming of values
  - Empty value filtering
  - Example: `['php', 'symfony', 'laravel']` ↔ `'php, symfony, laravel'`

- **NumberToLocalizedStringTransformer** (`FormGenerator\V2\DataTransformer\NumberToLocalizedStringTransformer`)
  - Convert number ↔ localized string representation
  - Customizable decimal separator
  - Customizable thousands separator
  - Precision control
  - Rounding mode support
  - Example: `75000.50` ↔ `'75,000.50'` (US) or `'75.000,50'` (EU)

- **BooleanToStringTransformer** (`FormGenerator\V2\DataTransformer\BooleanToStringTransformer`)
  - Convert boolean ↔ string representation
  - Customizable true/false values
  - Smart parsing (yes/no, on/off, 1/0, true/false)
  - Example: `true` ↔ `'yes'` or `'1'` or `'active'`

- **CallbackTransformer** (`FormGenerator\V2\DataTransformer\CallbackTransformer`)
  - Custom transformation logic using closures
  - Perfect for quick transformations
  - No need to create dedicated transformer class
  - Example: Entity ↔ ID, uppercase/lowercase, JSON encoding, etc.

#### InputBuilder Integration

- **addTransformer()** Method
  - Add transformer to input field
  - Chainable with other InputBuilder methods
  - Multiple transformers can be added (chaining)
  - Transformers applied in order

- **setTransformers()** Method
  - Replace all transformers at once
  - Array of DataTransformerInterface instances

- **getTransformers()** Method
  - Get all transformers for the field

- **hasTransformers()** Method
  - Check if field has any transformers

- **transformValue()** Method
  - Apply transformations (model → view)
  - Called automatically during form build
  - Internal use by FormBuilder

- **reverseTransformValue()** Method
  - Apply reverse transformations (view → model)
  - Applied in reverse order
  - Internal use by FormBuilder

#### FormBuilder Integration

- **Automatic Transform Application**
  - Transformers automatically applied during `buildInputsContext()`
  - Model data transformed to view format before rendering
  - Graceful error handling with logging

- **applyReverseTransform()** Method
  - Transform submitted form data back to model format
  - Call after form submission to get properly typed data
  - Returns array with transformed values
  - Preserves non-transformed fields
  - Error handling with detailed messages

- **applyTransformToValue()** Private Method
  - Internal method for applying transformations
  - Error logging on transformation failure
  - Returns original value on error

#### Examples & Documentation

- **WithDataTransformation.php** (`Examples/V2/WithDataTransformation.php`)
  - Comprehensive examples of all transformers
  - Basic transformation examples
  - Custom transformation with callbacks
  - Form submission processing
  - Chaining multiple transformers
  - Real-world use cases

- **DataTransformationController.php** (`Examples/Symfony/DataTransformationController.php`)
  - Symfony controller integration example
  - Entity ↔ ID transformation
  - JSON array transformation
  - DateTime transformation
  - Database entity loading
  - API endpoint with transformations

#### Use Cases

1. **DateTime Handling**
   ```php
   ->addDate('birthday', 'Birthday')
       ->addTransformer(new DateTimeToStringTransformer('Y-m-d'))
       ->add()
   ```

2. **Array/Tags Input**
   ```php
   ->addText('tags', 'Skills')
       ->addTransformer(new StringToArrayTransformer(', '))
       ->add()
   ```

3. **Entity Selection**
   ```php
   ->addSelect('department', 'Department')
       ->options($departmentOptions)
       ->addTransformer(new CallbackTransformer(
           fn($dept) => $dept?->getId(),
           fn($id) => $repository->find($id)
       ))
       ->add()
   ```

4. **Localized Numbers**
   ```php
   ->addText('price', 'Price')
       ->addTransformer(new NumberToLocalizedStringTransformer(2, ',', '.'))
       ->add()
   ```

5. **Boolean Radio Buttons**
   ```php
   ->addRadio('is_active', 'Status')
       ->options(['yes' => 'Active', 'no' => 'Inactive'])
       ->addTransformer(new BooleanToStringTransformer('yes', 'no'))
       ->add()
   ```

### Technical Details

- **PHP Version**: Requires PHP 8.1+
- **Features Used**: readonly properties, typed properties, union types, mixed type
- **Architecture**: Strategy pattern with composition
- **Inspired By**: Symfony DataTransformerInterface
- **Backward Compatibility**: Fully backward compatible
- **Error Handling**: Graceful degradation with logging

### Breaking Changes

None. All changes are backward compatible.

### Migration Notes

Data transformation is opt-in. Existing forms continue to work without any changes. To use transformers:

```php
// Before (manual conversion)
$birthday = new DateTime($_POST['birthday']);

// After (automatic transformation)
$form->addDate('birthday')->addTransformer(new DateTimeToStringTransformer('Y-m-d'));
$modelData = $form->applyReverseTransform($_POST);
// $modelData['birthday'] is already a DateTime object
```

---

## [2.2.0] - 2025-10-27

### Added - Blade Template Engine Support

Complete Laravel Blade template engine integration for FormGenerator V2, providing the same level of support as Twig and Smarty template engines.

#### Core Blade Components

- **BladeRenderer** (`FormGenerator\V2\Renderer\BladeRenderer`)
  - Full RendererInterface implementation
  - Laravel Illuminate/View integration
  - Blade template compilation and caching
  - Global variables support
  - Custom directives support
  - Template existence checking
  - Cache management (enable/disable/clear)
  - Helper methods: `renderAttributes()`, `renderClasses()`
  - Standalone or Laravel-integrated usage

#### Blade Directives

- **Form Control Directives**:
  - `@formStart(name, options)` - Start a form
  - `@formEnd` - End and render the form

- **Input Directives** (11 directives):
  - `@formText(name, label, options)` - Text input
  - `@formEmail(name, label, options)` - Email input
  - `@formPassword(name, label, options)` - Password input
  - `@formTextarea(name, label, options)` - Textarea
  - `@formNumber(name, label, options)` - Number input
  - `@formDate(name, label, options)` - Date input
  - `@formSelect(name, label, selectOptions, options)` - Select dropdown
  - `@formCheckbox(name, label, options)` - Checkbox
  - `@formRadio(name, label, radioOptions, options)` - Radio buttons
  - `@formSubmit(label, options)` - Submit button
  - `@formButton(label, type, options)` - Button

- **Helper Directives**:
  - `@attributes($array)` - Render HTML attributes from array
  - `@classes($array)` - Render CSS classes from array
  - `@csrf($formName)` - CSRF token field

#### Blade Components

Modern component-based syntax for Laravel 8+:

- `<x-form>` - Form wrapper component
- `<x-form-text>` - Text input component
- `<x-form-email>` - Email input component
- `<x-form-password>` - Password input component
- `<x-form-textarea>` - Textarea component
- `<x-form-number>` - Number input component
- `<x-form-select>` - Select dropdown component
- `<x-form-submit>` - Submit button component

All components support:
- Required/optional fields
- Placeholder text
- Help text
- Default values
- CSS classes
- Validation attributes (min, max, minLength, maxLength, pattern)
- Readonly/disabled states

#### Laravel Integration

- **BladeServiceProvider** (`FormGenerator\V2\Integration\Blade\BladeServiceProvider`)
  - Auto-registration of directives and components
  - Service container bindings
  - BladeRenderer singleton
  - Configuration publishing
  - Laravel 11+ auto-discovery support

- **Service Container Bindings**:
  - `BladeRenderer::class` - Singleton blade renderer
  - `RendererInterface::class` - Bound to BladeRenderer
  - `FormBuilder::class` - Singleton form builder with Blade renderer

#### Examples & Documentation

- **Usage Examples**:
  - `examples/blade/user-registration.blade.php` - Registration form using directives
  - `examples/blade/contact-form-components.blade.php` - Contact form using components
  - `examples/blade/README.md` - Comprehensive Blade integration guide

- **Directive Syntax Example**:
  ```blade
  @formStart('user-form', ['action' => '/submit', 'method' => 'POST'])
  @formText('username', 'Username', ['required' => true, 'minLength' => 3])
  @formEmail('email', 'Email', ['required' => true])
  @formPassword('password', 'Password', ['required' => true])
  @formSubmit('Register')
  @formEnd
  ```

- **Component Syntax Example**:
  ```blade
  <x-form name="user-form" action="/submit" method="POST">
      <x-form-text name="username" label="Username" required />
      <x-form-email name="email" label="Email" required />
      <x-form-password name="password" label="Password" required />
      <x-form-submit>Register</x-form-submit>
  </x-form>
  ```

#### Testing

- **BladeRendererTest** - Comprehensive unit tests
  - Template rendering
  - Global variables
  - Template existence checking
  - Cache management
  - Helper methods testing
  - Path management

### Technical Details

- **PHP Version**: 8.1+
- **Laravel Support**: 10.x, 11.x (auto-discovery enabled)
- **Illuminate Packages**: illuminate/view, illuminate/filesystem, illuminate/events
- **Architecture**: Service provider pattern, component-based
- **Backward Compatibility**: Full backward compatibility maintained

### Installation

```bash
composer require selcukmart/form-generator
```

For Laravel:
```bash
php artisan vendor:publish --tag=form-generator-config
```

### Breaking Changes

None. All changes are backward compatible.

### Migration from 2.1.0

No migration required. Blade support is additive - existing code continues to work unchanged.

---

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

# FormGenerator

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-2.3.1-blue.svg)](CHANGELOG.md)

Modern PHP Form Generator with Chain Pattern, Event-Driven Architecture, Data Transformation, Symfony & Laravel Integration

---

## 🎉 Version 2.3.1 is Here!

**FormGenerator V2.3.1** adds powerful Data Transformation support inspired by Symfony!

### 📖 [Read Full V2 Documentation →](README_V2.md)

### Quick V2.3.1 Example - Data Transformation

```php
use FormGenerator\V2\Builder\FormBuilder;
use FormGenerator\V2\DataTransformer\DateTimeToStringTransformer;
use FormGenerator\V2\DataTransformer\StringToArrayTransformer;

// Model data with proper types
$userData = [
    'name' => 'John Doe',
    'birthday' => new \DateTime('1990-05-15'),  // DateTime object
    'tags' => ['php', 'symfony', 'laravel'],     // Array
];

$form = FormBuilder::create('user_form')
    ->setRenderer($renderer)
    ->setTheme($theme)
    ->setData($userData)

    ->addText('name', 'Full Name')
        ->required()
        ->add()

    // DateTime ↔ String transformation (NEW in v2.3.1!)
    ->addDate('birthday', 'Birthday')
        ->addTransformer(new DateTimeToStringTransformer('Y-m-d'))
        ->add()

    // Array ↔ String transformation (NEW in v2.3.1!)
    ->addText('tags', 'Skills')
        ->addTransformer(new StringToArrayTransformer(', '))
        ->add()

    ->addSubmit('save')
    ->build();

echo $form; // DateTime & Array automatically formatted for display!

// After form submission - transform back to model format
$modelData = $form->applyReverseTransform($_POST);
// $modelData['birthday'] is now a DateTime object
// $modelData['tags'] is now an array
```

### Quick V2.3 Example - Event-Driven Dependencies

```php
use FormGenerator\V2\Builder\FormBuilder;
use FormGenerator\V2\Event\FieldEvent;

$form = FormBuilder::create('user_form')
    ->setRenderer($renderer)
    ->setTheme($theme)

    ->addText('name', 'Full Name')
        ->required()
        ->minLength(3)
        ->add()

    ->addEmail('email', 'Email Address')
        ->required()
        ->add()

    // Dependency Management - Pure JavaScript!
    ->addRadio('user_type', 'User Type')
        ->options(['personal' => 'Personal', 'business' => 'Business'])
        ->isDependency()
        ->add()

    // Event-Driven Dependencies
    ->addText('company_name', 'Company Name')
        ->dependsOn('user_type', 'business') // Auto show/hide!
        ->onShow(function(FieldEvent $event) {
            // Make required when visible
            $event->getField()->required(true);
        })
        ->onHide(function(FieldEvent $event) {
            // Optional when hidden
            $event->getField()->required(false);
        })
        ->add()

    ->addSubmit('save')
    ->build();

echo $form; // That's it!
```

### Query String Based Forms (NEW!)

```php
// Control fields with URL parameters: ?role=admin&mode=advanced
$role = $_GET['role'] ?? '';
$mode = $_GET['mode'] ?? '';

$form = FormBuilder::create('query_form')
    ->enableServerSideDependencyEvaluation() // PHP-side rendering
    ->setData(['role' => $role, 'mode' => $mode]);

$form->addHidden('role', $role)->isDependency()->add();
$form->addHidden('mode', $mode)->isDependency()->add();

// Only visible with ?role=admin (NOT rendered in HTML if condition unmet)
$form->addTextarea('admin_notes', 'Admin Panel')
    ->dependsOn('role', 'admin')
    ->add();

// Only visible with ?mode=advanced
$form->addText('advanced_settings', 'Advanced Settings')
    ->dependsOn('mode', 'advanced')
    ->add();

echo $form->build();
```

### What's New in V2.3.1?

**🆕 Data Transformation (Symfony-Inspired)**
- ✅ **DateTimeToStringTransformer**: Convert DateTime objects ↔ formatted strings
- ✅ **StringToArrayTransformer**: Convert arrays ↔ comma-separated strings
- ✅ **NumberToLocalizedStringTransformer**: Format numbers with locale settings
- ✅ **BooleanToStringTransformer**: Convert boolean ↔ string representations
- ✅ **CallbackTransformer**: Custom transformation logic with closures
- ✅ **Chainable Transformers**: Apply multiple transformers in sequence
- ✅ **Automatic Application**: Transformers applied during form build and submission

### What's New in V2.3?

**🆕 Event-Driven Dependencies**
- ✅ **Field-Level Events**: `onShow()`, `onHide()`, `onValueChange()` and 10+ more
- ✅ **Server-Side Evaluation**: PHP conditional rendering with `enableServerSideDependencyEvaluation()`
- ✅ **Query String Support**: Conditional fields based on URL parameters
- ✅ **Custom Dependency Logic**: `onDependencyCheck()` for complex conditions
- ✅ **Framework-Agnostic**: Pure native PHP, no external dependencies

**Core Features**
- ✅ **PHP 8.1+** with modern features (enums, attributes, readonly)
- ✅ **Chain Pattern** fluent interface
- ✅ **Data Transformation** inspired by Symfony (v2.3.1+)
- ✅ **Dependency Management** with pure JavaScript (no jQuery!)
- ✅ **Symfony & Laravel** integration out of the box
- ✅ **Multiple Data Sources**: Doctrine, Eloquent, PDO, Array
- ✅ **Template Engines**: Twig, Smarty 5 & Blade support
- ✅ **Bootstrap 5 & Tailwind** themes included
- ✅ **Security First**: CSRF, XSS protection, input sanitization
- ✅ **Type Safe**: Full type hints and IDE autocomplete

---

## 📦 Installation

```bash
composer require selcukmart/form-generator
```

---

## 🚀 Migrating from V1?

See the [**Upgrade Guide**](UPGRADE.md) for detailed migration instructions.

---

## V1 Documentation (Legacy)

**Note:** V1 is still supported for backward compatibility but V2 is recommended for new projects.

### Installation

```bash
composer require selcukmart/form-generator
```

### Basic Usage (V1)

```php
use FormGenerator\FormGeneratorDirector;

$form_generator_array = [
    'data' => [
        'from' => 'row',
        'row' => $row,
    ],
    'build' => [
        'format' => 'GenericBuilder',
        'render' => [
            'by' => 'smarty',
            'smarty' => $smarty,
        ],
    ],
    'inputs' => [
        'section1' => [
            [
                'type' => 'text',
                'attributes' => ['name' => 'username'],
            ],
        ],
    ],
];

$form_generator = new FormGeneratorDirector($form_generator_array, 'edit');
$form_generator->buildHtmlOutput();
echo $form_generator->getHtmlOutput();
```

### Scopes (V1)

- `add` - For creating new records
- `edit` - For editing existing records

---

## 📚 Documentation

- **[V2 Complete Documentation](README_V2.md)** ⭐ Recommended
- [Migration Guide from V1 to V2](UPGRADE.md)
- [Examples](/Examples/V2/)
  - [Basic Usage](/Examples/V2/BasicUsage.php)
  - [Data Transformation](/Examples/V2/WithDataTransformation.php) ⭐ NEW in v2.3.1!
  - [Symfony Data Transformation](/Examples/Symfony/DataTransformationController.php) ⭐ NEW in v2.3.1!
  - [Event-Driven Dependencies](/Examples/V2/WithEventDrivenDependencies.php)
  - [Query String Dependencies](/Examples/V2/WithQueryStringDependencies.php)
  - [Symfony Integration](/Examples/Symfony/QueryStringFormController.php)
  - [Dependency Management](/Examples/V2/WithDependencies.php)
  - [With Doctrine](/Examples/V2/WithDoctrine.php)
  - [With Laravel](/Examples/V2/WithLaravel.php)
  - [With Symfony DTO](/Examples/V2/WithSymfonyDTO.php)
  - [Validation Examples](/Examples/V2/WithValidation.php)
  - [Form Sections](/Examples/V2/WithSections.php)
  - [Form Stepper/Wizard](/Examples/V2/WithStepper.php)
  - [Built-in Pickers](/Examples/V2/WithPickers.php)
  - [CheckboxTree](/Examples/V2/WithCheckboxTree.php)
  - [Repeater Fields](/Examples/V2/WithRepeater.php)

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md).

---

## 📝 License

MIT License. See [LICENSE](LICENSE) for details.

---

## 👨‍💻 Author

**selcukmart**
- Email: admin@hostingdevi.com
- GitHub: [@selcukmart](https://github.com/selcukmart)

---

**Made with ❤️ using modern PHP**

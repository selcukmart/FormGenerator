# FormGenerator V2

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Modern PHP Form Generator with Chain Pattern, Symfony & Laravel Integration

## 🎯 What's New in V2

- **PHP 8.1+**: Modern PHP features (enums, attributes, typed properties, readonly)
- **Chain Pattern**: Fluent interface for intuitive form building
- **Multi-Framework**: Symfony Bundle & Laravel ServiceProvider
- **Data Providers**: Doctrine, Eloquent, PDO support
- **Security First**: Built-in CSRF, XSS protection, input sanitization
- **Modern Themes**: Bootstrap 5, Tailwind CSS included
- **Template Engines**: Twig & Smarty 5 support
- **🆕 Native Validation**: Built-in PHP + JavaScript validation (no jQuery!)
- **🆕 Symfony DTO Support**: Auto-extract validation from DTO/Entity
- **🆕 Dependency Management**: Pure JavaScript conditional fields
- **🆕 15+ Validation Rules**: required, email, minLength, pattern, etc.

## 🚀 Installation

```bash
composer require selcukmart/form-generator
```

## 📖 Quick Start

### Basic Usage

```php
use FormGenerator\V2\Builder\FormBuilder;
use FormGenerator\V2\Renderer\TwigRenderer;
use FormGenerator\V2\Theme\Bootstrap5Theme;

// Setup
$renderer = new TwigRenderer(__DIR__ . '/templates');
$theme = new Bootstrap5Theme();

// Build form with chain pattern
$form = FormBuilder::create('user_form')
    ->setAction('/users/save')
    ->setMethod('POST')
    ->setRenderer($renderer)
    ->setTheme($theme)

    // Add inputs fluently
    ->addText('name', 'Full Name')
        ->required()
        ->minLength(3)
        ->maxLength(100)
        ->placeholder('Enter your name')
        ->add()

    ->addEmail('email', 'Email Address')
        ->required()
        ->add()

    ->addPassword('password', 'Password')
        ->required()
        ->minLength(8)
        ->add()

    ->addSelect('country', 'Country')
        ->options([
            'us' => 'United States',
            'uk' => 'United Kingdom',
            'de' => 'Germany',
        ])
        ->add()

    ->addSubmit('save', 'Create Account')
    ->build();

echo $form;
```

### With Doctrine (Symfony)

```php
use FormGenerator\V2\DataProvider\DoctrineDataProvider;
use Doctrine\ORM\EntityManagerInterface;

$provider = new DoctrineDataProvider($entityManager, User::class);

$form = FormBuilder::create('edit_user')
    ->edit() // Set edit mode
    ->setDataProvider($provider)
    ->loadData($userId) // Auto-load from Doctrine

    ->addText('username', 'Username')
        ->readonly()
        ->add()

    ->addText('firstName', 'First Name')
        ->required()
        ->add()

    ->addSelect('role', 'Role')
        ->optionsFromProvider($roleProvider, 'id', 'name')
        ->add()

    ->addSubmit('update', 'Update User')
    ->build();
```

### With Laravel Eloquent

```php
use FormGenerator\V2\DataProvider\EloquentDataProvider;
use App\Models\User;

$provider = new EloquentDataProvider(User::class);

$form = FormBuilder::create('user_form')
    ->setDataProvider($provider)
    ->loadData($userId)

    ->addText('name')->required()->add()
    ->addEmail('email')->required()->add()
    ->addSubmit('save')
    ->build();

return view('users.form', ['form' => $form]);
```

## 🔧 Framework Integration

### Symfony

1. Register the bundle:

```php
// config/bundles.php
return [
    // ...
    FormGenerator\V2\Integration\Symfony\FormGeneratorBundle::class => ['all' => true],
];
```

2. Configure:

```yaml
# config/packages/form_generator.yaml
form_generator:
    default_theme: 'bootstrap5'
    default_renderer: 'twig'
    cache:
        enabled: true
        dir: '%kernel.cache_dir%/form_generator'
```

3. Use in controllers:

```php
use FormGenerator\V2\Integration\Symfony\FormType\FormGeneratorType;

$symfonyForm = $this->createForm(FormGeneratorType::class, $user, [
    'generator_builder' => $formBuilder,
]);
```

### Laravel

1. Publish configuration:

```bash
php artisan vendor:publish --tag=form-generator-config
```

2. Use in Blade templates:

```blade
@formGenerator($form)

<!-- Or include assets -->
@formAssets($theme)
```

3. In controllers:

```php
public function create()
{
    $form = app(FormBuilder::class)
        ->setRenderer(app(TwigRenderer::class))
        ->setTheme(app(Bootstrap5Theme::class))
        ->addText('name')->required()->add()
        ->addSubmit('create')
        ->build();

    return view('form', compact('form'));
}
```

## 🎨 Themes

### Bootstrap 5 (Included)

```php
use FormGenerator\V2\Theme\Bootstrap5Theme;

$theme = new Bootstrap5Theme();

// Enable floating labels
$theme->enableFloatingLabels();

// Enable horizontal form
$theme->enableHorizontalForm('col-md-3', 'col-md-9');
```

### Custom Theme

```php
use FormGenerator\V2\Theme\AbstractTheme;

class MyCustomTheme extends AbstractTheme
{
    protected function initialize(): void
    {
        $this->templateMap = [
            'text' => 'custom/input_text.twig',
            // ... other mappings
        ];

        $this->inputClasses = [
            'text' => [
                'wrapper' => 'my-form-group',
                'label' => 'my-label',
                'input' => 'my-input',
            ],
        ];
    }

    public function getName(): string
    {
        return 'My Custom Theme';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
```

## 🔒 Security

### CSRF Protection

```php
use FormGenerator\V2\Security\SecurityManager;

$security = new SecurityManager();

$form = FormBuilder::create('secure_form')
    ->setSecurity($security)
    ->enableCsrf() // Auto-generates CSRF token
    ->build();

// Validate on submit
if ($_POST) {
    $isValid = $security->validateCsrfToken(
        'secure_form',
        $_POST['_csrf_token']
    );
}
```

### Input Sanitization

```php
// Auto-sanitizes all input values
$security = new SecurityManager();
$form->setSecurity($security);

// Manual sanitization
$clean = $security->sanitize($_POST['user_input']);
$cleanHtml = $security->sanitize($_POST['html_content'], allowHtml: true);
```

### File Upload Validation

```php
$form->addFile('avatar', 'Profile Picture')
    ->attribute('accept', 'image/*')
    ->add();

// Validate upload
if ($security->validateFileUpload($_FILES['avatar'])) {
    $safeName = $security->getSafeFilename($_FILES['avatar']['name']);
    // Process upload
}
```

## 🔗 Dependency Management

**Native support for conditional field display** - Show/hide inputs based on other field values with **pure JavaScript** (no jQuery required)!

### Basic Dependency

```php
$form = FormBuilder::create('invoice_form')
    ->setRenderer($renderer)
    ->setTheme($theme)

    // Radio buttons that control visibility
    ->addRadio('invoice_type', 'Invoice Type')
        ->options([
            '1' => 'Individual',
            '2' => 'Corporate',
        ])
        ->controls('invoice_type') // Mark as dependency controller
        ->add()

    // Show only when invoice_type = 1
    ->addText('id_number', 'ID Number')
        ->required()
        ->dependsOn('invoice_type', '1')
        ->add()

    // Show only when invoice_type = 2
    ->addText('company_name', 'Company Name')
        ->required()
        ->dependsOn('invoice_type', '2')
        ->add()

    ->addSubmit('save')
    ->build();

// Pure JavaScript is automatically injected! No manual setup required.
```

### Select Dependencies

```php
// Country select controls other fields
->addSelect('country', 'Country')
    ->options([
        'us' => 'United States',
        'uk' => 'United Kingdom',
        'tr' => 'Turkey',
    ])
    ->controls('country') // Mark as controller
    ->add()

// Show state selector only for US
->addSelect('state', 'State')
    ->dependsOn('country', 'us')
    ->options([
        'ca' => 'California',
        'ny' => 'New York',
        'tx' => 'Texas',
    ])
    ->add()

// Show VAT field for UK and Turkey
->addText('vat_number', 'VAT Number')
    ->dependsOn('country', ['uk', 'tr']) // Multiple values!
    ->add()
```

### Checkbox Dependencies

```php
// Checkbox controls email field
->addCheckbox('newsletter', 'Subscribe to Newsletter')
    ->controls('newsletter_group')
    ->add()

// Show email when checkbox is checked
->addEmail('newsletter_email', 'Newsletter Email')
    ->required()
    ->dependsOn('newsletter', '1') // Checkbox value is '1' when checked
    ->add()
```

### How It Works

1. **Controller Fields**: Use `->controls('group_name')` to mark fields that control others
2. **Dependent Fields**: Use `->dependsOn('field_name', 'value')` to show/hide based on controller
3. **Auto JavaScript**: Pure JavaScript is automatically generated and injected once per form
4. **Smooth Animation**: Fields fade in/out with CSS transitions
5. **Form Validation**: Hidden fields are automatically disabled and cleared

### Features

- ✅ **Pure JavaScript** - No jQuery or other dependencies
- ✅ **Automatic Generation** - JavaScript injected automatically, only once per form
- ✅ **Multiple Values** - `dependsOn('field', ['val1', 'val2'])`
- ✅ **Smooth Animations** - Fade in/out transitions
- ✅ **Form-Specific** - Each form gets its own unique namespace
- ✅ **Smart Validation** - Hidden fields are disabled/cleared automatically
- ✅ **All Input Types** - Works with radio, select, checkbox, and more

### Advanced Example

```php
$form = FormBuilder::create('complex_form')
    ->setRenderer($renderer)
    ->setTheme($theme)

    // User type selector
    ->addSelect('user_type', 'User Type')
        ->options([
            'personal' => 'Personal',
            'business' => 'Business',
            'non_profit' => 'Non-Profit',
        ])
        ->controls('user_type')
        ->add()

    // Personal fields (shown for 'personal')
    ->addText('first_name', 'First Name')
        ->dependsOn('user_type', 'personal')
        ->add()

    ->addText('last_name', 'Last Name')
        ->dependsOn('user_type', 'personal')
        ->add()

    // Business fields (shown for 'business' or 'non_profit')
    ->addText('organization_name', 'Organization Name')
        ->dependsOn('user_type', ['business', 'non_profit'])
        ->add()

    // Tax ID (business only)
    ->addText('tax_id', 'Tax ID')
        ->dependsOn('user_type', 'business')
        ->add()

    // 501(c)(3) number (non-profit only)
    ->addText('nonprofit_number', '501(c)(3) Number')
        ->dependsOn('user_type', 'non_profit')
        ->add()

    ->addSubmit('register')
    ->build();
```

See `/Examples/V2/WithDependencies.php` for a complete working example.

## ✅ Validation System

**Built-in dual validation** - Both PHP backend and JavaScript frontend validation included!

### Basic Validation

```php
use FormGenerator\V2\Validation\NativeValidator;

$validator = new NativeValidator();

$form = FormBuilder::create('user_form')
    ->setValidator($validator)
    ->enableClientSideValidation() // Auto JavaScript validation!

    // Required field
    ->addText('username', 'Username')
        ->required()
        ->minLength(3)
        ->maxLength(20)
        ->add()

    // Email validation
    ->addEmail('email', 'Email')
        ->required()
        ->email() // Validates email format
        ->add()

    // Password with pattern
    ->addPassword('password', 'Password')
        ->required()
        ->minLength(8)
        ->pattern('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)/', 'Must contain uppercase, lowercase, and number')
        ->add()

    // Numeric range
    ->addNumber('age', 'Age')
        ->required()
        ->min(18)
        ->max(120)
        ->add()

    ->addSubmit('save')
    ->build();

// JavaScript validation automatically injected!
```

### Available Validation Rules

| Rule | Description | JavaScript Support |
|------|-------------|-------------------|
| `required()` | Field cannot be empty | ✅ |
| `email()` | Valid email format | ✅ |
| `minLength(n)` | Minimum string length | ✅ |
| `maxLength(n)` | Maximum string length | ✅ |
| `min(n)` | Minimum numeric value | ✅ |
| `max(n)` | Maximum numeric value | ✅ |
| `pattern(regex)` | Regex pattern match | ✅ |
| `url()` | Valid URL format | ✅ |
| `numeric()` | Must be numeric | ✅ |
| `integer()` | Must be integer | ✅ |
| `alpha()` | Only letters | ✅ |
| `alphanumeric()` | Letters and numbers | ✅ |
| `date()` | Valid date | ✅ |
| `match(field)` | Match another field | ✅ |
| `in(array)` | Value in whitelist | ✅ |

### Validation Features

- ✅ **Real-time validation** on blur
- ✅ **Clear errors** on input
- ✅ **Error summary** on submit
- ✅ **Focus first error** automatically
- ✅ **Dependency-aware** (skips hidden fields)
- ✅ **Bootstrap styling** for error messages
- ✅ **Custom rules** support

### Backend Validation

```php
// Validate on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $validator->validateForm($_POST, [
        'username' => ['required' => true, 'minLength' => 3],
        'email' => ['required' => true, 'email' => true],
        'age' => ['min' => 18, 'max' => 120],
    ]);

    foreach ($errors as $field => $result) {
        if ($result->isFailed()) {
            // Handle errors
            $fieldErrors = $result->getErrors();
        }
    }
}
```

### Custom Validation Rules

```php
$validator->addRule('username_available', function($value, $params, $context) {
    // Check database
    return !userExists($value);
}, 'Username is already taken');

// Use in form
->addText('username')
    ->required()
    ->add(); // Rule applied via validator
```

## 🎯 Symfony DTO Support

**Automatic validation extraction** from Symfony DTO/Entity constraints!

### DTO Example

```php
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    public ?string $username = null;

    #[Assert\Email]
    public ?string $email = null;

    #[Assert\Range(min: 18, max: 120)]
    public ?int $age = null;
}

// Setup Symfony Validator
use Symfony\Component\Validator\Validation;
use FormGenerator\V2\Validation\{SymfonyValidator, NativeValidator};

$symfonyValidator = Validation::createValidatorBuilder()
    ->enableAnnotationMapping()
    ->getValidator();

$validator = new SymfonyValidator($symfonyValidator, new NativeValidator());

// Use DTO with FormBuilder
$userDto = new UserDTO();

$form = FormBuilder::create('user_form')
    ->setValidator($validator)
    ->setDto($userDto) // Auto-extracts validation rules!

    ->addText('username')->add() // Validation from DTO!
    ->addEmail('email')->add()
    ->addNumber('age')->add()

    ->addSubmit('save')
    ->build();

// Validate against DTO
if ($_POST) {
    $errors = $form->validateDto($_POST);
    if (empty($errors)) {
        // DTO is valid, persist to database
        $entityManager->persist($userDto);
    }
}
```

### Supported Symfony Constraints

| Symfony Constraint | Mapped To |
|-------------------|-----------|
| `NotBlank`, `NotNull` | required |
| `Email` | email |
| `Length` | minLength/maxLength |
| `Range` | min/max |
| `Regex` | pattern |
| `Url` | url |
| `Type` | numeric/integer |
| `Choice` | in |

### DTO Features

- ✅ Auto-extract validation rules
- ✅ Auto-hydrate DTO from form data
- ✅ Type-safe with PHP 8+ attributes
- ✅ Works with Doctrine entities
- ✅ JavaScript validation generated
- ✅ DRY principle (define rules once)

### Complete Example

See `/Examples/V2/WithSymfonyDTO.php` and `/Examples/V2/WithValidation.php`

## 📊 Data Providers

### Array Provider

```php
use FormGenerator\V2\DataProvider\ArrayDataProvider;

$data = [
    ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
    ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
];

$provider = new ArrayDataProvider($data);
$form->setDataProvider($provider)->loadData(1);
```

### PDO Provider

```php
use FormGenerator\V2\DataProvider\PDODataProvider;

$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');
$provider = new PDODataProvider($pdo, 'users', 'id');

$form->setDataProvider($provider)->loadData($userId);
```

### Doctrine Provider

```php
use FormGenerator\V2\DataProvider\DoctrineDataProvider;

$provider = new DoctrineDataProvider($entityManager, User::class);
$form->setDataProvider($provider)->loadData($userId);
```

### Eloquent Provider

```php
use FormGenerator\V2\DataProvider\EloquentDataProvider;

$provider = new EloquentDataProvider(User::class);
$form->setDataProvider($provider)->loadData($userId);
```

## 🧪 Input Types

All HTML5 input types supported:

- `addText()` - Text input
- `addEmail()` - Email input
- `addPassword()` - Password input
- `addTextarea()` - Textarea
- `addSelect()` - Select dropdown
- `addCheckbox()` - Checkbox
- `addRadio()` - Radio buttons
- `addFile()` - File upload
- `addImage()` - Image upload
- `addHidden()` - Hidden input
- `addNumber()` - Number input
- `addDate()` - Date picker
- `addDatetime()` - DateTime picker
- `addTime()` - Time picker
- `addColor()` - Color picker
- `addRange()` - Range slider
- `addSubmit()` - Submit button
- `addReset()` - Reset button
- `addButton()` - Generic button

## 🎯 Validation

### Built-in Validation

```php
$form->addEmail('email')
    ->required()
    ->minLength(5)
    ->maxLength(100)
    ->pattern('[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$')
    ->add();

$form->addNumber('age')
    ->required()
    ->min(18)
    ->max(120)
    ->add();
```

### Dependencies

```php
// Show company fields only when user type is "business"
$form->addRadio('user_type', 'User Type')
    ->options(['personal' => 'Personal', 'business' => 'Business'])
    ->add();

$form->addText('company_name', 'Company Name')
    ->dependsOn('user_type', 'business')
    ->required()
    ->add();
```

## 📚 Documentation

- [Installation Guide](docs/installation.md)
- [Chain Pattern Guide](docs/chain-pattern.md)
- [Data Providers](docs/data-providers.md)
- [Security Features](docs/security.md)
- [Theme Development](docs/themes.md)
- [Symfony Integration](docs/symfony.md)
- [Laravel Integration](docs/laravel.md)
- [Migration from V1](UPGRADE.md)

## 🔄 Upgrading from V1

See [UPGRADE.md](UPGRADE.md) for detailed migration guide.

**Quick comparison:**

```php
// V1 (Array-based)
$config = [
    'inputs' => [
        ['type' => 'text', 'attributes' => ['name' => 'email']],
    ],
];
$form = new FormGeneratorDirector($config, 'add');

// V2 (Chain Pattern)
$form = FormBuilder::create('form')
    ->addText('email')->add()
    ->build();
```

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md).

## 📝 License

MIT License. See [LICENSE](LICENSE) for details.

## 👨‍💻 Author

**selcukmart**
- Email: admin@hostingdevi.com
- GitHub: [@selcukmart](https://github.com/selcukmart)

## 🙏 Acknowledgments

- Bootstrap team for amazing UI framework
- Twig team for excellent template engine
- Symfony & Laravel communities

---

**Version 2.0.0** - Built with ❤️ using modern PHP

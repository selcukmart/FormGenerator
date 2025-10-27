# FormGenerator V2

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Modern PHP Form Generator with Chain Pattern, Symfony & Laravel Integration

## 🎯 What's New in V2

### Core Features
- **PHP 8.1+**: Modern PHP features (enums, attributes, typed properties, readonly)
- **Chain Pattern**: Fluent interface for intuitive form building
- **Multi-Framework**: Symfony Bundle & Laravel ServiceProvider
- **Data Providers**: Doctrine, Eloquent, PDO support
- **Security First**: Built-in CSRF, XSS protection, input sanitization
- **Modern Themes**: Bootstrap 5, Tailwind CSS included
- **Template Engines**: Twig & Smarty 5 support

### Validation & Dependencies
- **🆕 Native Validation**: Built-in PHP + JavaScript validation (no jQuery!)
- **🆕 Symfony DTO Support**: Auto-extract validation from DTO/Entity
- **🆕 Dependency Management**: Pure JavaScript conditional fields
- **🆕 15+ Validation Rules**: required, email, minLength, pattern, etc.
- **🆕 Nested Dependencies**: Multi-level A→B→C dependency chains
- **🆕 Custom Animations**: Configurable fade/slide/none animations

### Advanced Components
- **🆕 Form Sections**: Organize forms with titles, descriptions, HTML content
- **🆕 Form Wizard/Stepper**: Multi-step forms with progress tracking and validation
- **🆕 Built-in Pickers**: Date, Time, DateTime, Range sliders with multi-language support
- **🆕 DateTime Picker**: Combined date+time picker with tabbed interface
- **🆕 RTL Support**: Full right-to-left language support (Arabic, Hebrew)
- **🆕 CheckboxTree**: Hierarchical checkboxes with cascade/independent modes
- **🆕 Repeater Fields**: Dynamic add/remove rows (like jquery.repeater, no jQuery!)
- **🆕 Twig Extension**: Generate forms directly in Twig templates
- **🆕 Smarty Plugin**: Generate forms directly in Smarty templates

### Developer Experience
- **🆕 PHPUnit 10+**: Comprehensive test suite with 100+ tests
- **🆕 Code Coverage**: 80%+ coverage with HTML reports
- **🆕 CONTRIBUTING.md**: Complete contribution guidelines
- **🆕 Multiple Output Formats**: HTML, JSON, XML output for different use cases

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

## 🎯 Advanced Features

### Nested Dependencies (A→B→C Chain)

Create multi-level dependency chains where field visibility cascades through multiple levels:

```php
$form = FormBuilder::create('multi-level-form')
    ->setTheme(new Bootstrap5Theme())
    
    // Level 1: Account Type
    ->addSelect('account_type', 'Account Type')
    ->options(['personal' => 'Personal', 'business' => 'Business'])
    ->isDependency()
    ->add()
    
    // Level 2: Company Name (depends on account_type)
    ->addText('company_name', 'Company Name')
    ->dependsOn('account_type', 'business')
    ->isDependency('company')
    ->add()
    
    // Level 3: Company Size (depends on company_name)
    ->addSelect('company_size', 'Company Size')
    ->options(['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'])
    ->dependsOn('company_name', 'company')
    ->add()
    
    ->build();
```

**How it works:**
- When account_type = "business" → company_name appears
- When company_name is filled → company_size appears
- Hiding a parent automatically hides all nested children

### Custom Animation Options

Control how dependent fields appear/disappear with configurable animations:

```php
$form = FormBuilder::create('animated-form')
    ->setTheme(new Bootstrap5Theme())
    
    // Configure animations
    ->setDependencyAnimation([
        'enabled' => true,
        'type' => 'slide',      // 'fade', 'slide', or 'none'
        'duration' => 400,       // milliseconds
        'easing' => 'ease-out'   // CSS easing function
    ])
    
    // Or disable animations completely
    ->disableDependencyAnimation()
    
    ->addSelect('country', 'Country')
    ->isDependency()
    ->add()
    
    ->addText('state', 'State')
    ->dependsOn('country', 'US')
    ->add()
    
    ->build();
```

**Animation Types:**
- **fade**: Opacity transition (default)
- **slide**: Height + opacity transition
- **none**: Instant show/hide (best performance)

See `Examples/V2/WithNestedDependencies.php` for complete examples.

### Form Sections

Organize your forms into logical sections with titles, descriptions, and HTML content:

```php
$form = FormBuilder::create('registration')
    ->setTheme(new Bootstrap5Theme())
    
    // Section 1: Personal Information
    ->addSection('Personal Information', 'Please provide your basic details')
    ->addText('first_name', 'First Name')->required()->add()
    ->addText('last_name', 'Last Name')->required()->add()
    ->addEmail('email', 'Email')->required()->add()
    ->endSection()
    
    // Section 2: With HTML content
    ->addSection('Terms and Conditions', 'Please review and accept')
    ->setSectionHtml('<div class="alert alert-info">
        By continuing, you agree to our <a href="/terms">Terms of Service</a>
    </div>')
    ->addCheckbox('accept_terms', 'I accept the terms')->required()->add()
    ->endSection()
    
    // Section 3: Collapsible
    ->addSection('Optional Information', 'Additional details (optional)')
    ->collapsibleSection(true) // true = collapsed by default
    ->addText('company', 'Company Name')->add()
    ->addText('website', 'Website')->add()
    ->endSection()
    
    ->addSubmit('Register')
    ->build();
```

**Section Features:**
- Title and description support
- HTML content support (alerts, links, formatting)
- Collapsible sections (Bootstrap: collapse, Tailwind: details)
- Custom CSS classes and attributes
- Nested field grouping

See `Examples/V2/WithSections.php` for complete examples.

### CheckboxTree

Hierarchical checkbox structures with two modes: cascade and independent.

```php
$permissionsTree = [
    [
        'value' => 'users',
        'label' => 'User Management',
        'children' => [
            ['value' => 'users.view', 'label' => 'View Users'],
            ['value' => 'users.create', 'label' => 'Create Users'],
            ['value' => 'users.edit', 'label' => 'Edit Users'],
        ]
    ],
    [
        'value' => 'content',
        'label' => 'Content Management',
        'children' => [
            ['value' => 'content.view', 'label' => 'View Content'],
            [
                'value' => 'content.media',
                'label' => 'Media Library',
                'children' => [
                    ['value' => 'content.media.upload', 'label' => 'Upload'],
                    ['value' => 'content.media.delete', 'label' => 'Delete'],
                ]
            ]
        ]
    ]
];

$form = FormBuilder::create('permissions')
    ->setTheme(new Bootstrap5Theme())
    
    // Cascade Mode (default)
    ->addCheckboxTree(
        'permissions',
        'User Permissions',
        $permissionsTree,
        CheckboxTreeManager::MODE_CASCADE
    )
    ->helpText('Checking a parent automatically checks all children')
    ->add()
    
    ->addSubmit('Save')
    ->build();
```

**Mode 1: Cascade (MODE_CASCADE)**
- Checking parent → All children checked
- Unchecking parent → All children unchecked
- Some children checked → Parent shows indeterminate state
- All children checked → Parent automatically checked

**Mode 2: Independent (MODE_INDEPENDENT)**
- Each checkbox is completely independent
- No parent-child synchronization
- Useful for hierarchical display without cascade behavior

**JavaScript API:**
```javascript
// Get checked values
const values = CheckboxTree_permissions.getCheckedValues();

// Set checked values programmatically
CheckboxTree_permissions.setCheckedValues(['users', 'users.view']);
```

See `Examples/V2/WithCheckboxTree.php` for complete examples.

### Repeater Fields

Dynamic add/remove field groups - like jquery.repeater but with no jQuery required!

```php
$form = FormBuilder::create('contacts')
    ->setTheme(new Bootstrap5Theme())
    
    ->addRepeater('contacts', 'Emergency Contacts', function($repeater) {
        $repeater->addText('name', 'Full Name')
            ->required()
            ->add();
        
        $repeater->addTel('phone', 'Phone Number')
            ->required()
            ->add();
        
        $repeater->addSelect('relationship', 'Relationship')
            ->options([
                'spouse' => 'Spouse',
                'parent' => 'Parent',
                'friend' => 'Friend'
            ])
            ->add();
    })
    ->minRows(1)  // Minimum 1 contact required
    ->maxRows(5)  // Maximum 5 contacts allowed
    ->add()
    
    ->addSubmit('Save')
    ->build();
```

**Features:**
- **Dynamic Rows**: Add/remove rows with smooth animations
- **Min/Max Constraints**: Enforce minimum and maximum row counts
- **Auto-numbering**: Rows automatically numbered
- **Field Management**: Automatic name/ID generation with indices
- **Button States**: Add/remove buttons disabled when limits reached
- **Pre-population**: Support for default data

**JavaScript API:**
```javascript
// Add a row programmatically
Repeater_contacts.addRow();

// Get all data as array
const data = Repeater_contacts.getData();
// Returns: [{name: 'John', phone: '555-1234', ...}, ...]

// Listen to events
document.querySelector('[data-repeater="contacts"]')
    .addEventListener('repeater:add', (e) => {
        console.log('Row added:', e.detail.index);
    });
```

**Inspired By:**
- [jquery.repeater](https://github.com/DubFriend/jquery.repeater)
- [Repeater-Field-JS](https://github.com/Brutenis/Repeater-Field-JS)
- [CodyHouse Repeater](https://codyhouse.co/ds/components/info/repeater)

**Key Difference:** Pure vanilla JavaScript - no jQuery required!

See `Examples/V2/WithRepeater.php` for complete examples.

### Form Wizard / Stepper

Multi-step form wizards with progress tracking, validation, and beautiful UI. Sections automatically become steps!

```php
$form = FormBuilder::create('registration_wizard')
    ->setTheme(new Bootstrap5Theme())

    // Enable stepper mode
    ->enableStepper([
        'layout' => StepperManager::LAYOUT_HORIZONTAL, // or LAYOUT_VERTICAL
        'mode' => StepperManager::MODE_LINEAR,          // or MODE_NON_LINEAR
        'validateOnNext' => true,                       // Validate before proceeding
        'animation' => true,                            // Smooth transitions
        'animationDuration' => 300,                     // Animation duration (ms)
    ])

    // Step 1: Personal Information (section becomes a step)
    ->addSection('Personal Info', 'Tell us about yourself')
    ->addText('first_name', 'First Name')->required()->add()
    ->addText('last_name', 'Last Name')->required()->add()
    ->addEmail('email', 'Email')->required()->add()
    ->endSection()

    // Step 2: Account Details
    ->addSection('Account Setup', 'Create your credentials')
    ->addText('username', 'Username')->required()->minLength(4)->add()
    ->addPassword('password', 'Password')->required()->minLength(8)->add()
    ->endSection()

    // Step 3: Preferences
    ->addSection('Preferences', 'Customize your experience')
    ->addCheckbox('newsletter', 'Newsletter')->options(['yes' => 'Subscribe'])->add()
    ->addTextarea('bio', 'About You')->add()
    ->endSection()

    ->build();
```

**Layout Options:**

**Horizontal Layout (LAYOUT_HORIZONTAL):**
- Steps displayed horizontally at the top
- Perfect for desktop interfaces
- Progress shown left-to-right

**Vertical Layout (LAYOUT_VERTICAL):**
- Steps displayed vertically on the left
- Great for long forms with many steps
- Sidebar-style navigation

**Mode Options:**

**Linear Mode (MODE_LINEAR):**
- Users must complete steps sequentially
- Cannot skip to future steps
- Previous steps can be revisited
- Validates each step before proceeding
- Best for guided workflows (checkout, onboarding)

**Non-Linear Mode (MODE_NON_LINEAR):**
- Users can jump to any step
- No validation on navigation
- Useful for editing existing data
- Best for flexible workflows (settings, profiles)

**JavaScript API:**

```javascript
// Access stepper instance
const stepper = window.Stepper_registration_wizard;

// Navigate
stepper.goTo(2);           // Jump to step 3 (0-indexed)
stepper.next();            // Go to next step
stepper.previous();        // Go to previous step
stepper.finish();          // Complete wizard

// Get state
stepper.getCurrentStep();  // Returns current step index
stepper.getTotalSteps();   // Returns total number of steps
stepper.getProgress();     // Returns progress percentage (0-100)

// Validation
stepper.validateStep(0);   // Validate specific step

// Mark steps
stepper.markStepCompleted(0);  // Mark step as completed
stepper.markStepError(0);      // Mark step as error
```

**Events:**

```javascript
const stepperElement = document.querySelector('[data-stepper="registration_wizard"]');

// Stepper initialized
stepperElement.addEventListener('stepper:init', (e) => {
    console.log('Total steps:', e.detail.totalSteps);
});

// Step changed
stepperElement.addEventListener('stepper:change', (e) => {
    console.log('From step:', e.detail.from);
    console.log('To step:', e.detail.to);
    // Save progress, update UI, etc.
});

// Next/Previous clicked
stepperElement.addEventListener('stepper:next', (e) => {
    console.log('Moving to step:', e.detail.step);
});

stepperElement.addEventListener('stepper:previous', (e) => {
    console.log('Moving back to step:', e.detail.step);
});

// Validation failed
stepperElement.addEventListener('stepper:validation-failed', (e) => {
    console.log('Validation failed at step:', e.detail.step);
    alert('Please fill all required fields');
});

// Wizard completed
stepperElement.addEventListener('stepper:complete', (e) => {
    console.log('Wizard completed!');
    // Submit form, show success message, redirect, etc.
});
```

**Features:**
- **Auto-Submit**: Form automatically submits on finish
- **Progress Tracking**: Real-time progress percentage
- **State Management**: Pending/Active/Completed/Error states
- **Responsive**: Works on mobile, tablet, desktop
- **Accessible**: Keyboard navigation support
- **Themeable**: Bootstrap 5 and Tailwind CSS included
- **Customizable**: Override CSS, extend functionality

**Use Cases:**
- Multi-step registration forms
- E-commerce checkout flows
- Survey/questionnaire forms
- Onboarding wizards
- Settings/configuration panels
- Application forms

**Inspired By:**
- [Metronic Stepper](https://preview.keenthemes.com/html/metronic/docs/general/stepper)

**Key Difference:** Pure vanilla JavaScript with sections-as-steps architecture!

See `Examples/V2/WithStepper.php` for complete examples.

### Built-in Pickers

Native JavaScript pickers for date, time, and range inputs with multi-language support. **Built-in pickers are enabled by default** but can be disabled if you prefer native HTML5 inputs or custom pickers.

```php
$form = FormBuilder::create('booking_form')
    ->setTheme(new Bootstrap5Theme())

    // Date picker with English locale
    ->addDate('event_date', 'Event Date')
        ->required()
        ->setPickerLocale(DatePickerManager::LOCALE_EN)
        ->setPickerOptions([
            'format' => 'yyyy-mm-dd',
            'minDate' => date('Y-m-d'),  // Min date constraint
            'maxDate' => '2025-12-31',    // Max date constraint
            'disabledDates' => ['2025-01-01'], // Disable specific dates
            'weekStart' => 0,  // 0 = Sunday, 1 = Monday
            'showToday' => true,
            'showClear' => true,
        ])
        ->add()

    // Time picker with 12-hour format
    ->addTime('event_time', 'Event Time')
        ->setPickerOptions([
            'format' => '12',  // or '24' for 24-hour
            'showSeconds' => false,
            'step' => 15,  // 15-minute intervals
        ])
        ->add()

    // Range slider (single handle)
    ->addRange('attendees', 'Number of Attendees')
        ->setPickerOptions([
            'min' => 1,
            'max' => 100,
            'value' => 10,
            'step' => 1,
            'suffix' => ' people',
            'showValue' => true,
            'showTooltip' => true,
        ])
        ->add()

    // Range slider (dual handle)
    ->addRange('price_range', 'Price Range')
        ->setPickerOptions([
            'min' => 0,
            'max' => 1000,
            'from' => 200,  // Start value
            'to' => 800,    // End value
            'dual' => true, // Enable dual handles
            'prefix' => '$',
            'step' => 10,
        ])
        ->add()

    ->addSubmit('Book')
    ->build();
```

**Date Picker Features:**
- **Multi-language support**: English, Turkish, German, French, Spanish + custom locales
- **Date formats**: yyyy-mm-dd, dd-mm-yyyy, mm-dd-yyyy
- **Constraints**: Min/max dates, disabled dates
- **Calendar display**: Configurable week start day
- **Actions**: Today, Clear buttons
- **Inline or popup mode**

**Time Picker Features:**
- **Formats**: 12-hour (AM/PM) or 24-hour
- **Precision**: Show/hide seconds
- **Intervals**: Custom step for minutes/seconds
- **Constraints**: Min/max time
- **Multi-language**: Customizable labels

**Range Slider Features:**
- **Single or dual handles**: One value or min/max range
- **Custom formatting**: Prefix ($, €) and suffix (%, kg, etc.)
- **Constraints**: Min/max values, step intervals
- **Visual feedback**: Tooltips, value display
- **Orientation**: Horizontal or vertical
- **Keyboard navigation**: Arrow keys supported

**Built-in Locales:**

```php
// English (default)
DatePickerManager::LOCALE_EN
TimePickerManager::LOCALE_EN
RangeSliderManager::LOCALE_EN

// Turkish
DatePickerManager::LOCALE_TR
TimePickerManager::LOCALE_TR
RangeSliderManager::LOCALE_TR

// German
DatePickerManager::LOCALE_DE
TimePickerManager::LOCALE_DE
RangeSliderManager::LOCALE_DE

// French
DatePickerManager::LOCALE_FR
TimePickerManager::LOCALE_FR
RangeSliderManager::LOCALE_FR

// Spanish
DatePickerManager::LOCALE_ES
TimePickerManager::LOCALE_ES
RangeSliderManager::LOCALE_ES
```

**Custom Locale Example:**

```php
$customLocale = [
    'months' => ['Janvier', 'Février', ...],
    'monthsShort' => ['Jan', 'Fév', ...],
    'weekdays' => ['Dimanche', 'Lundi', ...],
    'weekdaysShort' => ['Dim', 'Lun', ...],
    'weekdaysMin' => ['Di', 'Lu', ...],
    'today' => 'Aujourd\'hui',
    'clear' => 'Effacer',
    'close' => 'Fermer',
];

$form->addDate('date_field', 'Date')
    ->setPickerLocale($customLocale)
    ->add();
```

**Disable Built-in Picker (Use Native HTML5):**

```php
// Use native HTML5 date input instead of custom picker
$form->addDate('native_date', 'Date')
    ->disablePicker()  // Disable built-in picker
    ->add();

// Use native HTML5 time input
$form->addTime('native_time', 'Time')
    ->disablePicker()
    ->add();

// Use native HTML5 range input
$form->addRange('native_range', 'Range')
    ->disablePicker()
    ->attributes(['min' => '0', 'max' => '100'])
    ->add();
```

**JavaScript API:**

```javascript
// Date Picker
const datePicker = window.DatePicker_event_date;
datePicker.selectDate('2025-12-25');
datePicker.selectToday();
datePicker.clear();
datePicker.show();
datePicker.hide();

// Time Picker
const timePicker = window.TimePicker_event_time;
timePicker.setNow();
timePicker.clear();
timePicker.updateInput();

// Range Slider
const rangeSlider = window.RangeSlider_price_range;
rangeSlider.setValue(50);  // Single handle
rangeSlider.setValue({from: 200, to: 800});  // Dual handle
const values = rangeSlider.getValues();
```

**Events:**

```javascript
// Date picker events
document.getElementById('event_date').addEventListener('datepicker:change', (e) => {
    console.log('Date:', e.detail.date, 'Value:', e.detail.value);
});

document.getElementById('event_date').addEventListener('datepicker:open', (e) => {
    console.log('Calendar opened');
});

document.getElementById('event_date').addEventListener('datepicker:close', (e) => {
    console.log('Calendar closed');
});

// Time picker events
document.getElementById('event_time').addEventListener('timepicker:change', (e) => {
    console.log('Time:', e.detail);
});

// Range slider events
document.getElementById('price_range').addEventListener('rangeslider:change', (e) => {
    console.log('Range:', e.detail);
});
```

**Turkish Example:**

```php
$form = FormBuilder::create('turkish_form')
    ->setTheme(new Bootstrap5Theme())

    ->addDate('dogum_tarihi', 'Doğum Tarihi')
        ->setPickerLocale(DatePickerManager::LOCALE_TR)
        ->setPickerOptions([
            'format' => 'dd-mm-yyyy',
            'weekStart' => 1,  // Pazartesi
        ])
        ->add()

    ->addTime('randevu_saati', 'Randevu Saati')
        ->setPickerLocale(TimePickerManager::LOCALE_TR)
        ->setPickerOptions([
            'format' => '24',
        ])
        ->add()

    ->addRange('fiyat', 'Fiyat Aralığı')
        ->setPickerOptions([
            'min' => 0,
            'max' => 10000,
            'from' => 1000,
            'to' => 5000,
            'dual' => true,
            'prefix' => '₺',
            'locale' => RangeSliderManager::LOCALE_TR,
        ])
        ->add()

    ->addSubmit('kaydet', 'Kaydet')
    ->build();
```

**Key Features:**
- **Built-in by Default**: Pickers automatically enabled for date/time/range inputs
- **Opt-out Available**: Use `disablePicker()` to use native inputs or custom pickers
- **Pure Vanilla JS**: No jQuery or external dependencies
- **Multi-language**: 5 built-in locales + custom locale support
- **Fully Customizable**: Extensive options for each picker type
- **Accessible**: Keyboard navigation, ARIA support
- **Responsive**: Works on mobile, tablet, desktop
- **Themeable**: Compatible with Bootstrap 5 and Tailwind CSS

See `Examples/V2/WithPickers.php` for complete examples.

### DateTime Picker

Combined date and time picker with tabbed interface for seamless datetime selection.

```php
$form = FormBuilder::create('meeting_form')
    ->setTheme(new Bootstrap5Theme())

    // DateTime picker with 12-hour format
    ->addDatetime('meeting_datetime', 'Meeting Date & Time')
        ->required()
        ->setPickerLocale(DateTimePickerManager::LOCALE_EN)
        ->setPickerOptions([
            'timeFormat' => '12',  // 12-hour with AM/PM
            'showSeconds' => false,
            'minDateTime' => date('Y-m-d H:i:s'),
        ])
        ->add()

    // DateTime picker with 24-hour format
    ->addDatetime('appointment', 'Appointment')
        ->setPickerOptions([
            'timeFormat' => '24',  // 24-hour format
            'showSeconds' => true,
        ])
        ->add()

    ->addSubmit('Schedule')
    ->build();
```

**Features:**
- **Tabbed Interface**: Separate tabs for date and time selection
- **Combined Selection**: Select both date and time in one picker
- **All Date Features**: Calendar navigation, min/max dates, disabled dates
- **All Time Features**: Hour/minute/second selection, 12/24 hour formats
- **Multi-language**: Supports all picker locales (EN, TR, DE, FR, ES, AR, HE)
- **RTL Support**: Full right-to-left language support
- **Now Button**: Set current date and time instantly
- **Clear Button**: Reset both date and time

**Available Locales:**
```php
DateTimePickerManager::LOCALE_EN  // English
DateTimePickerManager::LOCALE_TR  // Turkish
DateTimePickerManager::LOCALE_AR  // Arabic (RTL)
DateTimePickerManager::LOCALE_HE  // Hebrew (RTL)
```

### RTL (Right-to-Left) Support

Full support for right-to-left languages like Arabic and Hebrew.

```php
$form = FormBuilder::create('arabic_form')
    ->setTheme(new Bootstrap5Theme())

    // Arabic date picker
    ->addDate('birth_date', 'تاريخ الميلاد')
        ->setPickerLocale(DatePickerManager::LOCALE_AR)
        ->setPickerOptions([
            'rtl' => true,          // Enable RTL
            'weekStart' => 6,       // Saturday
            'format' => 'dd-mm-yyyy',
        ])
        ->add()

    // Arabic time picker
    ->addTime('appointment_time', 'وقت الموعد')
        ->setPickerLocale(TimePickerManager::LOCALE_AR)
        ->setPickerOptions([
            'rtl' => true,
            'format' => '12',
        ])
        ->add()

    // Arabic datetime picker
    ->addDatetime('meeting_datetime', 'تاريخ ووقت الاجتماع')
        ->setPickerLocale(DateTimePickerManager::LOCALE_AR)
        ->setPickerOptions([
            'rtl' => true,
            'timeFormat' => '24',
        ])
        ->add()

    ->addSubmit('حفظ', 'حفظ')
    ->build();
```

**Hebrew Example:**
```php
$form = FormBuilder::create('hebrew_form')
    ->setTheme(new Bootstrap5Theme())

    ->addDate('date_field', 'תאריך')
        ->setPickerLocale(DatePickerManager::LOCALE_HE)
        ->setPickerOptions([
            'rtl' => true,
            'weekStart' => 0,  // Sunday (common in Israel)
        ])
        ->add()

    ->addTime('time_field', 'שעה')
        ->setPickerLocale(TimePickerManager::LOCALE_HE)
        ->setPickerOptions(['rtl' => true])
        ->add()

    ->addSubmit('save', 'שמור')
    ->build();
```

**RTL Features:**
- **Automatic Direction**: Input fields get `dir="rtl"` attribute
- **Layout Adjustment**: Picker popups align correctly for RTL
- **CSS Support**: All pickers have RTL-specific styling
- **Flex Direction**: Navigation buttons flip for RTL
- **Built-in Locales**: Arabic and Hebrew locales included
- **Custom Locales**: Easy to add more RTL languages

**Supported in All Pickers:**
- Date Picker (`addDate()`)
- Time Picker (`addTime()`)
- DateTime Picker (`addDatetime()`)
- Range Slider (`addRange()`)

### Multiple Output Formats

Generate forms in HTML, JSON, or XML format for different use cases.

```php
use FormGenerator\V2\Contracts\OutputFormat;

$form = FormBuilder::create('user_form')
    ->setRenderer($renderer)
    ->setTheme($theme)
    ->addText('name', 'Name')->required()->add()
    ->addEmail('email', 'Email')->required()->add()
    ->addSelect('country', 'Country')->options([
        'us' => 'United States',
        'uk' => 'United Kingdom',
    ])->add()
    ->addSubmit('save', 'Save');

// HTML output (default)
$html = $form->build();
$html = $form->build(OutputFormat::HTML);
$html = $form->buildAsHtml();

// JSON output (for APIs)
$json = $form->build(OutputFormat::JSON);
$json = $form->buildAsJson();
$json = $form->buildAsJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// XML output (for exports)
$xml = $form->build(OutputFormat::XML);
$xml = $form->buildAsXml();
```

**HTML Output** (Default):
```html
<form name="user_form" method="POST" action="/api/users">
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <!-- ... more fields ... -->
    <button type="submit" class="btn btn-primary">Save</button>
</form>
<script>/* validation & picker scripts */</script>
```

**JSON Output** (API Integration):
```json
{
    "name": "user_form",
    "method": "POST",
    "action": "/api/users",
    "scope": "add",
    "csrf_enabled": true,
    "validation_enabled": true,
    "inputs": [
        {
            "name": "name",
            "type": "text",
            "label": "Name",
            "required": true,
            "validationRules": [
                {"type": "required"}
            ]
        }
    ],
    "validation_rules": {
        "name": [{"type": "required"}],
        "email": [{"type": "required"}, {"type": "email"}]
    }
}
```

**XML Output** (Data Export):
```xml
<?xml version="1.0" encoding="UTF-8"?>
<form name="user_form" method="POST" action="/api/users" scope="add">
    <settings>
        <csrf_enabled>true</csrf_enabled>
        <validation_enabled>true</validation_enabled>
    </settings>
    <inputs>
        <input name="name" type="text" required="true">
            <label>Name</label>
            <validation_rules>
                <rule type="required"/>
            </validation_rules>
        </input>
    </inputs>
</form>
```

**Use Cases:**

**JSON Format:**
- REST API form schemas
- Frontend framework integration (React, Vue, Angular)
- Form configuration storage
- Dynamic form generation
- Mobile app integration

**XML Format:**
- SOAP API integration
- Legacy system integration
- Data export/import
- Configuration files
- Enterprise systems integration

**HTML Format:**
- Traditional web applications
- Server-side rendering
- Direct browser rendering

**Backward Compatibility:**
- `build()` without parameters returns HTML (default behavior)
- All existing code continues to work without changes

## 🎨 Template Engine Integration

### Twig Extension

Generate forms directly in Twig templates without controller code:

**Setup:**
```php
use FormGenerator\V2\Integration\Twig\FormGeneratorExtension;
use FormGenerator\V2\Renderer\TwigRenderer;
use FormGenerator\V2\Theme\Bootstrap5Theme;

$renderer = new TwigRenderer(__DIR__ . '/templates');
$theme = new Bootstrap5Theme();

$twig->addExtension(new FormGeneratorExtension($renderer, $theme));
```

**Usage in Twig:**
```twig
{# registration.twig #}
{{ form_start('registration', {'action': '/register', 'method': 'POST'}) }}

{{ form_text('username', 'Username', {'required': true, 'placeholder': 'Enter username'}) }}
{{ form_email('email', 'Email Address', {'required': true}) }}
{{ form_password('password', 'Password', {'required': true, 'minLength': 8}) }}

{{ form_select('country', 'Country', {'us': 'United States', 'uk': 'United Kingdom'}) }}

{{ form_checkbox('newsletter', 'Subscribe to newsletter') }}

{{ form_submit('Create Account') }}

{{ form_end() }}
```

**Available Functions:**
- `form_start()` / `form_end()` - Form wrapper
- `form_text()`, `form_email()`, `form_password()` - Text inputs
- `form_textarea()`, `form_number()`, `form_date()` - Other inputs
- `form_select()`, `form_checkbox()` - Options
- `form_submit()` - Submit button

### Smarty Plugin

Generate forms directly in Smarty templates:

**Setup:**
```php
use FormGenerator\V2\Integration\Smarty\FormGeneratorPlugin;
use FormGenerator\V2\Renderer\SmartyRenderer;
use FormGenerator\V2\Theme\Bootstrap5Theme;

$renderer = new SmartyRenderer(__DIR__ . '/templates');
$theme = new Bootstrap5Theme();

FormGeneratorPlugin::setRenderer($renderer);
FormGeneratorPlugin::setDefaultTheme($theme);

// Register plugins
$smarty->registerPlugin('function', 'form_start', ['FormGeneratorPlugin', 'formStart']);
$smarty->registerPlugin('function', 'form_text', ['FormGeneratorPlugin', 'formText']);
$smarty->registerPlugin('function', 'form_email', ['FormGeneratorPlugin', 'formEmail']);
$smarty->registerPlugin('function', 'form_submit', ['FormGeneratorPlugin', 'formSubmit']);
$smarty->registerPlugin('function', 'form_end', ['FormGeneratorPlugin', 'formEnd']);
```

**Usage in Smarty:**
```smarty
{* registration.tpl *}
{form_start name="registration" action="/register" method="POST"}

{form_text name="username" label="Username" required=true placeholder="Enter username"}
{form_email name="email" label="Email Address" required=true}
{form_password name="password" label="Password" required=true}

{form_select name="country" label="Country" options=$countryOptions}

{form_checkbox name="newsletter" label="Subscribe to newsletter"}

{form_submit label="Create Account"}

{form_end}
```

**Benefits:**
- ✅ No controller code needed
- ✅ Forms defined directly in templates
- ✅ Automatic theme and renderer injection
- ✅ Clean, readable template syntax

## 🧪 Testing

### PHPUnit 10+ Test Suite

Comprehensive test coverage with PHPUnit 10+:

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test suite
vendor/bin/phpunit --testsuite Unit

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/html

# Generate coverage report
./generate-coverage.sh
```

**Test Structure:**
```
tests/
├── bootstrap.php          # Test bootstrap
├── TestCase.php           # Base test class
├── Unit/                  # Unit tests
│   ├── Builder/
│   │   ├── FormBuilderTest.php
│   │   ├── InputBuilderTest.php
│   │   └── DependencyManagerTest.php
│   ├── Validation/
│   │   └── NativeValidatorTest.php
│   └── DataProvider/
├── Integration/           # Integration tests
└── Feature/              # Feature tests
```

**Test Coverage:**
- FormBuilder: 25+ tests
- InputBuilder: 20+ tests
- NativeValidator: 30+ tests (all 15 rules)
- DependencyManager: JavaScript generation tests
- Data Providers: Doctrine, Eloquent, PDO, Array

**Code Coverage Target:** 80%+

See `CONTRIBUTING.md` for testing guidelines.

## 📊 Code Coverage

View code coverage reports:

```bash
# Generate HTML coverage report
vendor/bin/phpunit --coverage-html coverage/html

# Open in browser
open coverage/html/index.html

# Or use the convenience script
./generate-coverage.sh
```

Coverage configuration is in `phpunit.xml`:
- HTML report: `coverage/html/`
- Text report: `coverage/coverage.txt`
- Clover XML: `coverage/clover.xml`


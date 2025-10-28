# FormGenerator

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-3.0.0-blue.svg)](CHANGELOG.md)
[![Tests](https://img.shields.io/badge/tests-500%2B-success.svg)](tests/)

**Modern PHP Form Generator - Now a Complete Symfony Form Component Alternative!**

A production-ready, feature-complete form builder with Symfony parity, offering nested forms, type system, cross-field validation, dynamic form modification, advanced error handling, i18n, and automatic CSRF protection.

---

## 🎉 Version 3.0.0 - Production Ready!

**FormGenerator V3.0.0** achieves **100% feature parity** with Symfony Form Component while maintaining a simpler, more intuitive API.

### 🚀 What Makes v3.0.0 Special?

✨ **Complete Symfony Alternative**
- All major Symfony Form features implemented
- Simpler API with chain pattern
- Zero Symfony dependencies (standalone or integrate)
- **500+ comprehensive unit tests**

🌍 **Internationalization (NEW in v3.0.0)**
- Multi-language form labels and messages
- Built-in translator with PHP and YAML loaders
- Parameter interpolation (`{{ param }}` syntax)
- Locale fallback chains

🔒 **Automatic CSRF Protection (NEW in v3.0.0)**
- Session-based token management
- Automatic token generation and validation
- Configurable token lifetime (default: 2 hours)
- Zero configuration required

### 📖 [Read Full V3 Documentation →](README_V3.md)

### Quick Start - Simple Contact Form with i18n & CSRF

```php
use FormGenerator\V2\Builder\FormBuilder;
use FormGenerator\V2\Translation\FormTranslator;
use FormGenerator\V2\Translation\Loader\PhpLoader;

// Setup translator (optional)
$translator = new FormTranslator('en_US');
$translator->addLoader('php', new PhpLoader());
$translator->loadTranslationFile(__DIR__ . '/translations/forms.en_US.php', 'en_US', 'php');

FormBuilder::setTranslator($translator);

// Build form with CSRF protection (automatic!)
$form = FormBuilder::create('contact_form')
    ->setAction('/contact/send')
    ->setMethod('POST')
    ->setCsrfTokenId('contact_form') // Automatic CSRF protection

    ->addText('name', 'form.label.name') // Translated automatically
        ->required()
        ->minLength(3)
        ->add()

    ->addEmail('email', 'form.label.email')
        ->required()
        ->add()

    ->addTextarea('message', 'form.label.message')
        ->required()
        ->minLength(20)
        ->add()

    ->addSubmit('send', 'form.button.send')
    ->build();

// Render with automatic CSRF token
echo $form;
```

### Nested Forms with Cross-Field Validation

```php
use FormGenerator\V2\Builder\FormBuilder;
use FormGenerator\V2\Form\Form;
use FormGenerator\V2\Validation\Constraints\Callback;

// Create address sub-form
$addressForm = new Form('address');
$addressForm->add('street', FormBuilder::text('street', 'Street'));
$addressForm->add('city', FormBuilder::text('city', 'City'));
$addressForm->add('zipcode', FormBuilder::text('zipcode', 'ZIP Code'));

// Create user form with nested address
$form = FormBuilder::create('user_registration')
    ->setCsrfTokenId('register')

    ->addText('username', 'Username')
        ->required()
        ->minLength(3)
        ->add()

    ->addPassword('password', 'Password')
        ->required()
        ->minLength(8)
        ->add()

    ->addPassword('password_confirm', 'Confirm Password')
        ->required()
        ->add()

    ->add('address', $addressForm) // Nested form!

    ->build();

// Add cross-field validation
$form->addConstraint(new Callback(function($data, $context) {
    if ($data['password'] !== $data['password_confirm']) {
        $context->buildViolation('Passwords do not match')
                ->atPath('password_confirm')
                ->addViolation();
    }
}));

// Validate and handle errors
$form->submit($_POST);
if ($form->isValid()) {
    // Process data
} else {
    $errors = $form->getErrorList(deep: true);
}
```

### Dynamic Form Modification with Events

```php
use FormGenerator\V2\Builder\FormBuilder;
use FormGenerator\V2\Event\FormEvents;

$form = FormBuilder::create('product_form')
    ->setCsrfTokenId('product')

    ->addSelect('product_type', 'Product Type')
        ->options([
            'physical' => 'Physical Product',
            'digital' => 'Digital Product',
        ])
        ->add()

    ->build();

// Add fields dynamically based on product type
$form->addEventListener(FormEvents::PRE_SET_DATA, function($event) {
    $data = $event->getData();
    $form = $event->getForm();

    if ($data['product_type'] === 'physical') {
        // Add shipping fields
        $form->add('weight', FormBuilder::number('weight', 'Weight (kg)'));
        $form->add('dimensions', FormBuilder::text('dimensions', 'Dimensions'));
    } else {
        // Add download fields
        $form->add('file_size', FormBuilder::text('file_size', 'File Size'));
        $form->add('download_limit', FormBuilder::number('download_limit', 'Downloads'));
    }
});
```

### 📊 Complete Feature Set

**v3.0.0 - Internationalization & CSRF (Current)**
- 🌍 Multi-language support with built-in translator
- 🔒 Automatic CSRF protection
- 📝 Multiple translation loaders (PHP, YAML)
- 🔄 Parameter interpolation in messages

**v2.9.0 - Advanced Error Handling**
- ⚠️ Three error levels (ERROR, WARNING, INFO)
- 🎯 Error bubbling from nested forms
- 📋 Rich error metadata
- 🔍 Error filtering and grouping

**v2.8.0 - Dynamic Form Modification**
- 🎭 Event-driven form building
- 🔄 Modify forms based on data
- ➕ Add/remove fields dynamically
- 🎬 Full event lifecycle

**v2.7.0 - Cross-Field Validation**
- ✅ Field relationship validation
- 🎯 Validation groups
- 📝 Custom callback constraints
- 🔧 Execution context

**v2.5.0 - Type System**
- 🏗️ Custom field types
- 🎨 Type inheritance
- 🔌 Type extensions
- ⚙️ Options resolver

**v2.4.0 - Nested Forms**
- 🌳 Unlimited nesting
- 🔁 Form collections
- 👨‍👩‍👧 Parent-child relationships
- 🗺️ Data mapping

**Earlier Features**
- 🔄 Data transformation (v2.3.1)
- 🎯 Event-driven dependencies (v2.3.0)
- ✅ Laravel-style validation (v2.1.0)
- 🎨 Bootstrap 5 & Tailwind themes
- 🔐 CSRF, XSS protection
- 🚀 Symfony & Laravel integration

---

## 📦 Installation

```bash
composer require selcukmart/form-generator
```

---

## 🧪 Testing

**500+ Comprehensive Unit Tests**

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test suite
vendor/bin/phpunit tests/V2/Type/          # Type system tests
vendor/bin/phpunit tests/V2/Validation/    # Validation tests
vendor/bin/phpunit tests/V2/Event/         # Event system tests
vendor/bin/phpunit tests/V2/Error/         # Error handling tests
vendor/bin/phpunit tests/V2/Translation/   # i18n tests
vendor/bin/phpunit tests/V2/Security/      # CSRF tests

# With coverage
vendor/bin/phpunit --coverage-html coverage/
```

**Test Coverage:**
- ✅ 400+ unit tests
- ✅ 100+ integration tests
- ✅ Full API coverage
- ✅ Edge case handling
- ✅ Real-world scenarios

---

## 🔄 Migration from Symfony Forms

### Before (Symfony Form Component)

```php
$form = $this->createFormBuilder()
    ->add('username', TextType::class, [
        'required' => true,
        'constraints' => [new Length(['min' => 3])]
    ])
    ->add('email', EmailType::class)
    ->getForm();
```

### After (FormGenerator)

```php
$form = FormBuilder::create('user_form')
    ->addText('username')->required()->minLength(3)->add()
    ->addEmail('email')->required()->add()
    ->build();
```

**Advantages:**
- 🎯 Chain pattern is more readable
- 🚀 No need for type classes
- 🔧 Less boilerplate code
- 💡 Better IDE autocomplete
- 🎨 Cleaner syntax

See [SYMFONY_ALTERNATIVE_ROADMAP.md](SYMFONY_ALTERNATIVE_ROADMAP.md) for complete migration guide.

---

## 📚 Complete Documentation

### Core Documentation
- **[Complete V3 Documentation](README_V3.md)** - Full feature guide ⭐
- **[V2 Documentation](README_V2.md)** - Legacy V2 documentation
- **[Symfony Alternative Guide](SYMFONY_ALTERNATIVE_ROADMAP.md)** - Migration from Symfony Forms
- **[Upgrade Guide](UPGRADE.md)** - Version migration guides
- **[Changelog](CHANGELOG.md)** - Version history
- **[Contributing](CONTRIBUTING.md)** - Contribution guidelines

### Examples by Version

**🌍 v3.0.0 - Internationalization & CSRF (NEW!)**
- [Internationalization Example](/Examples/V2/WithI18nAndCsrf.php)
- [Multi-language Forms](/Examples/V2/WithI18nAndCsrf.php)
- [Auto CSRF Protection](/Examples/V2/WithI18nAndCsrf.php)

**⚠️ v2.9.0 - Error Handling**
- [Advanced Error Handling](/Examples/V2/WithAdvancedErrorHandling.php)
- [Error Levels & Filtering](/Examples/V2/WithAdvancedErrorHandling.php)
- [Error Bubbling](/Examples/V2/WithAdvancedErrorHandling.php)

**🎭 v2.8.0 - Dynamic Forms**
- [Dynamic Form Modification](/Examples/V2/WithDynamicFormModification.php)
- [Event-based Form Building](/Examples/V2/WithDynamicFormModification.php)

**✅ v2.7.0 - Cross-Field Validation**
- [Callback Validation](/Examples/V2/WithCallbackValidation.php)
- [Validation Groups](/Examples/V2/WithValidationGroups.php)
- [Cross-Field Rules](/Examples/V2/WithCallbackValidation.php)

**🏗️ v2.5.0 - Type System**
- [Custom Types](/Examples/V2/WithCustomTypes.php)
- [Type Extensions](/Examples/V2/WithTypeExtensions.php)
- [Options Resolver](/Examples/V2/WithCustomTypes.php)

**🌳 v2.4.0 - Nested Forms**
- [Nested Forms](/Examples/V2/WithNestedForms.php)
- [Form Collections](/Examples/V2/WithFormCollections.php)
- [Parent-Child Forms](/Examples/V2/WithNestedForms.php)

**Earlier Features**
- [Data Transformation](/Examples/V2/WithDataTransformation.php) (v2.3.1)
- [Event-Driven Dependencies](/Examples/V2/WithEventDrivenDependencies.php) (v2.3.0)
- [Laravel-Style Validation](/Examples/V2/WithValidation.php) (v2.1.0)
- [Form Wizard/Stepper](/Examples/V2/WithStepper.php)
- [Built-in Pickers](/Examples/V2/WithPickers.php)
- [Doctrine Integration](/Examples/V2/WithDoctrine.php)
- [Laravel Integration](/Examples/V2/WithLaravel.php)

---

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

**Areas for contribution:**
- Additional field types
- More translation loaders
- Framework integrations
- Theme development
- Documentation improvements

---

## 📝 Version History

- **v3.0.0** (2025) - i18n & Auto CSRF - **Current** ⭐
- **v2.9.0** (2025) - Advanced Error Handling & Bubbling
- **v2.8.0** (2025) - Dynamic Form Modification
- **v2.7.0** (2024) - Cross-Field Validation & Groups
- **v2.5.0** (2024) - Type System & Extensions
- **v2.4.0** (2024) - Nested Forms & Collections
- **v2.3.1** (2024) - Data Transformation
- **v2.3.0** (2024) - Event-Driven Dependencies
- **v2.1.0** (2024) - Laravel-Style Validation
- **v2.0.0** (2024) - Complete rewrite with chain pattern

See [CHANGELOG.md](CHANGELOG.md) for details.

---

## 📄 License

MIT License - see [LICENSE](LICENSE) for details.

---

## 👨‍💻 Author

**selcukmart**
- Email: admin@hostingdevi.com
- GitHub: [@selcukmart](https://github.com/selcukmart)

---

## 🌟 Star History

If this project helped you, please give it a ⭐ on GitHub!

---

**Made with ❤️ using modern PHP 8.1+**

**🎯 Production-ready • 🚀 Symfony Alternative • 🔒 Secure by Default • 🌍 Internationalized**

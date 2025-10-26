# FormGenerator

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Modern PHP Form Generator with Chain Pattern, Symfony & Laravel Integration

---

## 🎉 Version 2.0 is Here!

**FormGenerator V2** is a complete rewrite with modern PHP 8.1+ features, chain pattern fluent interface, and much more!

### 📖 [Read Full V2 Documentation →](README_V2.md)

### Quick V2 Example

```php
use FormGenerator\V2\Builder\FormBuilder;

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
        ->controls('user_type')
        ->add()
    
    ->addText('company_name', 'Company Name')
        ->dependsOn('user_type', 'business') // Auto show/hide!
        ->add()
    
    ->addSubmit('save')
    ->build();

echo $form; // That's it!
```

### What's New in V2?

- ✅ **PHP 8.1+** with modern features (enums, attributes, readonly)
- ✅ **Chain Pattern** fluent interface
- ✅ **Dependency Management** with pure JavaScript (no jQuery!)
- ✅ **Symfony & Laravel** integration out of the box
- ✅ **Multiple Data Sources**: Doctrine, Eloquent, PDO, Array
- ✅ **Twig & Smarty 5** support
- ✅ **Bootstrap 5** theme included
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
  - [With Doctrine](/Examples/V2/WithDoctrine.php)
  - [With Laravel](/Examples/V2/WithLaravel.php)
  - [Dependency Management](/Examples/V2/WithDependencies.php) ⭐ New!

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

# Symfony Form Component vs FormGenerator - Feature Comparison

## Mevcut Özellikler ✅

### FormGenerator'da Zaten Var
- ✅ **Fluent API / Chain Pattern**: FormBuilder ile zincirleme yapı
- ✅ **Data Binding**: Doctrine, Eloquent, PDO veri kaynakları
- ✅ **Validation**: Laravel tarzı 25+ validation rule
- ✅ **Theming**: Bootstrap 5, Tailwind CSS
- ✅ **Template Engines**: Twig, Smarty, Blade
- ✅ **Events**: Field-level ve form-level event system
- ✅ **CSRF Protection**: Güvenlik yönetimi
- ✅ **Input Types**: Temel tüm input türleri
- ✅ **Dependencies**: Conditional rendering, nested dependencies
- ✅ **Advanced Components**: Repeater, CheckboxTree, Form Wizard

## Eksik Kritik Özellikler ⚠️

### Priority 1 - CRITICAL (v2.4.0)
1. **❌ Data Transformers**: View <-> Model veri dönüşümleri
   - Örnek: "2024-10-27" (form) <-> DateTime object (model)
   - ViewTransformers: Model -> View
   - ModelTransformers: View -> Model
   - İsim: `DataTransformerInterface`, `DataTransformerManager`

2. **❌ Form Options System**: Symfony-style form options
   - Configurable form behavior
   - Inherit/override options
   - Type-specific options
   - İsim: `FormOptions`, `FormOptionsResolver`

3. **❌ Form Request Handler**: Form submission handling
   - handleRequest() method
   - isSubmitted() check
   - isValid() check
   - Automatic data binding on submit
   - İsim: `FormRequestHandler`

### Priority 2 - HIGH (v2.5.0)
4. **❌ Compound Form Types**: İç içe form tipleri
   - Nested forms (Address, Contact)
   - Collection types (dynamic arrays)
   - Embedded forms
   - İsim: `CompoundType`, `CollectionType`

5. **❌ Choice Type System**: Gelişmiş seçim yönetimi
   - ChoiceType (select, radio, checkbox)
   - EntityType (database-backed choices)
   - Choice loaders (lazy loading)
   - Choice groups (optgroup)
   - İsim: `ChoiceType`, `EntityType`, `ChoiceLoaderInterface`

6. **❌ Form Extensions**: Genişletme sistemi
   - FormTypeExtensions
   - Custom type builders
   - Type decorators
   - İsim: `FormTypeExtensionInterface`, `ExtensionManager`

### Priority 3 - MEDIUM (v2.6.0)
7. **❌ Guess Type System**: Otomatik tip tahmin
   - Doctrine metadata'dan tip çıkarma
   - Validation constraint'den tip çıkarma
   - Auto-generate forms from Entity
   - İsim: `FormTypeGuesser`, `DoctrineTypeGuesser`

8. **❌ Property Path System**: Nested data access
   - `user.address.city` gibi deep path
   - Array access `data[0][name]`
   - Property accessor
   - İsim: `PropertyAccessor`, `PropertyPath`

9. **❌ Form Inheritance**: Form tipleri inheritance
   - BaseType extending
   - Parent form configuration
   - Type hierarchy
   - İsim: `AbstractType` improvements

10. **❌ Constraint Mapping**: Symfony Validator integration
    - Symfony Constraints support
    - Auto-validate from annotations/attributes
    - Group validation
    - İsim: `ConstraintMapper`, `ValidationGroupManager`

### Priority 4 - LOW (v2.7.0)
11. **❌ Form Factory System**: Advanced form creation
    - FormFactory pattern
    - Type registry
    - Builder factory
    - İsim: `FormFactory`, `FormRegistry`

12. **❌ Form Views**: Separate view layer
    - FormView objects
    - View variables
    - Render context
    - İsim: `FormView`, `FormRenderer`

13. **❌ Button Types**: Submit variants
    - SubmitType
    - ButtonType
    - ResetType
    - İsim: Button variants in InputType

14. **❌ File Upload Advanced**: Gelişmiş dosya yönetimi
    - Multiple files
    - File validators (size, mime)
    - Upload handling
    - İsim: `FileType` improvements

15. **❌ Translation Integration**: i18n desteği
    - Form labels translation
    - Error messages translation
    - Translation domains
    - İsim: `TranslatorInterface` integration

## Önerilen Yol Haritası

### v2.4.0 - Data Transformation (EN KRİTİK)
- Data Transformers (ViewTransformers, ModelTransformers)
- Form Options System
- Form Request Handler (handleRequest, isSubmitted, isValid)

**Sebep**: Bu üç özellik Symfony Form'un temel mimarisidir. Data transformers olmadan karmaşık veri tipleri kullanılamaz.

### v2.5.0 - Form Type System
- Compound Form Types
- Choice Type System improvements
- Form Extensions

**Sebep**: Form type sistemi Symfony'nin gücünün kaynağıdır. Reusable form components.

### v2.6.0 - Smart Features
- Guess Type System (auto-generate)
- Property Path System
- Constraint Mapping
- Form Inheritance

**Sebep**: Developer experience iyileştirmeleri. Otomatik form generation.

### v2.7.0 - Polish & Advanced
- Form Factory System
- Form Views
- Button Types
- File Upload Advanced
- Translation Integration

**Sebep**: Production-ready, enterprise-level features.

## İlk Adım: v2.4.0 Detay Planı

### 1. Data Transformers (3-4 gün)
```php
interface DataTransformerInterface {
    public function transform(mixed $data): mixed;      // Model -> View
    public function reverseTransform(mixed $data): mixed; // View -> Model
}

// Built-in transformers
- DateTimeToStringTransformer
- BooleanToStringTransformer
- NumberToLocalizedStringTransformer
- ArrayToStringTransformer

// Usage
->addDate('birth_date', 'Birth Date')
    ->addModelTransformer(new DateTimeToStringTransformer('Y-m-d'))
    ->add()
```

### 2. Form Options System (2-3 gün)
```php
class FormOptions {
    public function __construct(
        public bool $mapped = true,
        public bool $required = false,
        public bool $disabled = false,
        public ?string $label = null,
        public mixed $data = null,
        public array $attr = [],
        // ... more options
    )
}

// Usage
->addText('name', options: [
    'required' => true,
    'label' => 'User Name',
    'attr' => ['class' => 'form-control']
])
```

### 3. Form Request Handler (2-3 gün)
```php
$form = FormBuilder::create('user')
    ->addText('name')->required()->add()
    ->build();

// Handle request
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $data = $form->getData();
    // Save to database
}
```

**Toplam süre**: ~8-10 gün

## Sonraki Adımlar

Bu özellikler eklendikçe, FormGenerator:
1. Symfony Form'a gerçek alternatif olur
2. Enterprise projeler için production-ready olur
3. Auto-form generation ile developer experience 10x artar
4. Symfony/Laravel'de native form component yerine tercih edilebilir

**Başlayalım mı?**

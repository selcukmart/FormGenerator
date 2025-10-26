<?php

declare(strict_types=1);

namespace FormGenerator\V2\Builder;

use FormGenerator\V2\Contracts\{
    BuilderInterface,
    DataProviderInterface,
    InputType,
    RendererInterface,
    ScopeType,
    SecurityInterface,
    ThemeInterface,
    ValidatorInterface
};
use FormGenerator\V2\Validation\{ValidationManager, SymfonyValidator};

/**
 * Form Builder - Main Entry Point with Chain Pattern
 *
 * Usage Example:
 * <code>
 * $form = FormBuilder::create('user_form')
 *     ->setAction('/users/save')
 *     ->setMethod('POST')
 *     ->addText('name')->required()->minLength(3)->add()
 *     ->addEmail('email')->required()->add()
 *     ->addSelect('country')->options($countries)->add()
 *     ->addSubmit('save', 'Save User')
 *     ->build();
 * </code>
 *
 * @author selcukmart
 * @since 2.0.0
 */
class FormBuilder implements BuilderInterface
{
    private string $name;
    private string $method = 'POST';
    private string $action = '';
    private ScopeType $scope = ScopeType::ADD;
    private array $attributes = [];
    private array $inputs = [];
    private array $sections = [];
    private ?Section $currentSection = null;
    private ?DataProviderInterface $dataProvider = null;
    private ?RendererInterface $renderer = null;
    private ?ThemeInterface $theme = null;
    private ?SecurityInterface $security = null;
    private ?ValidatorInterface $validator = null;
    private bool $enableCsrf = true;
    private bool $enableValidation = true;
    private bool $enableClientSideValidation = true;
    private array $data = [];
    private ?string $enctype = null;
    private ?object $dto = null;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create new form builder instance
     */
    public static function create(string $name): self
    {
        return new self($name);
    }

    /**
     * Set form name
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get form name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set form method (GET/POST)
     */
    public function setMethod(string $method): self
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Set form action URL
     */
    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Set form scope (add/edit/view)
     */
    public function setScope(ScopeType $scope): self
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Set form as edit mode
     */
    public function edit(): self
    {
        $this->scope = ScopeType::EDIT;
        return $this;
    }

    /**
     * Set form as view mode
     */
    public function view(): self
    {
        $this->scope = ScopeType::VIEW;
        return $this;
    }

    /**
     * Set data provider
     */
    public function setDataProvider(DataProviderInterface $provider): self
    {
        $this->dataProvider = $provider;
        return $this;
    }

    /**
     * Set renderer engine
     */
    public function setRenderer(RendererInterface $renderer): self
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Set theme
     */
    public function setTheme(ThemeInterface $theme): self
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * Set security handler
     */
    public function setSecurity(SecurityInterface $security): self
    {
        $this->security = $security;
        return $this;
    }

    /**
     * Set validator
     */
    public function setValidator(ValidatorInterface $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Enable/disable validation
     */
    public function enableValidation(bool $enable = true): self
    {
        $this->enableValidation = $enable;
        return $this;
    }

    /**
     * Enable/disable client-side (JavaScript) validation
     */
    public function enableClientSideValidation(bool $enable = true): self
    {
        $this->enableClientSideValidation = $enable;
        return $this;
    }

    /**
     * Set custom animation options for dependencies
     *
     * @param array $options Options: enabled (bool), duration (int ms), type (fade|slide|none), easing (string)
     */
    public function setDependencyAnimation(array $options): self
    {
        DependencyManager::setAnimationOptions($this->name, $options);
        return $this;
    }

    /**
     * Disable dependency animations
     */
    public function disableDependencyAnimation(): self
    {
        DependencyManager::setAnimationOptions($this->name, ['enabled' => false]);
        return $this;
    }

    /**
     * Enable/disable CSRF protection
     */
    public function enableCsrf(bool $enable = true): self
    {
        $this->enableCsrf = $enable;
        return $this;
    }

    /**
     * Set form data (for edit/view mode)
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Load data from provider by ID
     */
    public function loadData(mixed $id): self
    {
        if ($this->dataProvider === null) {
            throw new \RuntimeException('Data provider not set');
        }

        $data = $this->dataProvider->findById($id);
        if ($data !== null) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Set DTO/Entity object (Symfony DTO support)
     * Automatically extracts data and validation rules
     */
    public function setDto(object $dto): self
    {
        $this->dto = $dto;

        // Extract data from DTO
        $reflection = new \ReflectionClass($dto);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($dto);
            if ($value !== null) {
                $this->data[$property->getName()] = $value;
            }
        }

        // Extract validation rules if using SymfonyValidator
        if ($this->validator instanceof SymfonyValidator) {
            $rules = $this->validator->extractRulesFromObject($dto);
            foreach ($rules as $fieldName => $fieldRules) {
                // Apply rules to corresponding inputs
                foreach ($this->inputs as $input) {
                    if ($input->getName() === $fieldName) {
                        // Rules will be applied during validation
                        break;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get DTO object
     */
    public function getDto(): ?object
    {
        return $this->dto;
    }

    /**
     * Validate form data against DTO
     */
    public function validateDto(array $data): array
    {
        if ($this->dto === null) {
            throw new \RuntimeException('DTO not set. Use setDto() first.');
        }

        if (!$this->validator instanceof SymfonyValidator) {
            throw new \RuntimeException('SymfonyValidator required for DTO validation');
        }

        // Hydrate DTO with data
        $reflection = new \ReflectionClass($this->dto);
        foreach ($data as $key => $value) {
            if ($reflection->hasProperty($key)) {
                $property = $reflection->getProperty($key);
                $property->setAccessible(true);
                $property->setValue($this->dto, $value);
            }
        }

        // Validate DTO
        $result = $this->validator->validateObject($this->dto);

        return $result->getErrors();
    }

    /**
     * Set HTML attributes
     */
    public function attributes(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Set enctype (for file uploads)
     */
    public function setEnctype(string $enctype): self
    {
        $this->enctype = $enctype;
        return $this;
    }

    /**
     * Enable multipart for file uploads
     */
    public function multipart(): self
    {
        $this->enctype = 'multipart/form-data';
        return $this;
    }

    // ========== Section Methods ==========

    /**
     * Start a new section
     *
     * @param string $title Section title
     * @param string $description Optional description (supports HTML)
     */
    public function addSection(string $title, string $description = ''): self
    {
        $section = new Section($title);
        if ($description !== '') {
            $section->setDescription($description);
        }

        $this->currentSection = $section;
        $this->sections[] = $section;

        return $this;
    }

    /**
     * Set HTML content for current section
     */
    public function setSectionHtml(string $html): self
    {
        if ($this->currentSection !== null) {
            $this->currentSection->setHtmlContent($html);
        }

        return $this;
    }

    /**
     * Set attributes for current section
     */
    public function setSectionAttributes(array $attributes): self
    {
        if ($this->currentSection !== null) {
            $this->currentSection->setAttributes($attributes);
        }

        return $this;
    }

    /**
     * Set classes for current section
     */
    public function setSectionClasses(array $classes): self
    {
        if ($this->currentSection !== null) {
            $this->currentSection->setClasses($classes);
        }

        return $this;
    }

    /**
     * Make current section collapsible
     */
    public function collapsibleSection(bool $collapsed = false): self
    {
        if ($this->currentSection !== null) {
            $this->currentSection->collapsible($collapsed);
        }

        return $this;
    }

    /**
     * End current section
     */
    public function endSection(): self
    {
        $this->currentSection = null;
        return $this;
    }

    // ========== Input Building Methods ==========

    /**
     * Add text input
     */
    public function addText(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::TEXT, $label);
    }

    /**
     * Add email input
     */
    public function addEmail(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::EMAIL, $label);
    }

    /**
     * Add password input
     */
    public function addPassword(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::PASSWORD, $label);
    }

    /**
     * Add textarea
     */
    public function addTextarea(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::TEXTAREA, $label);
    }

    /**
     * Add select dropdown
     */
    public function addSelect(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::SELECT, $label);
    }

    /**
     * Add checkbox
     */
    public function addCheckbox(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::CHECKBOX, $label);
    }

    /**
     * Add radio buttons
     */
    public function addRadio(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::RADIO, $label);
    }

    /**
     * Add file input
     */
    public function addFile(string $name, ?string $label = null): InputBuilder
    {
        $this->multipart(); // Auto-enable multipart
        return $this->createInput($name, InputType::FILE, $label);
    }

    /**
     * Add image input
     */
    public function addImage(string $name, ?string $label = null): InputBuilder
    {
        $this->multipart(); // Auto-enable multipart
        return $this->createInput($name, InputType::IMAGE, $label);
    }

    /**
     * Add hidden input
     */
    public function addHidden(string $name, mixed $value = null): InputBuilder
    {
        $input = $this->createInput($name, InputType::HIDDEN);
        if ($value !== null) {
            $input->value($value);
        }
        return $input;
    }

    /**
     * Add number input
     */
    public function addNumber(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::NUMBER, $label);
    }

    /**
     * Add date input
     */
    public function addDate(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::DATE, $label);
    }

    /**
     * Add datetime input
     */
    public function addDatetime(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::DATETIME, $label);
    }

    /**
     * Add time input
     */
    public function addTime(string $name, ?string $label = null): InputBuilder
    {
        return $this->createInput($name, InputType::TIME, $label);
    }

    /**
     * Add checkbox tree (hierarchical checkboxes)
     *
     * @param string $name Field name
     * @param string|null $label Field label
     * @param array $tree Hierarchical array structure
     * @param string $mode 'cascade' or 'independent'
     *
     * Example tree structure:
     * [
     *     ['value' => 'parent1', 'label' => 'Parent 1', 'children' => [
     *         ['value' => 'child1', 'label' => 'Child 1'],
     *         ['value' => 'child2', 'label' => 'Child 2']
     *     ]],
     *     ['value' => 'parent2', 'label' => 'Parent 2']
     * ]
     */
    public function addCheckboxTree(
        string $name,
        ?string $label = null,
        array $tree = [],
        string $mode = CheckboxTreeManager::MODE_CASCADE
    ): InputBuilder {
        $input = $this->createInput($name, InputType::CHECKBOX_TREE, $label);
        $input->setTree($tree);
        $input->setTreeMode($mode);
        return $input;
    }

    /**
     * Add repeater field group (dynamic add/remove rows)
     *
     * @param string $name Field name
     * @param string|null $label Field label
     * @param callable $callback Callback to define repeatable fields
     *
     * Example:
     * $form->addRepeater('contacts', 'Contact List', function($repeater) {
     *     $repeater->addText('name', 'Name')->add();
     *     $repeater->addEmail('email', 'Email')->add();
     * });
     */
    public function addRepeater(string $name, ?string $label = null, ?callable $callback = null): InputBuilder
    {
        $input = $this->createInput($name, InputType::REPEATER, $label);
        if ($callback !== null) {
            // Create a temporary form builder for repeater fields
            $repeaterBuilder = new FormBuilder($name . '_template');
            $repeaterBuilder->setTheme($this->theme);
            $repeaterBuilder->setRenderer($this->renderer);

            $callback($repeaterBuilder);

            $input->setRepeaterFields($repeaterBuilder);
        }
        return $input;
    }

    /**
     * Add submit button
     */
    public function addSubmit(string $name = 'submit', ?string $label = null): self
    {
        $input = $this->createInput($name, InputType::SUBMIT, $label ?? 'Submit');
        $input->add();
        return $this;
    }

    /**
     * Add reset button
     */
    public function addReset(string $name = 'reset', ?string $label = null): self
    {
        $input = $this->createInput($name, InputType::RESET, $label ?? 'Reset');
        $input->add();
        return $this;
    }

    /**
     * Add button
     */
    public function addButton(string $name, string $label): self
    {
        $input = $this->createInput($name, InputType::BUTTON, $label);
        $input->add();
        return $this;
    }

    /**
     * Create input builder
     */
    private function createInput(string $name, InputType $type, ?string $label = null): InputBuilder
    {
        $input = new InputBuilder($this, $name, $type);

        if ($label !== null) {
            $input->label($label);
        }

        // Auto-fill value from data if available
        if (isset($this->data[$name])) {
            $input->value($this->data[$name]);
        }

        return $input;
    }

    /**
     * Add input builder to form (called by InputBuilder)
     *
     * @internal
     */
    public function addInputBuilder(InputBuilder $input): void
    {
        $this->inputs[] = [
            'input' => $input,
            'section' => $this->currentSection
        ];
    }

    /**
     * Build and return form HTML
     */
    public function build(): string
    {
        if ($this->renderer === null) {
            throw new \RuntimeException('Renderer not set. Use setRenderer() before building.');
        }

        if ($this->theme === null) {
            throw new \RuntimeException('Theme not set. Use setTheme() before building.');
        }

        $context = [
            'form' => $this->buildFormContext(),
            'inputs' => $this->buildInputsContext(),
            'csrf_token' => $this->getCsrfToken(),
        ];

        $formHtml = $this->renderer->render($this->theme->getFormTemplate(), $context);

        // Add dependency management JavaScript if any inputs have dependencies
        if ($this->hasDependencies()) {
            $formHtml .= "\n" . DependencyManager::generateScript($this->name);
        }

        // Add validation JavaScript if validation is enabled
        if ($this->enableValidation && $this->enableClientSideValidation && $this->validator !== null) {
            $fieldsRules = $this->collectValidationRules();
            if (!empty($fieldsRules)) {
                $formHtml .= "\n" . ValidationManager::generateScript(
                    $this->name,
                    $fieldsRules,
                    $this->validator
                );
            }
        }

        return $formHtml;
    }

    /**
     * Collect validation rules from all inputs
     */
    private function collectValidationRules(): array
    {
        $rules = [];

        foreach ($this->inputs as $item) {
            $input = $item['input'];
            $config = $input->toArray();
            if (!empty($config['validationRules'])) {
                $rules[$config['name']] = $config['validationRules'];
            }
        }

        // Merge with DTO rules if available
        if ($this->dto !== null && $this->validator instanceof SymfonyValidator) {
            $dtoRules = $this->validator->extractRulesFromObject($this->dto);
            $rules = array_merge($dtoRules, $rules); // Input rules override DTO rules
        }

        return $rules;
    }

    /**
     * Check if form has any dependency configurations
     */
    private function hasDependencies(): bool
    {
        foreach ($this->inputs as $item) {
            $input = $item['input'];
            $config = $input->toArray();
            if (!empty($config['dependencies']) || isset($config['attributes']['data-dependency'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build form context for template
     */
    private function buildFormContext(): array
    {
        $attributes = array_merge([
            'name' => $this->name,
            'id' => $this->name,
            'method' => $this->method,
            'action' => $this->action,
        ], $this->attributes);

        if ($this->enctype !== null) {
            $attributes['enctype'] = $this->enctype;
        }

        return [
            'name' => $this->name,
            'method' => $this->method,
            'action' => $this->action,
            'scope' => $this->scope->value,
            'attributes' => $attributes,
            'classes' => $this->theme->getFormClasses(),
        ];
    }

    /**
     * Build inputs context for template
     */
    private function buildInputsContext(): array
    {
        $inputsContext = [];
        $currentSectionInputs = [];
        $lastSection = null;

        foreach ($this->inputs as $item) {
            $input = $item['input'];
            $section = $item['section'];

            // If we have sections, group inputs by section
            if (!empty($this->sections)) {
                // New section encountered
                if ($section !== $lastSection) {
                    // Save previous section inputs if any
                    if ($lastSection !== null || !empty($currentSectionInputs)) {
                        $inputsContext[] = [
                            'is_section' => true,
                            'section' => $lastSection?->toArray(),
                            'inputs' => $currentSectionInputs
                        ];
                        $currentSectionInputs = [];
                    }
                    $lastSection = $section;
                }
            }

            $inputData = $input->toArray();
            $inputData['template'] = $this->theme->getInputTemplate($input->getType());
            $inputData['classes'] = $this->theme->getInputClasses($input->getType());

            // Sanitize values if security is enabled
            if ($this->security !== null && $inputData['value'] !== null) {
                $inputData['value'] = $this->security->sanitize($inputData['value']);
            }

            if (!empty($this->sections)) {
                $currentSectionInputs[] = $inputData;
            } else {
                $inputsContext[] = $inputData;
            }
        }

        // Add final section inputs if any
        if (!empty($this->sections) && (!empty($currentSectionInputs) || $lastSection !== null)) {
            $inputsContext[] = [
                'is_section' => true,
                'section' => $lastSection?->toArray(),
                'inputs' => $currentSectionInputs
            ];
        }

        return $inputsContext;
    }

    /**
     * Get CSRF token
     */
    private function getCsrfToken(): ?string
    {
        if (!$this->enableCsrf || $this->security === null) {
            return null;
        }

        return $this->security->generateCsrfToken($this->name);
    }

    /**
     * Get form configuration as array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'method' => $this->method,
            'action' => $this->action,
            'scope' => $this->scope->value,
            'attributes' => $this->attributes,
            'enctype' => $this->enctype,
            'csrf_enabled' => $this->enableCsrf,
            'inputs' => array_map(fn($input) => $input->toArray(), $this->inputs),
        ];
    }
}

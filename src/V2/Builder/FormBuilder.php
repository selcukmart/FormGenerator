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
    ThemeInterface
};

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
    private ?DataProviderInterface $dataProvider = null;
    private ?RendererInterface $renderer = null;
    private ?ThemeInterface $theme = null;
    private ?SecurityInterface $security = null;
    private bool $enableCsrf = true;
    private array $data = [];
    private ?string $enctype = null;

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
        $this->inputs[] = $input;
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

        return $formHtml;
    }

    /**
     * Check if form has any dependency configurations
     */
    private function hasDependencies(): bool
    {
        foreach ($this->inputs as $input) {
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

        foreach ($this->inputs as $input) {
            $inputData = $input->toArray();
            $inputData['template'] = $this->theme->getInputTemplate($input->getType());
            $inputData['classes'] = $this->theme->getInputClasses($input->getType());

            // Sanitize values if security is enabled
            if ($this->security !== null && $inputData['value'] !== null) {
                $inputData['value'] = $this->security->sanitize($inputData['value']);
            }

            $inputsContext[] = $inputData;
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

<?php

declare(strict_types=1);

namespace FormGenerator\V2\Builder;

use FormGenerator\V2\Contracts\InputType;
use FormGenerator\V2\Contracts\DataProviderInterface;

/**
 * Input Builder - Chain Pattern for Individual Form Inputs
 *
 * @author selcukmart
 * @since 2.0.0
 */
class InputBuilder
{
    private string $name;
    private InputType $type;
    private mixed $value = null;
    private ?string $label = null;
    private ?string $placeholder = null;
    private ?string $helpText = null;
    private array $attributes = [];
    private array $wrapperAttributes = [];
    private array $labelAttributes = [];
    private array $validationRules = [];
    private array $options = [];
    private ?DataProviderInterface $optionsProvider = null;
    private array $dependencies = [];
    private bool $required = false;
    private bool $disabled = false;
    private bool $readonly = false;
    private ?string $defaultValue = null;

    public function __construct(
        private readonly FormBuilder $formBuilder,
        string $name,
        InputType $type
    ) {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Set input label
     */
    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set input placeholder
     */
    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Set help text below input
     */
    public function helpText(string $text): self
    {
        $this->helpText = $text;
        return $this;
    }

    /**
     * Set input value
     */
    public function value(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set default value (when no data provided)
     */
    public function defaultValue(mixed $value): self
    {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * Mark input as required
     */
    public function required(bool $required = true): self
    {
        $this->required = $required;
        if ($required) {
            $this->validationRules['required'] = true;
        }
        return $this;
    }

    /**
     * Mark input as disabled
     */
    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * Mark input as readonly
     */
    public function readonly(bool $readonly = true): self
    {
        $this->readonly = $readonly;
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
     * Set single HTML attribute
     */
    public function attribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Set wrapper/container attributes
     */
    public function wrapperAttributes(array $attributes): self
    {
        $this->wrapperAttributes = array_merge($this->wrapperAttributes, $attributes);
        return $this;
    }

    /**
     * Set label attributes
     */
    public function labelAttributes(array $attributes): self
    {
        $this->labelAttributes = array_merge($this->labelAttributes, $attributes);
        return $this;
    }

    /**
     * Add CSS class
     */
    public function addClass(string $class): self
    {
        if (!isset($this->attributes['class'])) {
            $this->attributes['class'] = '';
        }
        $this->attributes['class'] = trim($this->attributes['class'] . ' ' . $class);
        return $this;
    }

    /**
     * Set options for select/radio/checkbox
     */
    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Set options from data provider
     */
    public function optionsFromProvider(
        DataProviderInterface $provider,
        string $keyColumn,
        string $labelColumn,
        array $criteria = []
    ): self {
        $this->optionsProvider = $provider;
        $this->options = $provider->getOptions($keyColumn, $labelColumn, $criteria);
        return $this;
    }

    /**
     * Add dependency (show/hide based on another field)
     */
    public function dependsOn(string $fieldName, mixed $value): self
    {
        $this->dependencies[] = [
            'field' => $fieldName,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * Validation: Minimum length
     */
    public function minLength(int $length): self
    {
        $this->validationRules['minLength'] = $length;
        return $this;
    }

    /**
     * Validation: Maximum length
     */
    public function maxLength(int $length): self
    {
        $this->validationRules['maxLength'] = $length;
        $this->attributes['maxlength'] = $length;
        return $this;
    }

    /**
     * Validation: Minimum value (for number inputs)
     */
    public function min(int|float $value): self
    {
        $this->validationRules['min'] = $value;
        $this->attributes['min'] = $value;
        return $this;
    }

    /**
     * Validation: Maximum value (for number inputs)
     */
    public function max(int|float $value): self
    {
        $this->validationRules['max'] = $value;
        $this->attributes['max'] = $value;
        return $this;
    }

    /**
     * Validation: Pattern matching
     */
    public function pattern(string $regex, ?string $message = null): self
    {
        $this->validationRules['pattern'] = [
            'regex' => $regex,
            'message' => $message ?? "Invalid format for {$this->name}",
        ];
        $this->attributes['pattern'] = $regex;
        return $this;
    }

    /**
     * Validation: Email format
     */
    public function email(): self
    {
        $this->validationRules['email'] = true;
        $this->type = InputType::EMAIL;
        return $this;
    }

    /**
     * Finish building this input and return to FormBuilder
     */
    public function add(): FormBuilder
    {
        $this->formBuilder->addInputBuilder($this);
        return $this->formBuilder;
    }

    /**
     * Get input configuration as array
     */
    public function toArray(): array
    {
        $config = [
            'name' => $this->name,
            'type' => $this->type->value,
            'label' => $this->label ?? ucfirst(str_replace('_', ' ', $this->name)),
            'value' => $this->value,
            'placeholder' => $this->placeholder,
            'helpText' => $this->helpText,
            'attributes' => $this->buildAttributes(),
            'wrapperAttributes' => $this->wrapperAttributes,
            'labelAttributes' => $this->labelAttributes,
            'required' => $this->required,
            'disabled' => $this->disabled,
            'readonly' => $this->readonly,
            'validationRules' => $this->validationRules,
            'dependencies' => $this->dependencies,
        ];

        if ($this->type->requiresOptions()) {
            $config['options'] = $this->options;
        }

        if ($this->defaultValue !== null) {
            $config['defaultValue'] = $this->defaultValue;
        }

        return $config;
    }

    /**
     * Build final HTML attributes array
     */
    private function buildAttributes(): array
    {
        $attrs = $this->attributes;
        $attrs['name'] = $this->name;
        $attrs['id'] = $attrs['id'] ?? $this->name;

        if ($this->required) {
            $attrs['required'] = 'required';
        }

        if ($this->disabled) {
            $attrs['disabled'] = 'disabled';
        }

        if ($this->readonly) {
            $attrs['readonly'] = 'readonly';
        }

        if ($this->placeholder) {
            $attrs['placeholder'] = $this->placeholder;
        }

        return $attrs;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): InputType
    {
        return $this->type;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}

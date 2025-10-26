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
    private array $tree = [];
    private string $treeMode = CheckboxTreeManager::MODE_CASCADE;
    private ?FormBuilder $repeaterFields = null;
    private int $repeaterMin = 0;
    private int $repeaterMax = 10;

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
     * This input will show/hide when the dependency field changes
     *
     * @param string $fieldName The field this input depends on
     * @param string|array $value The value(s) that trigger this input to show
     * @param string|null $group Optional group name for dependency management
     */
    public function dependsOn(string $fieldName, string|array $value, ?string $group = null): self
    {
        $values = is_array($value) ? $value : [$value];

        $this->dependencies[] = [
            'field' => $fieldName,
            'values' => $values,
            'group' => $group ?? $fieldName, // Use field name as default group
        ];

        // Add data attributes for JavaScript
        $dependValues = array_map(fn($v) => $fieldName . '-' . $v, $values);
        $this->wrapperAttributes['data-dependends'] = '';
        $this->wrapperAttributes['data-dependend'] = implode(' ', $dependValues);
        $this->wrapperAttributes['data-dependend-group'] = $group ?? $fieldName;

        // Initially hide dependent fields (will be shown by JS if condition met)
        $this->wrapperAttributes['style'] = 'display: none;';

        return $this;
    }

    /**
     * Mark this input as a dependency controller
     * Other inputs can depend on this input's value
     *
     * @param string|null $group Optional group name
     */
    public function isDependency(?string $group = null): self
    {
        $this->attributes['data-dependency'] = 'true';
        $this->attributes['data-dependency-group'] = $group ?? $this->name;
        $this->attributes['data-dependency-field'] = $this->name;

        return $this;
    }

    /**
     * Shortcut: Make this input control dependencies and configure dependent
     *
     * Example:
     * ->controls('company_fields') // This field controls 'company_fields' group
     */
    public function controls(string $groupName): self
    {
        return $this->isDependency($groupName);
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

    // ========== CheckboxTree Methods ==========

    /**
     * Set tree structure for checkbox tree
     *
     * @param array $tree Hierarchical array structure
     */
    public function setTree(array $tree): self
    {
        $this->tree = $tree;
        return $this;
    }

    /**
     * Set tree mode (cascade or independent)
     */
    public function setTreeMode(string $mode): self
    {
        $this->treeMode = $mode;
        return $this;
    }

    /**
     * Get tree structure
     */
    public function getTree(): array
    {
        return $this->tree;
    }

    /**
     * Get tree mode
     */
    public function getTreeMode(): string
    {
        return $this->treeMode;
    }

    // ========== Repeater Methods ==========

    /**
     * Set repeater field template
     */
    public function setRepeaterFields(FormBuilder $fields): self
    {
        $this->repeaterFields = $fields;
        return $this;
    }

    /**
     * Set minimum number of rows
     */
    public function minRows(int $min): self
    {
        $this->repeaterMin = $min;
        return $this;
    }

    /**
     * Set maximum number of rows
     */
    public function maxRows(int $max): self
    {
        $this->repeaterMax = $max;
        return $this;
    }

    /**
     * Get repeater fields
     */
    public function getRepeaterFields(): ?FormBuilder
    {
        return $this->repeaterFields;
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

        // CheckboxTree specific
        if ($this->type === InputType::CHECKBOX_TREE) {
            $config['tree'] = $this->tree;
            $config['treeMode'] = $this->treeMode;
        }

        // Repeater specific
        if ($this->type === InputType::REPEATER) {
            $config['repeaterFields'] = $this->repeaterFields;
            $config['repeaterMin'] = $this->repeaterMin;
            $config['repeaterMax'] = $this->repeaterMax;
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

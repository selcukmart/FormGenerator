<?php

declare(strict_types=1);

namespace FormGenerator\V2\Event;

use FormGenerator\V2\Builder\FormBuilder;

/**
 * Form Event - Container for event data
 *
 * Carries information about the form and its data during event dispatch.
 *
 * Example:
 * ```php
 * $event = new FormEvent($builder, $data);
 *
 * // Get data
 * $data = $event->getData();
 *
 * // Modify data
 * $event->setData(['username' => 'modified']);
 *
 * // Stop event propagation
 * $event->stopPropagation();
 * ```
 */
class FormEvent
{
    private bool $propagationStopped = false;

    /**
     * @param FormBuilder $form Form builder instance
     * @param mixed $data Form data
     * @param array $context Additional context data
     */
    public function __construct(
        private FormBuilder $form,
        private mixed $data = null,
        private array $context = []
    ) {
    }

    /**
     * Get the form builder
     *
     * @return FormBuilder Form builder instance
     */
    public function getForm(): FormBuilder
    {
        return $this->form;
    }

    /**
     * Get form data
     *
     * @return mixed Form data
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Set form data
     *
     * @param mixed $data New form data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     * Get context data
     *
     * @param string|null $key Context key, or null to get all context
     * @param mixed $default Default value if key doesn't exist
     * @return mixed Context value or all context
     */
    public function getContext(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->context;
        }

        return $this->context[$key] ?? $default;
    }

    /**
     * Set context data
     *
     * @param string $key Context key
     * @param mixed $value Context value
     */
    public function setContext(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    /**
     * Check if context has a key
     *
     * @param string $key Context key
     * @return bool True if context has the key
     */
    public function hasContext(string $key): bool
    {
        return array_key_exists($key, $this->context);
    }

    /**
     * Stop event propagation
     *
     * Prevents subsequent listeners from being called
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * Check if propagation is stopped
     *
     * @return bool True if propagation is stopped
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}

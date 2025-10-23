<?php

declare(strict_types=1);

namespace FormGenerator\V2\Builder;

/**
 * Dependency Manager
 *
 * Generates JavaScript code for managing input dependencies
 * (show/hide inputs based on other input values)
 *
 * @author selcukmart
 * @since 2.0.0
 */
class DependencyManager
{
    private static array $renderedForms = [];

    /**
     * Generate dependency JavaScript for a form
     *
     * @param string $formId Unique form identifier
     * @param bool $includeScript Whether to wrap in <script> tags
     */
    public static function generateScript(string $formId, bool $includeScript = true): string
    {
        // Check if already rendered for this form
        if (isset(self::$renderedForms[$formId])) {
            return ''; // Already rendered, return empty
        }

        // Mark as rendered
        self::$renderedForms[$formId] = true;

        $script = self::getJavaScriptCode($formId);

        if ($includeScript) {
            return sprintf('<script type="text/javascript">%s</script>', $script);
        }

        return $script;
    }

    /**
     * Check if script was already rendered for a form
     */
    public static function isRendered(string $formId): bool
    {
        return isset(self::$renderedForms[$formId]);
    }

    /**
     * Reset rendered forms tracker (useful for testing)
     */
    public static function reset(): void
    {
        self::$renderedForms = [];
    }

    /**
     * Get the pure JavaScript code
     */
    private static function getJavaScriptCode(string $formId): string
    {
        // Create a unique namespace for this form
        $namespace = 'FormGen_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $formId);

        return <<<JAVASCRIPT

(function() {
    'use strict';

    /**
     * FormGenerator V2 Dependency Manager
     * Form ID: {$formId}
     */
    const {$namespace} = {
        formSelector: '#{$formId}',

        /**
         * Initialize dependency management
         */
        init: function() {
            const form = document.querySelector(this.formSelector);
            if (!form) {
                console.warn('FormGenerator: Form not found:', this.formSelector);
                return;
            }

            // Attach event listeners to dependency controllers
            const dependencyElements = form.querySelectorAll('[data-dependency="true"]');
            dependencyElements.forEach((element) => {
                const eventType = this.getEventType(element);
                element.addEventListener(eventType, () => this.handleDependency(element));

                // Also trigger on load for select elements with pre-selected values
                if (element.tagName === 'SELECT' && element.value) {
                    this.handleDependency(element);
                }
            });

            // Initial dependency detection
            this.detectInitialState(form);
        },

        /**
         * Get appropriate event type for element
         */
        getEventType: function(element) {
            if (element.tagName === 'SELECT') {
                return 'change';
            }
            if (element.type === 'checkbox' || element.type === 'radio') {
                return 'change';
            }
            return 'input';
        },

        /**
         * Detect and set initial state for all dependencies
         */
        detectInitialState: function(form) {
            const dependencyElements = form.querySelectorAll('[data-dependency="true"]');
            dependencyElements.forEach((element) => {
                if (element.type === 'hidden') {
                    this.handleDependency(element);
                } else if (element.tagName === 'SELECT' && element.value !== '') {
                    this.handleDependency(element);
                } else if ((element.type === 'checkbox' || element.type === 'radio') && element.checked) {
                    this.handleDependency(element);
                }
            });
        },

        /**
         * Handle dependency change
         */
        handleDependency: function(element) {
            const group = element.getAttribute('data-dependency-group');
            const field = element.getAttribute('data-dependency-field');

            if (!group || !field) return;

            let isActive = false;
            let fieldIdentifier = field;

            // Determine if dependency is active
            if (element.tagName === 'SELECT') {
                isActive = element.value !== '' && element.value !== null;
                if (isActive) {
                    fieldIdentifier = field + '-' + element.value;
                }
            } else if (element.type === 'checkbox' || element.type === 'radio') {
                isActive = element.checked;
                if (isActive && element.value) {
                    fieldIdentifier = field + '-' + element.value;
                }
            } else if (element.type === 'hidden') {
                isActive = true;
                if (element.value) {
                    fieldIdentifier = field + '-' + element.value;
                }
            }

            // Find and toggle dependent elements
            this.toggleDependents(group, fieldIdentifier, isActive, element);

            // If select value is empty, hide all dependents in this group
            if (element.tagName === 'SELECT' && (element.value === '' || element.value === null)) {
                this.hideAllDependents(group);
            }
        },

        /**
         * Toggle dependent elements
         */
        toggleDependents: function(group, fieldIdentifier, isActive, triggerElement) {
            const form = document.querySelector(this.formSelector);
            if (!form) return;

            const dependents = form.querySelectorAll('[data-dependend-group="' + group + '"]');

            dependents.forEach((dependent) => {
                const dependedValue = dependent.getAttribute('data-dependend');
                if (!dependedValue) return;

                const dependedValues = dependedValue.split(' ');

                // Check if this dependent should be shown
                const shouldShow = dependedValues.includes(fieldIdentifier) || dependedValues.includes('all');

                if (shouldShow && isActive) {
                    this.showElement(dependent);
                } else if (!shouldShow || !isActive) {
                    this.hideElement(dependent);
                }
            });
        },

        /**
         * Hide all dependents in a group
         */
        hideAllDependents: function(group) {
            const form = document.querySelector(this.formSelector);
            if (!form) return;

            const dependents = form.querySelectorAll('[data-dependend-group="' + group + '"]');
            dependents.forEach((dependent) => this.hideElement(dependent));
        },

        /**
         * Show element with animation
         */
        showElement: function(element) {
            element.style.display = '';
            element.style.opacity = '0';
            element.style.transition = 'opacity 0.3s ease-in';

            // Enable form inputs
            this.toggleInputs(element, false);

            // Trigger animation
            setTimeout(() => {
                element.style.opacity = '1';
            }, 10);
        },

        /**
         * Hide element with animation
         */
        hideElement: function(element) {
            element.style.opacity = '0';
            element.style.transition = 'opacity 0.3s ease-out';

            setTimeout(() => {
                element.style.display = 'none';
                // Disable and clear form inputs
                this.toggleInputs(element, true);
            }, 300);
        },

        /**
         * Enable/disable and clear inputs within element
         */
        toggleInputs: function(container, disable) {
            const inputs = container.querySelectorAll('input, select, textarea');
            inputs.forEach((input) => {
                if (disable) {
                    input.disabled = true;
                    // Clear value when hiding (except checkboxes/radios)
                    if (input.type !== 'checkbox' && input.type !== 'radio') {
                        input.value = '';
                    } else {
                        input.checked = false;
                    }
                } else {
                    input.disabled = false;
                }
            });
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {$namespace}.init());
    } else {
        {$namespace}.init();
    }
})();

JAVASCRIPT;
    }
}

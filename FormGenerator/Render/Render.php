<?php
/**
 * @author selcukmart
 * 7.06.2021
 * 11:26
 */

namespace FormGenerator\Render;


use FormGenerator\FormGenerator;
use SmartyException;

class Render
{
    protected
        $formGenerator,
        $input_parts = [],
        $input_variables = [
        'form_group_class' => '',
        'label_attributes' => '',
        'label_desc' => '',
        'input_above_desc' => '',
        'input_belove_desc' => '',
        'label' => '',
        'attributes' => '',
        'input_attr' => ''
    ];

    public function __construct(FormGenerator $formGenerator)
    {
        $this->formGenerator = $formGenerator;
    }

    /**
     * @throws SmartyException
     */
    public function render($template = 'TEMPLATE', $return = false)
    {

        if (empty($this->input_parts)) {
            $this->formGenerator->setErrorMessage('There is no input');
        } else {
            $output = $this->embedTemplate($template);
            if ($return) {
                return $output;
            }

            $this->formGenerator->setOutput($output);
        }
    }

    /**
     * @param array $input_parts
     */
    public function setInputParts(array $input_parts): void
    {
        $this->input_parts = $input_parts;
    }

    /**
     * @throws SmartyException
     */
    protected function embedTemplate($template)
    {
        $render_class_name = ucfirst(strtolower($this->formGenerator->getRenderObjectBy()));
        $classname = __NAMESPACE__.'\RenderEngines\\'.$render_class_name;

        $class = $classname::getInstance($this->formGenerator,$this);
        $output = $class->render($template);
        if (!$class->isResult()) {
            $this->formGenerator->setErrorMessage($class->getErrorMessage());
            return false;
        }
        return $output;
    }

    public function __toString()
    {
        return static::class;
    }

    /**
     * @return array
     */
    public function getInputParts(): array
    {
        return $this->input_parts;
    }

    /**
     * @return string[]
     */
    public function getInputVariables(): array
    {
        return $this->input_variables;
    }

    /**
     * @return FormGenerator
     */
    public function getFormGenerator(): FormGenerator
    {
        return $this->formGenerator;
    }

    public function __destruct()
    {

    }
}
<?php
/**
 * @author selcukmart
 * 7.06.2021
 * 11:26
 */

namespace FormGenerator\Render;


use FormGenerator\FormGeneratorDirector;
use Helpers\Classes;
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

    public function __construct(FormGeneratorDirector $formGenerator)
    {
        $this->formGenerator = $formGenerator;
    }

    /**
     * @throws SmartyException
     */
    public function createHtmlOutput($template, $return = false)
    {

        if (empty($this->input_parts)) {
            $this->formGenerator->setErrorMessage('There is no input');
        } else {
            $htmlOutput = $this->getHtmlOutput($template);
            if ($return) {
                return $htmlOutput;
            }

            $this->formGenerator->mergeOutputAsString($htmlOutput);
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
    protected function getHtmlOutput($template)
    {
        $factoryClassname = $this->getFactoryClassname();

        $render_factory = $factoryClassname::getInstance($this->formGenerator,$this);
        $output = $render_factory->createHtmlOutput($template);
        if (!$render_factory->isResult()) {
            $this->formGenerator->setErrorMessage($render_factory->getErrorMessage());
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
     * @return FormGeneratorDirector
     */
    public function getFormGenerator(): FormGeneratorDirector
    {
        return $this->formGenerator;
    }

    /**
     * @return string
     * @author selcukmart
     * 8.02.2022
     * 11:08
     */
    protected function getFactoryClassname(): string
    {
        $render_class_name =Classes::prepareFromString($this->formGenerator->getRenderObjectBy());
        return __NAMESPACE__ . '\RenderEngines\\' . $render_class_name;
    }

    public function __destruct()
    {

    }
}
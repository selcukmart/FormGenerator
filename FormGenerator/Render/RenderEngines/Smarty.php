<?php

namespace FormGenerator\Render\RenderEngines;

use SmartyException;

/**
 * @author selcukmart
 * 2.02.2022
 * 11:41
 */
class Smarty extends AbstractRenderEngines implements RenderInterface
{

    /**
     * @throws SmartyException
     */
    public function render(string $template): string
    {
        $renderObject = $this->formGenerator->getRenderobject();
        $template_dir = $renderObject->getTemplateDir()[0] . $this->formGenerator->getExportFormat();

        if (!is_dir($template_dir)) {
            $template_path =  'Generic';
        }else{
            $template_path = $this->formGenerator->getExportFormat();
        }
        $template = $template_path . '/' . $template . '.tpl';
        $template_full_path = $renderObject->getTemplateDir()[0] . $template;

        if (is_file($template_full_path)) {
            $renderObject->clearAllAssign();
            $input_parts = defaults_form_generator($this->render->getInputParts(), $this->render->getInputVariables());
            foreach ($input_parts as $index => $input_part) {
                $renderObject->assign($index, $input_part);
            }
            $this->setResult(true);
            return $renderObject->fetch($template);
        }
        $this->setErrorMessage('There is no tpl file for this template ' . $template);
        return '';

    }

}
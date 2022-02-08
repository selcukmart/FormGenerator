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
    public function createHtmlOutput(string $template): string
    {
        $renderObject = $this->formGenerator->getRenderobject();
        $template = $this->getTemplateFullPath($renderObject, $template);

        if ($template) {
            $renderObject->clearAllAssign();
            $input_parts = defaults_form_generator($this->render->getInputParts(), $this->render->getInputVariables());
            foreach ($input_parts as $index => $input_part) {
                $renderObject->assign($index, $input_part);
            }
            $this->setResult(true);
            return $renderObject->fetch($template);
        }

        return '';
    }

    /**
     * @param \Smarty $renderObject
     * @param string $template
     * @return false|mixed|string
     * @author selcukmart
     * 8.02.2022
     * 11:14
     */
    protected function getTemplateFullPath(\Smarty $renderObject, string $template)
    {
        if (isset(self::$templates[$template])) {
            return self::$templates[$template];
        }
        $template_dir = $renderObject->getTemplateDir()[0] . $this->formGenerator->getExportFormat();

        if (!is_dir($template_dir)) {
            $template_path = 'Generic';
        } else {
            $template_path = $this->formGenerator->getExportFormat();
        }
        $template_with_path = $template_path . '/' . $template . '.tpl';

        $template_full_path = $renderObject->getTemplateDir()[0] . $template_with_path;

        if (!is_file($template_full_path)) {
            self::$templates[$template] = false;
            $this->setErrorMessage('There is no tpl file for this template ' . $template_with_path);
            return false;
        }
        self::$templates[$template] = $template_with_path;
        return $template_with_path;
    }

}
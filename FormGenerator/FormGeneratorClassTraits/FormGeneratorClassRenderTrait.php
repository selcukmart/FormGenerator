<?php
/**
 * @author selcukmart
 * 8.02.2022
 * 09:43
 */

namespace FormGenerator\FormGeneratorClassTraits;

use Smarty;

trait FormGeneratorClassRenderTrait
{
    protected
        $render_object,
        $render_object_by = 'smarty',
        $render_class_name;

    protected function setRenderObjectDetails(): void
    {
        if ($this->hasProvidedByUser()) {
            $this->setRenderObject($this->getSmartyByUserDefined());
        } else {
            $smarty = new Smarty();
            $smarty->setTemplateDir($this->getBaseDir() . '/../SMARTY_TPL_FILES');
            $smarty->setCompileDir($this->getBaseDir() . '/../SMARTY_TPL_FILES/template_compile');
            $smarty->setCacheDir($this->getBaseDir() . '/../SMARTY_TPL_FILES/template_cache');
            $this->setRenderObject($smarty);
        }
    }

    public function renderToHtml(array $input_parts, $template, $return = false)
    {
        $renderFactory = $this->getRenderInstance();
        $renderFactory->setInputParts($input_parts);
        return $renderFactory->createHtmlOutput($template, $return,$this->getHtmlOutputType());
    }

    /**
     * @return mixed
     * @author selcukmart
     * 7.02.2022
     * 17:11
     */
    protected function getRenderInstance()
    {
        if (isset(self::$instances['render'])) {
            return self::$instances['render'];
        }
        $class = $this->getRenderClassName();
        self::$instances['render'] = new $class($this);
        return self::$instances['render'];
    }


    /**
     * @return string
     * @author selcukmart
     * 7.02.2022
     * 17:11
     */
    protected function getRenderClassName(): string
    {
        if (!is_null($this->render_class_name)) {
            return $this->render_class_name;
        }
        $this->render_class_name = $this->namespace . '\Render\\Render';
        return $this->render_class_name;
    }

    /**
     * @return mixed
     */
    public function getRenderobject()
    {
        return $this->render_object;
    }

    /**
     * @param mixed $smarty
     */
    public function setRenderObject(Smarty $smarty): void
    {
        $this->render_object = $smarty;
    }

    /**
     * @return mixed
     */
    public function getRenderObjectBy()
    {
        return $this->render_object_by;
    }

    /**
     * @return bool
     * @author selcukmart
     * 8.02.2022
     * 11:22
     */
    protected function hasProvidedByUser(): bool
    {
        return isset($this->generator_array['build']['render']['by']) && $this->getSmartyByUserDefined() !== null && is_string($this->generator_array['build']['render']['by']) && !empty($this->generator_array['build']['render']['by']) && is_object($this->getSmartyByUserDefined());
    }

    /**
     * @return mixed
     * @author selcukmart
     * 8.02.2022
     * 11:23
     */
    protected function getSmartyByUserDefined()
    {
        return $this->generator_array[$this->generator_array['build']['render']['by']] ?? null;
    }
}
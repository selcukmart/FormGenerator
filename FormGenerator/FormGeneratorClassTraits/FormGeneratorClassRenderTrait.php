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
        if (isset($this->generator_array['export']['render']['by'], $this->generator_array[$this->generator_array['export']['render']['by']]) && is_string($this->generator_array['export']['render']['by']) && !empty($this->generator_array['export']['render']['by']) && is_object($this->generator_array[$this->generator_array['export']['render']['by']])) {
            $this->setRenderObject($this->generator_array[$this->generator_array['export']['render']['by']]);
        } else {
            $smarty = new Smarty();
            $smarty->setTemplateDir($this->getBaseDir() . '/../SMARTY_TPL_FILES');
            $smarty->setCompileDir($this->getBaseDir() . '/../SMARTY_TPL_FILES/template_compile');
            $smarty->setCacheDir($this->getBaseDir() . '/../SMARTY_TPL_FILES/template_cache');
            $this->setRenderObject($smarty);
        }
    }

    public function render(array $input_parts, $template = 'TEMPLATE', $return = false)
    {
        $run = $this->getRenderInstance();
        $run->setInputParts($input_parts);
        return $run->render($template, $return);
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
}
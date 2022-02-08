<?php

namespace FormGenerator\Render\RenderEngines;

use FormGenerator\FormGenerator;
use FormGenerator\Render\Render;
use GlobalTraits\ErrorMessagesWithResultTrait;

/**
 * @pattern singleton and factory
 * @author selcukmart
 * 2.02.2022
 * 11:45
 */
abstract class AbstractRenderEngines
{
    use ErrorMessagesWithResultTrait;

    private static
        $instances = [];
    protected static
        $templates;
    protected
        $formGenerator,
        $render;

    public function __construct(FormGenerator $formGenerator, Render $templateObject)
    {
        $this->formGenerator = $formGenerator;
        $this->render = $templateObject;
    }

    public static function getInstance(FormGenerator $formGenerator, $templateObject): AbstractRenderEngines
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static($formGenerator, $templateObject);
        }

        return self::$instances[$cls];
    }
}
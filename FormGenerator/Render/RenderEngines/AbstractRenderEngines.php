<?php
namespace FormGenerator\Render\RenderEngines;
use FormGenerator\FormGenerator;
use FormGenerator\Render\Render;
use GlobalTraits\ErrorMessagesWithResultTrait;

/**
 * @author selcukmart
 * 2.02.2022
 * 11:45
 */
abstract class AbstractRenderEngines
{
    use ErrorMessagesWithResultTrait;
    protected
        $formGenerator,
        $render;

    public function __construct(FormGenerator $formGenerator, Render $templateObject)
    {
        $this->formGenerator = $formGenerator;
        $this->render = $templateObject;
    }

    public static function getInstance(FormGenerator $formGenerator,$templateObject): AbstractRenderEngines
    {
        return new static($formGenerator,$templateObject);
    }
}
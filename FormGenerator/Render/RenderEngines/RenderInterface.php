<?php
namespace FormGenerator\Render\RenderEngines;
use FormGenerator\FormGenerator;
use FormGenerator\Render\Render;

/**
 * @author selcukmart
 * 2.02.2022
 * 11:46
 */
interface RenderInterface
{
    public function __construct(FormGenerator $formGenerator,Render $templateObject);

    public function render(string $template):string;
}
<?php
/**
 * @author selcukmart
 * 2.02.2022
 * 11:18
 */

namespace FormGenerator\FormGeneratorBuilder;

use FormGenerator\FormGenerator;

interface BuilderInterface
{
    public function __construct(FormGenerator $formGenerator);

    public function createHtmlOutput($items = null, $parent_group = null):void;

    public function __destruct();
}
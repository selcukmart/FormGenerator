<?php
/**
 * @author selcukmart
 * 2.02.2022
 * 11:18
 */

namespace FormGenerator\FormGeneratorBuilder;

use FormGenerator\FormGeneratorDirector;

interface BuilderInterface
{
    public function __construct(FormGeneratorDirector $formGenerator);

    public function createHtmlOutput($items = null, $parent_group = null):void;

    public function __destruct();
}
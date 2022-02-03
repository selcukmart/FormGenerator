<?php
/**
 * @author selcukmart
 * 2.02.2022
 * 11:18
 */

namespace FormGenerator\FormGeneratorExport;

use FormGenerator\FormGenerator;

interface ExportInterface
{
    public function __construct(FormGenerator $formGenerator);

    public function extract($items = null, $parent_group = null):void;

    public function __destruct();
}
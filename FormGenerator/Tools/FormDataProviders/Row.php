<?php
/**
 * @author selcukmart
 * 5.02.2022
 * 13:29
 */

namespace FormGenerator\Tools\FormDataProviders;

class Row extends AbstractFormDataProviders implements FormDataProvidersInterface
{

    public function execute(): array
    {
        return $this->data['row'];

    }
}
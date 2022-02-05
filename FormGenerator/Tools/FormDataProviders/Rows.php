<?php
/**
 * @author selcukmart
 * 5.02.2022
 * 13:29
 */

namespace FormGenerator\Tools\FormDataProviders;

class Rows extends AbstractFormDataProviders implements FormDataProvidersInterface
{

    public function execute(): array
    {
        return $this->data['rows'];

    }

    public function execute4multiple(): array
    {
        return $this->execute();
    }
}
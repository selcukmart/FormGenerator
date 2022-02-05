<?php
/**
 * @author selcukmart
 * 5.02.2022
 * 13:29
 */

namespace FormGenerator\Tools\FormDataProviders;

use Examples\DBExamples\Libraries\Database\DB;

class Query extends AbstractFormDataProviders implements FormDataProvidersInterface
{

    public function execute(): array
    {
        if (empty($this->data['query'])) {
            $this->formGenerator->setErrorMessage('Query is empty');
            return [];
        }
        return DB::fetch($this->data['query']);
    }
}
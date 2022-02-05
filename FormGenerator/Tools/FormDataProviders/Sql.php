<?php
/**
 * @author selcukmart
 * 5.02.2022
 * 13:29
 */

namespace FormGenerator\Tools\FormDataProviders;

use Examples\DBExamples\Libraries\Database\DB;
use FormGenerator\Tools\Row;

class Sql extends AbstractFormDataProviders implements FormDataProvidersInterface
{

    public function execute(): array
    {
        if (empty($this->getSql())) {
            $this->formGenerator->setErrorMessage('SQL empty');
            return [];
        }
        return $this->getDb()::fetch($this->getDb()::query($this->getSql()));

    }
}
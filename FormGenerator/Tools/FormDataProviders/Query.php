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

    public function execute4multiple(): array
    {
        $query = $this->data['query'];
        $rows=[];
        foreach ($query as $item) {
            $rows[] = $item;
        }
        return $rows;
    }
}
<?php
/**
 * @author selcukmart
 * 5.02.2022
 * 13:46
 */

namespace FormGenerator\Tools\FormDataProviders;

use FormGenerator\FormGenerator;

abstract class AbstractFormDataProviders
{
    public function __construct(FormGenerator $formGenerator, array $generator_array)
    {
        $this->generator_array = $generator_array;
        $this->formGenerator = $formGenerator;
        $this->data = $this->generator_array['data'];
    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 12:15
     */
    protected function getTable()
    {
        return $this->data['table'];
    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 12:15
     */
    protected function getId()
    {
        return $this->data['id'];
    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 12:16
     */
    protected function getIdColumnName()
    {
        return $this->data['id_column_name'];
    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 12:16
     */
    protected function getSql()
    {
        return $this->data['sql'];
    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 13:30
     */
    protected function getDb()
    {
        return $this->formGenerator->getDb();
    }

    public function __destruct()
    {

    }
}
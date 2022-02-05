<?php
/**
 * Prepares rows
 * @author selcukmart
 * 24.01.2021
 * 16:56
 */

namespace FormGenerator\Tools;


use FormGenerator\FormGenerator;

class Row
{
    private
        $row,
        $formGenerator,
        $generator_array = [],
        $data = [],
        $err_message = '';

    public function __construct(FormGenerator $formGenerator, array $generator_array)
    {
        $this->generator_array = $generator_array;
        $this->formGenerator = $formGenerator;
    }


    public function setRow(): void
    {
        if (isset($this->generator_array['data']['query'])) {
            $this->data['query'] = $this->generator_array['data']['query'];
            $this->query();
        } elseif (isset($this->generator_array['data']['rows'])) {
            $this->row = $this->generator_array['data']['rows'];
        } elseif (isset($this->generator_array['data']['id'], $this->generator_array['data']['table'])) {
            $this->data['id'] = $this->generator_array['data']['id'];
            $this->data['id_column_name'] = $this->generator_array['data']['id_column_name'] ?? 'id';
            $this->data['table'] = $this->generator_array['data']['table'];
            $this->db();
        } elseif (isset($this->generator_array['data']['from'])) {
            $this->data = $this->generator_array['data'];
            $from = $this->data['from'];
            $this->{$from}();
        }
    }

    public function getOptionsSettings()
    {
        if(isset($this->generator_array['data']['settings'])){
            return $this->generator_array['data']['settings'];
        }
    }

    private function db()
    {
        if (!$this->getId()) {
            $this->err_message = 'ID empty';
            return;
        }

        $this->row = $this->formGenerator->getDb()::getRow($this->getIdColumnName(), $this->getId(), $this->getTable());
    }

    private function sql()
    {
        if (empty($this->getSql())) {
            $this->err_message = 'SQL empty';
            return;
        }
        $this->data['query'] = $this->formGenerator->getDb()::query($this->getSql());
        $this->query();
    }

    private function query()
    {
        if (empty($this->data['query'])) {
            $this->err_message = 'Query is empty';
            return;
        }
        foreach ($this->data['query'] as $datum) {
            $this->row[] = $datum;
        }
    }

    private function key_label_array()
    {
        foreach ($this->data['key_label_array'] as $index => $datum) {
            $this->row[] = [
                'key' => $index,
                'label' => $datum
            ];
        }
    }

    private function array()
    {
        $this->row = $this->data['array'];
    }

    /**
     * @return mixed
     */
    public function getRow()
    {
        return $this->row;
    }


    /**
     * @return string
     */
    public function getErrMessage(): string
    {
        return $this->err_message;
    }

    public function __destruct()
    {

    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 12:15
     */
    private function getTable()
    {
        return $this->data['table'];
    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 12:15
     */
    private function getId()
    {
        return $this->data['id'];
    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 12:16
     */
    private function getIdColumnName()
    {
        return $this->data['id_column_name'];
    }

    /**
     * @return mixed
     * @author selcukmart
     * 5.02.2022
     * 12:16
     */
    private function getSql()
    {
        return $this->data['sql'];
    }
}
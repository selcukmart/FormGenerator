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
        if (isset($this->generator_array['query'])) {
            $this->data['query'] = $this->generator_array['query'];
            $this->query();
        } elseif (isset($this->generator_array['rows'])) {
            $this->row = $this->generator_array['rows'];
        } elseif (isset($this->generator_array['data_id'], $this->generator_array['data_table'])) {
            $this->data['id'] = $this->generator_array['data_id'];
            $this->data['table'] = $this->generator_array['data_table'];
            $this->db();
        } elseif (isset($this->generator_array['data']['from'])) {
            $this->data = $this->generator_array['data'];
            $from = $this->data['from'];
            $this->{$from}();
        }
    }

    private function db()
    {
        $this->data['id'] = (int)$this->data['id'];
        if (!$this->data['id']) {
            $this->err_message = 'ID empty';
            return;
        }
        $this->row = $this->formGenerator->getDb()::getRow($this->data['id'], $this->data['table']);
    }

    private function sql()
    {
        if (empty($this->data['sql'])) {
            $this->err_message = 'SQL empty';
            return;
        }
        $this->data['query'] = $this->formGenerator->getDb()::query($this->data['sql']);
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

    private function key_value_array()
    {
        foreach ($this->data['key_value_array'] as $index => $datum) {
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
}
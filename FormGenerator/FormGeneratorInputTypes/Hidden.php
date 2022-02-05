<?php
/**
 * @author selcukmart
 * 24.01.2021
 * 11:37
 */

namespace FormGenerator\FormGeneratorInputTypes;


class Hidden extends AbstractInputTypes implements InputTypeInterface
{


    private
        $unit_parts = [],
        $default_generator_arr = [
        'default_value' => '',
        'attributes' => [
            'value' => '',
            'type' => 'hidden',
        ],
        'value_callback' => ''
    ];


    public function prepare(array $item): array
    {
        $this->item = $item;
        $this->item = defaults_form_generator($this->item, $this->default_generator_arr);
        $field = $this->field = $this->item['attributes']['name'];

        if (!isset($this->item['attributes']['id'])) {
            $this->item['attributes']['id'] = $this->item['attributes']['name'];
        }

        $row_table = $this->formGenerator->getRow();


        if (!empty($this->item['value_callback']) && is_callable($this->item['value_callback'])) {
            $this->item['attributes']['value'] = htmlspecialchars(call_user_func_array($this->item['value_callback'], [$row_table, $this->field]));
        } else {
            $this->item['attributes']['value'] = isset($row_table[$this->field]) ? htmlspecialchars($row_table[$this->field]) : '';
        }

        $this->setDefinedDefaultValue();
        $this->setDBDefaultValue($field);


        $input_dom_array = [
            'element' => 'input',
            'attributes' => $this->item['attributes'],
            'content' => ''
        ];
        $this->unit_parts = [
            'input' => $this->domExport($input_dom_array),
            'template' => 'HIDDEN'
        ];

        return $this->unit_parts;
    }


}
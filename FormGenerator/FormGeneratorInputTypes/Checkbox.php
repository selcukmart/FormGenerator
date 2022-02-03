<?php
/**
 * @author selcukmart
 * 24.01.2021
 * 11:37
 */

namespace FormGenerator\FormGeneratorInputTypes;


use FormGenerator\Tools\CheckedControl;
use FormGenerator\Tools\Label;
use FormGenerator\Tools\Row;

class Checkbox extends AbstractInputTypes implements InputTypeInterface
{


    private
        $default_generator_arr = [
        'default_value' => '',
        'attributes' => [
        ],
        'option_settings' => [
            'key' => 'key',
            'label' => 'label',

        ],
        'options' => '',
        'dont_set_id' => false,
        'value_callback' => ''
    ],

        $row_data = [],
        $row = [],
        $units_output = '',
        $option_settings,
        $field;


    public function prepare(array $item): array
    {
        $this->item = $item;
        $this->row_data = $this->item['options'];
        $this->item = defaults_form_generator($this->item, $this->default_generator_arr);
        $this->option_settings = $this->item['option_settings'];
        $field = $this->field = $this->item['attributes']['name'];

        $this->cleanIDInAttributesIfNecessary();

        $this->row_table = $this->formGenerator->getRowTable();

        if (isset($this->row_table[$this->field])) {
            $this->item['attributes']['value'] = $this->row_table[$this->field];
        }

        $this->setDefinedDefaultValue();
        $this->setDBDefaultValue($field);
        $this->setLabel();

        return [
            'input' => $this->checkboxGenerate(),
            'label' => $this->item['label'],
            'input_capsule_attributes' => '',
            'label_attributes' => ''
        ];
    }

    private function checkboxGenerate(): string
    {

        $row = new Row($this->formGenerator, $this->row_data);
        $row->setRow();
        $this->row = $row->getRow();
        $key = $this->option_settings['key'];
        $this->label = $this->option_settings['label'];

        $checked_control = isset($this->item['options']['control']) && is_array($this->item['options']['control']) ? new CheckedControl($this->item['options']['control'], $this->row_table) : false;
        foreach ($this->row as $option_row) {

            $id = $this->field . '-' . $option_row[$key];
            $attr = [
                'type' => 'checkbox',
                'value' => $option_row[$key],
                'id' => $id,
                'name' => $this->field . '[]'
            ];
            $attr['label'] = isset($option_row[$this->label]) ? $option_row[$this->label] : '';
            if ($checked_control) {
                $checked_control->control($option_row[$key]);
                if ($checked_control->isChecked()) {
                    $attr['checked'] = 'checked';
                }
            }

            if (isset($this->item['dependency']) && $this->item['dependency']) {
                $arr = [
                    'data-dependency' => 'true',
                    'data-dependency-group' => $this->field,
                    'data-dependency-field' => $id
                ];
                $attr = array_merge($attr, $arr);
            }

            $arr = [
                'element' => 'input',
                'attributes' => $attr,
                'content' => $this->label
            ];
            $this->units_output .= $this->domExport($arr);
        }

        return $this->units_output;
    }


}
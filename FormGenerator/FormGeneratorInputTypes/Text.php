<?php
/**
 * @author selcukmart
 * 24.01.2021
 * 11:37
 */

namespace FormGenerator\FormGeneratorInputTypes;


class Text extends AbstractInputTypes implements InputTypeInterface
{


    private
        $default_generator_arr = [
        'default_value' => '',
        'attributes' => [
            'value' => '',
            'type' => 'text',
            'class' => '',
            'placeholder' => ''
        ],
        'dont_set_id' => false,
        'value_callback' => ''
    ];


    public function prepare(array $item): array
    {
        $this->item = $item;
        $this->item = defaults_form_generator($this->item, $this->default_generator_arr);
        $field = $this->item['attributes']['name'];

        $this->cleanIDInAttributesIfNecessary();
        $row_table = $this->formGenerator->getRow();


        if (!empty($this->item['value_callback']) && is_callable($this->item['value_callback'])) {
            $this->item['attributes']['value'] = htmlspecialchars(call_user_func_array($this->item['value_callback'], [$row_table, $field]));
        } elseif (isset($row_table[$field])) {
            $this->item['attributes']['value'] = htmlspecialchars($row_table[$field]);
        }

        $this->setDefinedDefaultValue();
        $this->setDBDefaultValue($field);
        $this->setLabel();

        if (empty($this->item['attributes']['placeholder'])) {
            $this->item['attributes']['placeholder'] = $this->label->getLabelWithoutHelp();
        }

        $input_dom_array = [
            'element' => 'input',
            'attributes' => $this->item['attributes'],
            'content' => ''
        ];


        return [
            'input' => $this->domExport($input_dom_array),
            'label' => $this->item['label'],
            'input_capsule_attributes' => '',
            'label_attributes' => ''
        ];
    }




}
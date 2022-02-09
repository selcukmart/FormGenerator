<?php
/**
 * @author selcukmart
 * 24.01.2021
 * 14:52
 */

namespace FormGenerator\FormGeneratorInputTypes;


class ButtonGroup extends AbstractInputTypes implements InputTypeInterface
{
    private
        $default_generator_arr = [
        'default_value' => '',
        'attributes' => [
            'value' => '',
            'class' => '',
            'placeholder' => ''
        ],
        'dont_set_id' => false,
        'value_callback' => ''
    ];


    public function createInput(array $items): array
    {
        $inputs = '';
        foreach ($items as $item) {
            $button = Button::getInstance($this->formGenerator);
            $inputs .= $button->createInput($item);
        }
        $input_dom_array = [
            'element' => 'input',
            'attributes' => $items['attributes'],
            'content' => $inputs
        ];

        /**
         * For encapsulation div or etc...
         */
        return [
            'input' => $this->toHtml($input_dom_array),
            'label' => $items['label'],
            'input_capsule_attributes' => '',
            'label_attributes' => ''
        ];
    }

}
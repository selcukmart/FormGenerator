<?php
/**
 * @author selcukmart
 * 25.01.2021
 * 20:26
 */

namespace FormGenerator\FormGeneratorInputTypes;



class Form extends AbstractInputTypes implements InputTypeInterface
{

    public function createInput(array $item):array
    {
        $this->item = $item;
        $this->setLabel();
        $input_dom_array = [
            'element' => 'form',
            'attributes' => $this->item['attributes'],
            'content' => ''
        ];

        return [
            'input' => $this->toHtml($input_dom_array),
            'label' => $this->item['label'],
            'input_capsule_attributes' => '',
            'label_attributes' => ''
        ];
    }
    
}
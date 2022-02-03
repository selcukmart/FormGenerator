<?php
/**
 * @author selcukmart
 * 25.01.2021
 * 20:26
 */

namespace FormGenerator\FormGeneratorInputTypes;


use FormGenerator\Tools\Label;

class Image extends AbstractInputTypes implements InputTypeInterface
{


    public function prepare(array $item):array
    {
        $this->item = $item;
        $this->field = $this->item['attributes']['name'];
        $this->row_table = $this->formGenerator->getRowTable();
        $this->setLabel();
        $this->item['attributes']['src'] = $this->row_table[$this->field];
        $this->unit_parts = [
            'input' => $this->formGenerator->render($this->item['attributes'],'IMAGE',true),
            'label' => $this->item['label'],
            'input_capsule_attributes' => '',
            'label_attributes' => ''
        ];
        return $this->unit_parts;
    }

    

    
}
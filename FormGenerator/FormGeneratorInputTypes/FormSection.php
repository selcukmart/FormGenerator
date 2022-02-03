<?php
/**
 * @author selcukmart
 * 27.01.2021
 * 22:59
 */

namespace FormGenerator\FormGeneratorInputTypes;


use FormGenerator\Tools\Label;

class FormSection extends AbstractInputTypes implements InputTypeInterface
{


    public function prepare(array $item):array
    {
        $this->item = $item;
        $this->item ['template'] = 'FORM_SECTION';
        $this->setLabel();
        return $this->item;
    }

}
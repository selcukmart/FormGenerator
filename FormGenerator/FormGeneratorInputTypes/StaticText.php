<?php
/**
 * @author selcukmart
 * 27.01.2021
 * 23:19
 */

namespace FormGenerator\FormGeneratorInputTypes;


use Helpers\Template;

class StaticText extends AbstractInputTypes implements InputTypeInterface
{
    

    

    public function createInput(array $item):array
    {
        $this->item = $item;
        $item ['template'] = 'STATIC_TEXT';
        $row_table = $this->formGeneratorDirector->getRow();
        if (isset($item['content_callback']) && is_callable($item['content_callback'])) {
            $item['content'] = call_user_func_array($item['content_callback'], [$row_table, $item]);
        } else {
            $item['content'] = Template::smarty($row_table, $item['content']);
        }

        $this->unit_parts = $item;

        return $this->unit_parts;
    }

    

    
}
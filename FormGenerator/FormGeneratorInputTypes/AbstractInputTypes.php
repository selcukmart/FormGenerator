<?php
/**
 * @author selcukmart
 * 2.02.2022
 * 13:52
 */

namespace FormGenerator\FormGeneratorInputTypes;

use FormGenerator\FormGenerator;
use FormGenerator\Tools\DefaultValue;
use FormGenerator\Tools\Label;
use Helpers\Dom;

abstract class AbstractInputTypes
{
    protected
        $item,
        $label,
        $formGenerator,
        $unnecessary_attributes = [
        'content',
        'label'
    ];

    public function __construct(FormGenerator $formGenerator)
    {
        $this->formGenerator = $formGenerator;
    }

    protected function domExport($input_dom_array, $export_type = null)
    {
        $export_type = is_null($export_type) ? strtoupper($this->item['type']) : $export_type;
        $result = $this->formGenerator->render($input_dom_array['attributes'], $export_type, true);

        if (!$result) {
            $input_dom_array = $this->clearUnnecessaryAttributes($input_dom_array);
            $result = Dom::generator($input_dom_array);
        }
        return $result;
    }

    public function getUnitParts(): array
    {
        return $this->unit_parts;
    }

    /**
     * @return bool
     * @author selcukmart
     * 2.02.2022
     * 14:17
     */
    protected function issetDefaultValue(): bool
    {

        return isset($this->item['default_value']) && $this->item['default_value'] !== '';
    }

    public function __destruct()
    {

    }

    /**
     * @param $input_dom_array
     * @return mixed
     * @author selcukmart
     * 2.02.2022
     * 17:19
     */
    protected function clearUnnecessaryAttributes($input_dom_array)
    {
        foreach ($this->unnecessary_attributes as $unnecessary_attribute) {
            if (isset($input_dom_array['attributes'][$unnecessary_attribute])) {
                unset($input_dom_array['attributes'][$unnecessary_attribute]);
            }
        }

        return $input_dom_array;
    }
    /**
     * @param $field
     * @author selcukmart
     * 3.02.2022
     * 15:21
     */
    protected function setDBDefaultValue($field): void
    {
        if (empty($this->item['attributes']['value'])) {
            $default_value = new DefaultValue($this->formGenerator, $field);
            $this->item['attributes']['value'] = $default_value->get();
        }
    }

    protected function setDefinedDefaultValue(): void
    {
        if (empty($this->item['attributes']['value']) && $this->issetDefaultValue()) {
            $this->item['attributes']['value'] = $this->item['default_value'];
        }
    }

    protected function setLabel(): void
    {
        $this->label = new Label($this->item);
        $this->item['label'] = $this->label->getLabel();
    }

    protected function cleanIDInAttributesIfNecessary(): void
    {
        if (!isset($this->item['attributes']['id'])) {
            if (!$this->item['dont_set_id']) {
                $this->item['attributes']['id'] = $this->item['attributes']['name'];
            }
        } elseif ($this->item['dont_set_id']) {
            unset($this->item['attributes']['id']);
        }
    }

    protected function addPlaceholderFromLabel(): void
    {
        if (empty($this->item['attributes']['placeholder'])) {
            $this->item['attributes']['placeholder'] = $this->label->getLabelWithoutHelp();
        }
    }

}
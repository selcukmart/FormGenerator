<?php
/**
 * @pattern singleton and factory
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
    private static $instances = [];
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

    public static function getInstance(FormGenerator $formGenerator): AbstractInputTypes
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static($formGenerator);
        }

        return self::$instances[$cls];
    }

    protected function toHtml($input_dom_array, $export_type = null)
    {
        $export_type = is_null($export_type) ? strtoupper($this->item['type']) : $export_type;
        $result = $this->formGenerator->renderToHtml($input_dom_array['attributes'], $export_type, true);
        if (!$result) {
            $input_dom_array = $this->clearUnnecessaryAttributes($input_dom_array);
            $result = Dom::htmlGenerator($input_dom_array);
        }
        return $result;
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
            $this->item['dont_set_id'] = $this->item['dont_set_id'] ?? false;
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

    /**
     * @param $row_table
     * @param $field
     * @author selcukmart
     * 5.02.2022
     * 16:22
     */
    protected function valueCallback($row_table, $field): void
    {
        if (!empty($this->item['value_callback']) && is_callable($this->item['value_callback'])) {
            $this->item['attributes']['value'] = htmlspecialchars(call_user_func_array($this->item['value_callback'], [$row_table, $field]));
        } elseif (isset($row_table[$field])) {
            $this->item['attributes']['value'] = htmlspecialchars($row_table[$field]);
        }
    }

}
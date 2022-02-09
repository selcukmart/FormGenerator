<?php
/**
 * @author selcukmart
 * 30.01.2021
 * 17:33
 */

namespace FormGenerator\FormGeneratorBuilder;


class Bootstrapv3FormWizardBuilder extends AbstractFormGeneratorBuilder implements BuilderInterface
{

    private
        $sections = [],
        $tab_contents=[],
        $is_string_group;

    public function buildHtmlOutput($inputs = null, $parent_group = null):void
    {
        if (is_null($inputs)) {
            $inputs = $this->formGenerator->getInputs();
        }

        foreach ($inputs as $group => $item) {
            if (!is_null($parent_group)) {
                $item['group'] = $parent_group;
            }
            $item['input-id'] = $this->formGenerator->inputID($item);
            $will_filtered = $this->filter->willFiltered($item, $group);
            if ($will_filtered) {
                continue;
            }
            $this->is_string_group = !is_numeric($group) && is_string($group);
            if ($this->is_string_group && !isset($this->sections[$group])) {
                $group_label = '';
                if (isset($item[0]['label'])) {
                    $group_label = $item[0]['label'];
                }
                $this->sections[$group] = $group_label;
            }

            $this->sendDataForRender($item, $group);
        }
    }


    protected function sendDataForRender($item, $group):void
    {
        if ($this->is_string_group) {
            unset($item['input-id']);
            $this->buildHtmlOutput($item, $group);
            return;
        }

        if ($item['type'] === 'form_section') {
            return;
        }

        $this->prepareInputParts($item);
        if (!isset($this->tab_contents[$item['group']])) {
            $this->tab_contents[$item['group']] = [];
        }

        $this->tab_contents[$item['group']][] = $this->formGenerator->renderToHtml($this->input_parts, 'TEMPLATE');
    }

}
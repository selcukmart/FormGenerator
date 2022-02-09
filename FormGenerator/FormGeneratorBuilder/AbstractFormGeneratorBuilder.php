<?php


namespace FormGenerator\FormGeneratorBuilder;


use FormGenerator\FormGeneratorDirector;
use Helpers\Dom;
use FormGenerator\Tools\DependencyManagerV1;
use Helpers\Classes;

abstract class AbstractFormGeneratorBuilder
{
    private static
        $instances = [],
        $classnames = [];

    protected
        $formGenerator,
        $class_names,
        $input_parts,
        $template,
        $filter,
        $without_help_block = [
        'hidden',
        'static_text',
        'file'
    ];

    public function __construct(FormGeneratorDirector $formGenerator)
    {
        $this->formGenerator = $formGenerator;
        $this->class_names = [];
        $this->filter = $this->formGenerator->getFilter();

    }

    public static function getInstance(FormGeneratorDirector $formGenerator): AbstractFormGeneratorBuilder
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static($formGenerator);
        }

        return self::$instances[$class];
    }

    public function createHtmlOutput($inputs = null, $parent_group = null): void
    {
        if (is_null($inputs)) {
            $inputs = $this->formGenerator->getInputs();
        }

        foreach ($inputs as $group => $input) {
            if (!is_null($parent_group)) {
                $input['group'] = $parent_group;
            }

            if ($this->isExceptionalSituation($input)) {
                continue;
            }

            $input['input-id'] = $this->formGenerator->inputID($input);
            $will_filtered = $this->filter->willFiltered($input, $group);
            if ($will_filtered) {
                continue;
            }
            $this->sendDataForRender($input, $group);
        }
    }

    public function createForm($item)
    {
        $this->sendDataForRender($item, '');
    }

    protected function getHelpBlock(array $item): string
    {
        $str = '';
        if (isset($item['help_block']) && !empty($item['help_block']) && !in_array($item['type'], $this->without_help_block, true)) {
            $str = $this->formGenerator->renderToHtml($item, 'HELP_BLOCK', true);
        }
        return $str;

    }

    protected function prepareInputParts(array $item): array
    {
        $input_factory_class = $this->getInputFactoryClassName($item['type']);
        $input_factory = $input_factory_class::getInstance($this->formGenerator);
        $this->input_parts = $input_factory->createInput($item);
        if (isset($item['type']) && $this->isForm($item['type'])) {
            $this->addHelpBlockToInputparts($item);
            $this->addInputCapsuleAttributes2InputParts($item);
        }

        return $this->input_parts;
    }


    protected function sendDataForRender($item, $group): void
    {
        if (!empty($group) && !is_numeric($group) && is_string($group)) {
            unset($item['input-id']);
            $this->createHtmlOutput($item, $group);
            return;
        }

        $this->prepareInputParts($item);
        $input_capsule = 'TEMPLATE';
        if (!(isset($item['type']) && $this->isForm($item['type']))) {
            $input_capsule = 'FORM_CAPSULE';
        }

        $this->formGenerator->renderToHtml($this->input_parts, $input_capsule);
    }

    /**
     * @param $type
     * @return string
     * @author selcukmart
     * 8.02.2022
     * 10:57
     */
    protected function getInputFactoryClassName($type): string
    {
        if (isset(self::$classnames[$type])) {
            return self::$classnames[$type];
        }
        if (isset($this->class_names[$type])) {
            $class_name = $this->class_names[$type];
        } else {
            $class_name = Classes::prepareFromString($type);
            $this->class_names[$type] = $class_name;
        }

        $input_factory_class = $this->formGenerator->getInputTypesNamespace() . $class_name;

        if (!class_exists($input_factory_class)) {
            $input_factory_class = $this->formGenerator->getInputTypesNamespace() . 'Generic';
        }
        self::$classnames[$type] = $input_factory_class;
        return $input_factory_class;
    }

    /**
     * @param array $item
     * @author selcukmart
     * 8.02.2022
     * 10:59
     */
    protected function addHelpBlockToInputparts(array $item): void
    {
        $help_block = $this->getHelpBlock($item);
        if (!isset($this->input_parts['input_belove_desc'])) {
            $this->input_parts['input_belove_desc'] = '';
        }

        $this->input_parts['input_belove_desc'] .= $help_block;
    }

    /**
     * @param array $item
     * @author selcukmart
     * 8.02.2022
     * 11:00
     */
    protected function addInputCapsuleAttributes2InputParts(array $item): void
    {
        if (!isset($this->input_parts['input_capsule_attributes'])) {
            $this->input_parts['input_capsule_attributes'] = '';
        }

        $this->input_parts['input_capsule_attributes'] .= DependencyManagerV1::dependend($item);
        $arr = [
            'attributes' => [
                'id' => $item['input-id']
            ]
        ];

        $this->input_parts['input_capsule_attributes'] .= Dom::makeAttr($arr);
    }

    /**
     * @param $item
     * @return bool
     * @author selcukmart
     * 8.02.2022
     * 11:02
     */
    protected function isExceptionalSituation($item): bool
    {
        return isset($item['type'], $item['label']) && $item['type'] === 'form_section' && empty($item['label']);
    }

    public function __destruct()
    {

    }

    /**
     * @param $type
     * @return bool
     * @author selcukmart
     * 8.02.2022
     * 16:53
     */
    protected function isForm($type): bool
    {
        return $type !== 'form';
    }
}
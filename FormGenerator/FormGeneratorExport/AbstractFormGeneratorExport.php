<?php


namespace FormGenerator\FormGeneratorExport;


use FormGenerator\FormGenerator;
use FormGenerator\Render\RenderEngines\AbstractRenderEngines;
use Helpers\Dom;
use FormGenerator\Tools\DependencyManagerV1;
use Helpers\Classes;

abstract class AbstractFormGeneratorExport
{
    private static $instances = [];

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

    public function __construct(FormGenerator $formGenerator)
    {
        $this->formGenerator = $formGenerator;
        $this->class_names = [];
        $this->filter = $this->formGenerator->getFilter();

    }

    public static function getInstance(FormGenerator $formGenerator): AbstractFormGeneratorExport
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static($formGenerator);
        }

        return self::$instances[$cls];
    }

    public function extract($items = null, $parent_group = null): void
    {
        if (is_null($items)) {
            $items = $this->formGenerator->getInputs();
        }
        foreach ($items as $group => $item) {
            if (!is_null($parent_group)) {
                $item['group'] = $parent_group;
            }

            if (isset($item['type'], $item['label']) && $item['type'] === 'form_section' && empty($item['label'])) {
                continue;
            }

            $item['input-id'] = $this->formGenerator->inputID($item);
            $will_filtered = $this->filter->willFiltered($item, $group);
            if ($will_filtered) {
                continue;
            }
            $this->extractCore($item, $group);
        }
    }

    protected function getHelpBlock(array $item): string
    {
        $str = '';
        if (isset($item['help_block']) && !empty($item['help_block']) && !in_array($item['type'], $this->without_help_block, true)) {
            $item['help_block'] = ___($item['help_block']);
            $str = $this->formGenerator->export($item, 'HELP_BLOCK', true);
        }
        return $str;

    }

    protected function prepareInputParts(array $item): array
    {
        if (isset($this->class_names[$item['type']])) {
            $class_name = $this->class_names[$item['type']];
        } else {
            $class_name = Classes::prepareFromString($item['type']);
            $this->class_names[$item['type']] = $class_name;
        }

        $class = $this->formGenerator->getInputTypesNamespace() . $class_name;

        if(!class_exists($class)){
            $class = $this->formGenerator->getInputTypesNamespace() .'Generic';
        }

        $run = $class::getInstance($this->formGenerator);

        $this->input_parts = $run->prepare($item);
        $help_block = $this->getHelpBlock($item);
        if (!isset($this->input_parts['input_belove_desc'])) {
            $this->input_parts['input_belove_desc'] = '';
        }

        $this->input_parts['input_belove_desc'] .= $help_block;

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

        return $this->input_parts;
    }

    protected function prepareTemplate(): string
    {
        if (isset($this->input_parts['template'])) {
            $this->template = $this->input_parts['template'];
        } else {
            $this->template = 'TEMPLATE';
        }

        return $this->template;
    }

    protected function extractCore($item, $group): void
    {
        if (!is_numeric($group) && is_string($group)) {
            unset($item['input-id']);
            $this->extract($item, $group);
            return;
        }

        $this->prepareInputParts($item);
        $this->prepareTemplate();

        $this->formGenerator->render($this->input_parts, $this->template);
    }

    public function __destruct()
    {

    }
}
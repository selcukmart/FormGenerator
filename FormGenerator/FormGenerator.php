<?php
/**
 * @author selcukmart
 * 24.01.2021
 * 11:38
 */

namespace FormGenerator;


use FormGenerator\Tools\Filter;
use FormGenerator\Tools\Row;
use GlobalTraits\ResultsTrait;
use Smarty;

class FormGenerator
{
    use ResultsTrait;

    protected
        // twig, mustache, blade
        $generator_array = [],
        $render_object,
        $render_object_by = 'smarty',
        $output = '',
        $error_message = '',
        $message = '',
        $export_format,
        $export_type,
        $row_table,
        $inputs,
        $has_filter = false,
        $input_filter = [],
        $input_excluding_filter = [],
        $input_group_filter = [],
        $input_group_excluding_filter = [],
        $scope,
        $filter,
        $namespace,
        $table,
        $db,
        $id,
        $id_column_name,
        $input_types_namespace;


    public function __construct(array $generator_array, $scope)
    {
        $this->scope = $scope;
        $this->generator_array = $generator_array;
        $this->inputs = $this->generator_array['inputs'];
        $this->setRowTable();
        $this->setRenderObjectDetails();
        $this->export_format = $this->generator_array['export']['format'];
        $this->export_type = $this->generator_array['export']['type'];
        $this->filterTask();
        $this->namespace = __NAMESPACE__;
        $this->setInputTypesFolderNamespace();
        $this->setDB();
        $this->databaseVariables();
    }


    public function setRowTable(): void
    {
        $row_table_detection = new Row($this, $this->generator_array);
        $row_table_detection->setRow();
        $this->row_table = $row_table_detection->getRow();
        if (empty($this->row_table)) {
            global $row_table;
            if ($row_table) {
                $this->row_table = $row_table;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getRowTable()
    {
        return $this->row_table;
    }

    public function extract(): void
    {
        if (!is_object($this->render_object)) {
            $this->setErrorMessage('Render Object not found');
            return;
        }
        $class = $this->getFormGeneratorExportClassName();


        $run = new $class($this);

        $run->extract();
    }

    public function render(array $input_parts, $template = 'TEMPLATE', $return = false)
    {
        $class = $this->namespace . '\Render\\Render';
        $run = new $class($this);
        $run->setInputParts($input_parts);

        return $run->render($template, $return);
    }

    public function inputID($item)
    {
        if (isset($item['attributes']['name'])) {
            return 'input-' . $item['attributes']['name'];
        }

        if (isset($item['label'])) {
            return 'input-' . form_generator_slug($item['label']);
        }
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->error_message;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output): void
    {
        $this->output .= $output;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param string $error_message
     */
    public function setErrorMessage(string $error_message): void
    {
        $this->setResult(false);
        $error_message = ' - ' . $error_message . '<br>';
        $this->error_message .= $error_message;
    }

    /**
     * @return mixed|string
     */
    public function getExportFormat(): string
    {
        return $this->export_format;
    }


    /**
     * @return mixed
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * @param bool $has_filter
     */
    public function setHasFilter(bool $has_filter): void
    {
        $this->has_filter = $has_filter;
    }

    /**
     * @param array $input_excluding_filter
     */
    public function setInputExcludingFilter(array $input_excluding_filter): void
    {
        $this->input_excluding_filter = $input_excluding_filter;
    }

    /**
     * @param array $input_filter
     */
    public function setInputFilter(array $input_filter): void
    {
        $this->input_filter = $input_filter;
    }

    /**
     * @param array $input_group_excluding_filter
     */
    public function setInputGroupExcludingFilter(array $input_group_excluding_filter): void
    {
        $this->input_group_excluding_filter = $input_group_excluding_filter;
    }

    /**
     * @param array $input_group_filter
     */
    public function setInputGroupFilter(array $input_group_filter): void
    {
        $this->input_group_filter = $input_group_filter;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return array
     */
    public function getInputExcludingFilter(): array
    {
        return $this->input_excluding_filter;
    }

    /**
     * @return array
     */
    public function getInputFilter(): array
    {
        return $this->input_filter;
    }

    /**
     * @return array
     */
    public function getGeneratorArray(): array
    {
        return $this->generator_array;
    }

    /**
     * @return array
     */
    public function getInputGroupExcludingFilter(): array
    {
        return $this->input_group_excluding_filter;
    }

    /**
     * @return array
     */
    public function getInputGroupFilter(): array
    {
        return $this->input_group_filter;
    }

    /**
     * @return mixed
     */
    public function getExportType()
    {
        return $this->export_type;
    }

    /**
     * @return bool
     */
    public function isHasFilter(): bool
    {
        return $this->has_filter;
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @return string
     */
    public function getInputTypesNamespace(): string
    {
        return $this->input_types_namespace;
    }

    /**
     * @return mixed
     */
    public function getRenderobject()
    {
        return $this->render_object;
    }

    /**
     * @param mixed $smarty
     */
    public function setRenderObject(Smarty $smarty): void
    {
        $this->render_object = $smarty;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getRenderObjectBy()
    {
        return $this->render_object_by;
    }

    public function __destruct()
    {

    }

    protected function setRenderObjectDetails(): void
    {
        if (isset($this->generator_array['export']['render']['by'], $this->generator_array[$this->generator_array['export']['render']['by']]) && is_string($this->generator_array['export']['render']['by']) && !empty($this->generator_array['export']['render']['by']) && is_object($this->generator_array[$this->generator_array['export']['render']['by']])) {
            $this->setRenderObject($this->generator_array[$this->generator_array['export']['render']['by']]);
        } else {
            $smarty = new Smarty();
            $smarty->setTemplateDir(__DIR__ . '/../SMARTY_TPL_FILES');
            $smarty->setCompileDir(__DIR__ . '/../SMARTY_TPL_FILES/template_compile');
            $smarty->setCacheDir(__DIR__ . '/../SMARTY_TPL_FILES/template_cache');
            $this->setRenderObject($smarty);
        }
    }

    private function setInputTypesFolderNamespace(): void
    {

        if (isset($this->generator_array['input-types']['namespace']) && !empty($this->generator_array['input-types']['namespace'])) {
            $namespace = $this->generator_array['input-types']['namespace'];
        } else {
            $namespace = $this->namespace;
        }
        $this->input_types_namespace = $namespace . '\FormGeneratorInputTypes\\';
    }

    /**
     * @return string
     * @author selcukmart
     * 3.02.2022
     * 13:38
     */
    private function getFormGeneratorExportClassName(): string
    {
        if (isset($this->generator_array['export-object']['namespace']) && !empty($this->generator_array['export-object']['namespace'])) {
            $namespace = $this->generator_array['export-object']['namespace'];
        } else {
            $namespace = $this->namespace;
        }

        $class = $namespace . '\FormGeneratorExport\\' . $this->export_format;
        if (!class_exists($class)) {
            $class = $namespace . '\FormGeneratorExport\\Generic';
        }
        return $class;
    }

    private function filterTask(): void
    {
        $this->filter = new Filter($this);
        $this->filter->hasFilter();
    }

    private function setDB(): void
    {
        if (isset($this->generator_array['data']['connection']['db']['object']) && is_object($this->generator_array['data']['connection']['db']['object'])) {
            $this->db = $this->generator_array['data']['connection']['db']['object'];
            $this->generator_array['data']['from'] = 'db';
        }
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    private function databaseVariables(): void
    {
        $this->table = $this->generator_array['data']['table'] ?? '';
        $this->id = $this->generator_array['data']['id'] ?? '';
        $this->id_column_name = $this->generator_array['data']['id_column_name'] ?? 'id';
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIdColumnName()
    {
        return $this->id_column_name;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }
}
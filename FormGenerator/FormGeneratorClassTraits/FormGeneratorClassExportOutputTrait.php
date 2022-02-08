<?php
/**
 * @author selcukmart
 * 8.02.2022
 * 10:02
 */

namespace FormGenerator\FormGeneratorClassTraits;

trait FormGeneratorClassExportOutputTrait
{
    protected
        $html_output = '',
        $export_format,
        $export_type;

    public function createHtmlOutput(): void
    {
        if (!is_object($this->render_object)) {
            $this->setErrorMessage('Render Object not found');
            return;
        }
        $factory_class = $this->getFormGeneratorExportClassName();
        $factory = $factory_class::getInstance($this);
        $factory->createHtmlOutput();

        if (isset($this->generator_array['form'])) {
            $this->generator_array['form']['attributes']['inputs'] = $this->getHtmlOutput();
            $this->removeOutput();
            $this->generator_array['form']['type'] = 'form';
            $this->generator_array['form']['input-id'] = $this->generator_array['form']['id'] ?? '';
            $factory->createForm($this->generator_array['form']);
        }
    }

    /**
     * @return string
     * @author selcukmart
     * 3.02.2022
     * 13:38
     */
    private function getFormGeneratorExportClassName(): string
    {
        if ($this->hasUserDefinedHtmlExportObjects()) {
            $namespace = $this->getUserDefinedExportNamespace();
        } else {
            $namespace = $this->namespace;
        }

        $class = $namespace . '\FormGeneratorExport\\' . $this->export_format;
        if (!class_exists($class)) {
            $class = $namespace . '\FormGeneratorExport\\Generic';
        }
        return $class;
    }

    /**
     * @return string
     */
    public function getHtmlOutput(): string
    {
        return $this->html_output;
    }

    /**
     * @param string $output
     */
    public function mergeOutputAsString(string $output): void
    {
        $this->html_output .= $output;

    }

    public function removeOutput()
    {
        $this->html_output = '';
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
    public function getExportType()
    {
        return $this->export_type;
    }

    public function setExportFormat(): void
    {
        $this->export_format = $this->generator_array['export']['format'];
    }

    public function setExportType(): void
    {
        $this->export_type = $this->generator_array['export']['type'];
    }

    /**
     * @return bool
     * @author selcukmart
     * 8.02.2022
     * 11:55
     */
    private function hasUserDefinedHtmlExportObjects(): bool
    {
        return isset($this->generator_array['export-object']['namespace']) && !empty($this->generator_array['export-object']['namespace']);
    }

    /**
     * @return mixed
     * @author selcukmart
     * 8.02.2022
     * 11:56
     */
    private function getUserDefinedExportNamespace()
    {
        return $this->generator_array['export-object']['namespace'];
    }
}
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
        $output = '',
        $export_format,
        $export_type;

    public function extract(): void
    {
        if (!is_object($this->render_object)) {
            $this->setErrorMessage('Render Object not found');
            return;
        }
        $class = $this->getFormGeneratorExportClassName();
        $run = $class::getInstance($this);
        $run->createOutput();
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
}
<?php
/**
 * @author selcukmart
 * 9.02.2022
 * 10:49
 */

namespace tests\InputTypes;


use FormGenerator\FormGeneratorDirector;
use PHPUnit\Framework\TestCase;

class TestInputTypesOutput extends TestCase
{
    public function test()
    {
        $any_data = '<div class="abc">Any Data, Input to here etc</div>';
        $form_generator_array = [
            /**
             * Optional
             * Form Inputs
             */
            'inputs' => [
                'decision' => [
                    // this is a form input row
                    [
                        'type' => 'output',
                        'output' => $any_data,
                        'label' => 'Any Data',
                        'capsule_template' => 'SIMPLE',
                    ],
                ]
            ]
        ];
        $form_generator = new FormGeneratorDirector($form_generator_array, 'edit');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = $any_data;
        $this->assertSame($expected, $html);
    }
}
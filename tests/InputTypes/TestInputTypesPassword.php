<?php
/**
 * @author selcukmart
 * 9.02.2022
 * 10:49
 */

namespace tests\InputTypes;


use FormGenerator\FormGeneratorDirector;
use PHPUnit\Framework\TestCase;

class TestInputTypesPassword extends TestCase
{
    public function test()
    {
        $form_generator_array = [
            /**
             * Optional
             * Form Inputs
             */
            'inputs' => [
                'decision' => [
                    // this is a form input row
                    [
                        'type' => 'password',
                        'attributes' => [
                            'name' => 'password',
                        ],
                        'capsule_template' => 'SIMPLE',
                    ],
                ]
            ]
        ];
        $form_generator = new FormGeneratorDirector($form_generator_array, 'edit');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<input name="password" value="" type="password" class="" placeholder="Password" __is_def="1" id="password" >';
        $this->assertSame($expected, $html);
    }
}

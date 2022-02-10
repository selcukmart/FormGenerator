<?php
/**
 * @author selcukmart
 * 9.02.2022
 * 10:49
 */

namespace tests\InputTypes;


use FormGenerator\FormGeneratorDirector;
use PHPUnit\Framework\TestCase;

class TestInputTypesHidden extends TestCase
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
                        'type' => 'hidden',
                        /**
                         * tpl filename
                         */
                        'capsule_template' => 'SIMPLE',
                        'attributes' => [
                            'name' => 'address_identification',
                        ]
                    ],
                ]
            ]
        ];
        $form_generator = new FormGeneratorDirector($form_generator_array, 'add');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<input id="address_identification" name="address_identification" value="" type="hidden">';
        $this->assertSame($expected, $html);
    }
}

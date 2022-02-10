<?php
/**
 * @author selcukmart
 * 9.02.2022
 * 10:49
 */

namespace Tests\InputTypes\ScopeEdit\OtherInputTypes;


use FormGenerator\FormGeneratorDirector;
use PHPUnit\Framework\TestCase;
use Tests\InputTypes\ScopeEdit\FormDataAsRow;

class TestInputTypesTel extends TestCase
{
    public function test()
    {
        $type = 'tel';
        $form_generator_array = [
            'data' => [
                'row' => FormDataAsRow::getData(),
            ],
            /**
             * Optional
             * Form Inputs
             */
            'inputs' => [
                'decision' => [
                    // this is a form input row
                    [
                        'type' => $type,
                        /**
                         * tpl filename
                         */
                        'capsule_template' => 'SIMPLE',
                        'attributes' => [
                            'name' => $type,
                        ]
                    ],
                ]
            ]
        ];
        $form_generator = new FormGeneratorDirector($form_generator_array, 'edit');
        $form_generator->buildHtmlOutput();
        $html = $form_generator->getHtmlOutput();
        $expected = '<input name="tel" value="+905542856789" class="" placeholder="Tel" __is_def="1" type="tel" id="tel" >';
        $this->assertSame($expected, $html);
    }
}

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

class TestInputTypesTime extends TestCase
{
    public function test()
    {
        $type = 'time';
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
        $expected = '<input name="time" value="15:30" class="" placeholder="Time" __is_def="1" type="time" id="time" >';
        $this->assertSame($expected, $html);
    }
}

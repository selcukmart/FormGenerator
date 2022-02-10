<?php
/**
 * @author selcukmart
 * 9.02.2022
 * 10:49
 */

namespace Tests\InputTypes\ScopeEdit;


use Examples\DBExamples\Libraries\Database\DB;
use FormGenerator\FormGeneratorDirector;
use PHPUnit\Framework\TestCase;

class TestInputTypesRadio extends TestCase
{
    public function testKeyValueArray()
    {
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
                    [
                        // this is a form input row
                        'type' => 'radio',
                        'capsule_template' => 'SIMPLE',
                        'attributes' => [
                            'name' => 'country'
                        ],
                        'label' => 'Countries',
                        'options' => [
                            'data' => [
                                'from' => 'key_label_array',
                                'key_label_array' => [
                                    'us' => 'USA',
                                    'gb' => 'United Kingdom',
                                    'de' => 'Germany'
                                ],
                            ],
                        ]
                    ],
                ]
            ]
        ];
        $form_generator = new FormGeneratorDirector($form_generator_array, 'edit');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<label for="country-us"> <input type="radio" value="us" id="country-us" name="country" checked >USA</label><br>        <label for="country-gb"> <input type="radio" value="gb" id="country-gb" name="country"  >United Kingdom</label><br>        <label for="country-de"> <input type="radio" value="de" id="country-de" name="country"  >Germany</label><br>';
        $this->assertSame($expected, $html);
    }

    public function testRows()
    {
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
                    [
                        // this is a form input row
                        'type' => 'radio',
                        'capsule_template' => 'SIMPLE',
                        'attributes' => [
                            'name' => 'iso'
                        ],
                        'label' => 'Nationalities',
                        'options' => [
                            'data' => [
                                'settings' => [
                                    'key' => 'iso',
                                    'label' => 'name',
                                ],
                                'rows' => [
                                    [
                                        'iso' => 'gb',
                                        'name' => 'UK'
                                    ],
                                    [
                                        'iso' => 'us',
                                        'name' => 'USA'
                                    ],
                                    [
                                        'iso' => 'de',
                                        'name' => 'Germany'
                                    ]
                                ],
                            ],
                        ]
                    ],
                ]
            ]
        ];
        $form_generator = new FormGeneratorDirector($form_generator_array, 'edit');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<label for="iso-gb"> <input type="radio" value="gb" id="iso-gb" name="iso"  >UK</label><br>        <label for="iso-us"> <input type="radio" value="us" id="iso-us" name="iso"  >USA</label><br>        <label for="iso-de"> <input type="radio" value="de" id="iso-de" name="iso"  >Germany</label><br>';
        $this->assertSame($expected, $html);
    }

    public function testQuery()
    {
        require_once __DIR__ . '/../../Examples/DBExamples/Config/Db.php';
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
                    [
                        // this is a form input row
                        'type' => 'radio',
                        'capsule_template' => 'SIMPLE',
                        'attributes' => [
                            'name' => 'iso'
                        ],
                        'label' => 'Nationalities',
                        'options' => [
                            'data' => [
                                'from' => 'query',
                                'query' => DB::query("select * from countries"),
                                'settings' => [
                                    'key' => 'iso',
                                    'label' => 'name',
                                ],
                            ],
                        ]
                    ],
                ]
            ]
        ];
        $form_generator = new FormGeneratorDirector($form_generator_array, 'edit');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<label for="iso-us"> <input type="radio" value="us" id="iso-us" name="iso"  >USA</label><br>        <label for="iso-gb"> <input type="radio" value="gb" id="iso-gb" name="iso"  >UK</label><br>        <label for="iso-de"> <input type="radio" value="de" id="iso-de" name="iso"  >Germany</label><br>';
        $this->assertSame($expected, $html);
    }

    public function testSQL()
    {
        require_once __DIR__ . '/../../Examples/DBExamples/Config/Db.php';
       $form_generator_array = [
            'data' => [
                'row' => FormDataAsRow::getData(),
            ],
            'data' => [
                'connection' => [
                    /**
                     * optional
                     * if you will use database operation you must set this
                     */
                    'db' => [
                        /**
                         * This must be an object, and it must implement FormGenerator\Tools\DB\DBInterface
                         * There is an example in FormGenerator\Tools\DB\ folder as DBExample
                         */
                        'object' => DB::class
                    ]
                ]
            ],
            /**
             * Optional
             * Form Inputs
             */
            'inputs' => [
                'decision' => [
                    [
                        // this is a form input row
                        'type' => 'radio',
                        'capsule_template' => 'SIMPLE',
                        'attributes' => [
                            'name' => 'iso'
                        ],
                        'label' => 'Nationalities',
                        'options' => [
                            'data' => [
                                'from' => 'sql',
                                'sql' => "select * from countries",
                                'settings' => [
                                    'key' => 'iso',
                                    'label' => 'name',
                                ],
                            ],
                        ]
                    ],
                ]
            ]
        ];
        $form_generator = new FormGeneratorDirector($form_generator_array, 'edit');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<label for="iso-us"> <input type="radio" value="us" id="iso-us" name="iso"  >USA</label><br>        <label for="iso-gb"> <input type="radio" value="gb" id="iso-gb" name="iso"  >UK</label><br>        <label for="iso-de"> <input type="radio" value="de" id="iso-de" name="iso"  >Germany</label><br>';
        $this->assertSame($expected, $html);
    }
}

<?php
/**
 * @author selcukmart
 * 9.02.2022
 * 10:49
 */

namespace Tests\InputTypesTests\ScopeAdd;


use Examples\DBExamples\Libraries\Database\DB;
use FormGenerator\FormGeneratorDirector;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function testKeyValueArray()
    {
        $form_generator_array = [
            /**
             * Optional
             * Form Inputs
             */
            'inputs' => [
                'decision' => [
                    [
                        // this is a form input row
                        'type' => 'select',
                        'capsule_template' => 'SIMPLE',
                        'attributes' => [
                            'name' => 'iso'
                        ],
                        'label' => 'Nationalities',
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
        $form_generator = new FormGeneratorDirector($form_generator_array, 'add');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<select name="iso" type="select" class="" __is_def="1" id="iso" ><option value="">...</option><option value="us" >USA</option><option value="gb" >United Kingdom</option><option value="de" >Germany</option></select>';
        $this->assertSame($expected, $html);
    }

    public function testRows()
    {
        $form_generator_array = [
            /**
             * Optional
             * Form Inputs
             */
            'inputs' => [
                'decision' => [
                    [
                        // this is a form input row
                        'type' => 'select',
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
        $form_generator = new FormGeneratorDirector($form_generator_array, 'add');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<select name="iso" type="select" class="" __is_def="1" id="iso" ><option value="">...</option><option value="gb" >UK</option><option value="us" >USA</option><option value="de" >Germany</option></select>';
        $this->assertSame($expected, $html);
    }

    public function testQuery()
    {
        require_once __DIR__ . '/../../../Examples/DBExamples/Config/Db.php';
        $form_generator_array = [
            /**
             * Optional
             * Form Inputs
             */
            'inputs' => [
                'decision' => [
                    [
                        // this is a form input row
                        'type' => 'select',
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
        $form_generator = new FormGeneratorDirector($form_generator_array, 'add');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<select name="iso" type="select" class="" __is_def="1" id="iso" ><option value="">...</option><option value="us" >USA</option><option value="gb" >UK</option><option value="de" >Germany</option></select>';
        $this->assertSame($expected, $html);
    }

    public function testSQL()
    {
        require_once __DIR__ . '/../../../Examples/DBExamples/Config/Db.php';
        $form_generator_array = [
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
                        'type' => 'select',
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
        $form_generator = new FormGeneratorDirector($form_generator_array, 'add');
        $form_generator->buildHtmlOutput();
        $html = trim($form_generator->getHtmlOutput());
        $expected = '<select name="iso" type="select" class="" __is_def="1" id="iso" ><option value="">...</option><option value="us" >USA</option><option value="gb" >UK</option><option value="de" >Germany</option></select>';
        $this->assertSame($expected, $html);
    }
}

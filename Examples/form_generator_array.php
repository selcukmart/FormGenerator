<?php
/**
 * @author selcukmart
 * 2.02.2022
 * 17:29
 */

use Examples\DBExamples\Libraries\Database\DB;
use FormGenerator\FormGenerator;

include __DIR__ . '/../Examples/DBExamples/config.php';
include __DIR__ . '/../Examples/DBExamples/Libraries/Database/DB.php';
if (!isset($format)) {
    die('Please don\'t execute this page.<a href="./">Bye</a>');
}
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__ . '/../SMARTY_TPL_FILES');
$smarty->setCompileDir(__DIR__ . '/template_compile');
$smarty->setCacheDir(__DIR__ . '/template_cache');
$row = [
    'id' => '7',
    'type' => '1',
    'user_id' => '8015',
    'address_identification' => 'Work Adress',
    'name' => 'Joe',
    'surname' => 'DOE',
    'address' => 'Test strasse berlin',
    'postal_code' => '28100',
    'country' => '',
    'province' => '0',
    'county' => '0',
    'district' => '0',
    'neighbourhood' => '0',
    'phone' => '',
    'mobile_phone' => '5542856789',
    'mail' => null,
    'invoice_type' => '1',
    'identification_number' => '3514950',
    'nationality_tc_or_not' => '1',
    'company_name' => '',
    'tax_department' => '',
    'tax_number' => '',
    'is_e_invoice_user' => '2',
];
$form_generator_array = [
    /**
     * json,XML,HTML
     * json and xml are not coded yet
     */
    'data' => [
        'from' => 'row',
        'row' => $row,
        //'query' => DB::query("SELECT * FROM address WHERE id='7'"),
        //'sql' =>"SELECT * FROM address WHERE id='7'",
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
        ],
        /**
         * Data Structure Start For DB usage
         * There are several other data getting formats, they are explaining with other data title
         * if data comes from table id must set here
         */
        'id' => '7',
        /**
         * if it doesn't set, the system will use id column name
         */
        'id_column_name' => 'id',
        /**
         * if data comes from table it must set here
         */
        'table' => 'address',
        /// Data Structure Finish
    ],
    'export' => [
        /**
         * Optional
         * Default runs Generic
         */
        //'format' => 'Bootstrapv3FormWizard',
        'format' => $format,
        'type' => 'html',
        /**
         * Default Smarty
         * optional
         */
        'render' => [
            // twig, mustache, blade
            'by' => 'smarty',
            // This must be an object
            'smarty' => $smarty,
        ],
        /**
         * optional
         */
        'input-types' => [
            // default: FormGenerator_namespace\FormGeneratorInputTypes
            // if you set your namespace the system will run your FormGeneratorInputTypes folder
            // only name space your folder name must be FormGeneratorInputTypes
            'namespace' => ''
        ],
        /**
         * optional
         */
        'export-object' => [
            // default: FormGenerator_namespace\FormGeneratorExport
            // if you set your namespace the system will run your FormGeneratorExport folder
            // only name space your folder name must be FormGeneratorExport
            'namespace' => ''
        ],
    ],


    /**
     * Form Inputs
     */
    'inputs' => [
        // this is a section
        'decision' => [
            [
                'type' => 'form_section',
                'label' => 'Address Information'
            ],
            // this is a form input row
            [
                'type' => 'text',
                'attributes' => [
                    'name' => 'address_identification',
                ]
            ],
            [
                'type' => 'text',
                'attributes' => [
                    'name' => 'name',
                ]
            ],
            [
                'type' => 'text',
                'attributes' => [
                    'name' => 'surname',
                ]
            ],
            [
                'type' => 'textarea',
                'attributes' => [
                    'name' => 'address',
                ]
            ],
            [
                'type' => 'text',
                'attributes' => [
                    'name' => 'postal_code',
                ]
            ],
            [
                'type' => 'text',
                'attributes' => [
                    'name' => 'phone',
                ]
            ],
            [
                'type' => 'text',
                'attributes' => [
                    'name' => 'mobile_phone',
                ]
            ],
            [
                'type' => 'text',
                'attributes' => [
                    'name' => 'mail',
                ]
            ],
        ],
        // this is other section
        'corporate-info' => [
            [
                'type' => 'form_section',
                'label' => 'Corporate Information'
            ],
            [
                'type' => 'checkbox',
                'attributes' => [
                    'name' => 'iso'
                ],
                'dependency' => 'true',
                'label' => 'Nationalities',
                'options' => [
                    'data' => [
                        'from' => 'key_label_array',
                        'key_label_array' => [
                            'us' => 'USA',
                            'gb' => 'United Kingdom',
                            'de' => 'Germany'
                        ],
//                        'from' => 'rows',
//                        'rows' => [
//                            [
//                                'iso' => 'gb',
//                                'name' => 'UK'
//                            ],
//                            [
//                                'iso' => 'us',
//                                'name' => 'USA'
//                            ],
//                            [
//                                'iso' => 'de',
//                                'name' => 'Germany'
//                            ]
//                        ],
//                        'from' => 'query',
//                        'query' => DB::query("select * from countries"),
//                        'from' => 'sql',
//                        'sql' => "select * from countries",
                        /**
                         * if using SQL/Query/ROWS, this is a MUST,key_label_array: DONT USE
                         */
//                        'settings' => [
//                            'key' => 'iso',
//                            'label' => 'name',
//                        ],
                    ],
                    'control' => [
                        'from' => 'sql',
                        'sql' => "select iso from address_countries",
                        /*
                         * after parameters render as sql, generated sql will add the sql so how the query
                         *  will go on, using WHERE or AND, if not choose the system will look at WHERE in it
                        */
                        'has_where' => false,
                        'parameters' => [
                            // optional, if is not defined the system detect as this.attributes.name: iso
                            'this_field' => 'iso',
                            // must set
                            'foreign_field' => 'address_id',
                        ]
                    ]
                    //checked values
//                    'control' => [
//                        'from' => 'key_label_array',
//                        'key_label_array' => [
//                            'gb', 'us'
//                        ]
//                    ]
                ]
            ],
            [
                'type' => 'select',
                'attributes' => [
                    'name' => 'countries'
                ],
                'dependency' => 'true',

                'options' => [
                    'data' => [
                        'from' => 'key_label_array',
                        'key_label_array' => [
                            'tr' => 'Turkey',
                            'uk' => 'United Kingdom'
                        ]
                    ]
                ]
            ],
            [
                'type' => 'radio',
                'attributes' => [
                    'name' => 'invoice_type'
                ],
                'default_value' => '1',
                'dependency' => 'true',
                'options' => [
                    'data' => [
//                        'from' => 'query',
                        //'query' => DB::query("select * from countries"),
                        //'from' => 'rows',
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
                        'settings' => [
                            'key' => 'iso',
                            'label' => 'name',
                        ],
//                        'from' => 'key_label_array',
//                        'key_label_array' => [
//                            '1' => 'Individual',
//                            '2' => 'Institutional'
//                        ]
                    ]
                ]
            ],
            [
                'type' => 'text',
                'dependend' => [
                    'group' => 'invoice_type',
                    'dependend' => 'invoice_type-2'
                ],
                'attributes' => [
                    'name' => 'company_name',
                ]
            ],
            [
                'type' => 'text',
                'dependend' => [
                    'group' => 'invoice_type',
                    'dependend' => 'invoice_type-2'
                ],
                'attributes' => [
                    'name' => 'tax_administration',
                ]
            ],
            [
                'type' => 'text',
                'dependend' => [
                    'group' => 'invoice_type',
                    'dependend' => 'invoice_type-2'
                ],
                'attributes' => [
                    'name' => 'tax_number',
                ]
            ]
        ]
    ]
];

$form_generator = new FormGenerator($form_generator_array, 'edit');
$form_generator->extract();
echo $form_generator->getOutput();


<?php
/**
 * @author selcukmart
 * 2.02.2022
 * 17:29
 */

use FormGenerator\FormGenerator;

if (!isset($format)) {
    die('Please don\'t execute this page.<a href="./">Bye</a>');
}
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__ . '/../SMARTY_TPL_FILES');
$smarty->setCompileDir(__DIR__ . '/template_compile');
$smarty->setCacheDir(__DIR__ . '/template_cache');
$form_generator_array = [
    /**
     * json,XML,HTML
     * json and xml are not coded yet
     */
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
                'object' => ''
            ]
        ],
        /**
         * Data Structure Start For DB usage
         * There are several other data getting formats, they are explaining with other data title
         * if data comes from table id must set here
         */
        'id' => '6',
        /**
         * if it doesn't set, the system will use id column name
         */
        'id_column_name' => 'branchID',
        /**
         * if data comes from table it must set here
         */
        'table' => 'abc',
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
        'decision' => [
            [
                'type' => 'form_section',
                'label' => 'Address Information'
            ],
            [
                'type' => 'text',
                'attributes' => [
                    'name' => 'addres_name',
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
        'corporate-info' => [
            [
                'type' => 'form_section',
                'label' => 'Corporate Information'
            ],
            [
                'type' => 'checkbox',
                'attributes' => [
                    'name' => 'abc'
                ],
                'dependency' => 'true',

                'options' => [
                    'data' => [
                        'from' => 'key_value_array',
                        'key_value_array' => [
                            'a' => 'Checkbox Label 1',
                            'b' => 'Checkbox Label 1',
                            'c' => 'Checkbox Label 2',
                        ]
                    ],
                    //checked values
                    'control' => [
                        'from' => 'key_value_array',
                        'key_value_array' => [
                            'a', 'c'
                        ]
                    ]
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
                        'from' => 'key_value_array',
                        'key_value_array' => [
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
                'default_value' => '0',
                'dependency' => 'true',
                'options' => [
                    'data' => [
                        'from' => 'key_value_array',
                        'key_value_array' => [
                            '0' => 'Individual',
                            '1' => 'Institutional'
                        ]
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
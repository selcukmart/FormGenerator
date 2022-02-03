Form Generator
===================================
```
composer require selcukmart/form-generator
```
Form Generator can build form from array.
```php
use FormGenerator\FormGenerator;
/**
* scopes: add or edit
  */
  $form_generator = new FormGenerator($form_generator_array, 'edit');
  $form_generator->extract();
  echo $form_generator->getOutput();
```
Scopes
==
add or edit

Form Generator Array Example

```php
$form_generator_array = [
            /**
             * json,XML,HTML
             * json and xml are not coded yet
             */
            'export' => [
                //'format' => 'Bootstrapv3FormWizard',
                'format' => 'Bootstrapv3Form',
                'type' => 'html'
            ],
            /**
             * Data Structer Start
             * There are several other data getting formats, they are explaining with other data title
             */
            'data_id' => '6',
            'data_table' => 'abc',
            /// Data Structure Finish
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
                    [
                        'type' => 'radio',
                        'attributes' => [
                            'name' => 'invoice_type'
                        ],
                        'dependency' => 'true',
                        'options' => [
                            'data' => [
                                'from' => 'key_value_array',
                                'key_value_array' => [
                                    '0' => 'abc',
                                    '1' => 'def'
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
```

#### Export As HTML

![Example](https://canlidershane.s3.eu-central-1.amazonaws.com/content/static/uploads/files/dosya1363738427_dosya1363738427___so___cdf98073-3005-473a-beca-2480a837e847-1643721228.png "Example Output")


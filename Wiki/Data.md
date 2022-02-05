![query](https://s3.eu-central-1.amazonaws.com/static.testbank.az/uploads/files/15-1619421581-ok-image.png)


###### What Does This Class Do?

It serves to prevent the input fields with fixed values from appearing in the form by determining their default values.

###### How to use

![keyvalue](https://s3.eu-central-1.amazonaws.com/static.testbank.az/uploads/files/15-1619421818-ok-image.png)
```php
          [
            'type' => 'radio',
            'label' => 'Components Include Place',
            'dependend' => [
                'group' => 'components_type',
                'dependend' => 'components_type-file'
            ],
            'attributes' => [
                'name' => 'type5'
            ],
            'options' => [
                'data' => [
                    'from' => 'key_label_array',
                    'key_label_array' => [
                        '0' => 'Put module between Header and Footer',
                        '1' => 'Install the module alone',
                    ]
                ]
            ]
        ],
                                                               
```

The above code corresponds to 0 option value in the values given in the key_label_array part. manual value entry
key_label_array is used when necessary.



![sql](https://s3.eu-central-1.amazonaws.com/static.testbank.az/uploads/files/15-1619422315-ok-image.png)
```
               [
            'type' => 'radio',
            'label' => 'Components Include Place',
            'dependend' => [
                'group' => 'components_type',
                'dependend' => 'components_type-file'
            ],
            'attributes' => [
                'name' => 'type5'
            ],
            'options' => [
                'data' => [
                    'from' => 'sql',
                    'sql' => "SELECT 
                                * FROM a AS b
                                WHERE 
                                b.type='1' 
                                AND b.parent_id='1'
                                "
                ]
            ]
        ],
                                          
```

It allows to list the results from the sql query with the above code. In the Data section as from => sql
After it is determined, we specify our sql query.

```
            [
            'type' => 'radio',
            'attributes' => [
                'name' => 'place'
            ],
            'empty_option' => false,
            'options' => [
                'data' => [
                    'from' => 'sql',
                    'sql' => "SELECT * FROM c WHERE type='abc' AND state='ok'"
                ]
            ],
            'label' => 'Menu Place',
        ],
                                     
               
```

If we have a query in the above code block, we add a query and send it as a parameter.
gives the answer.



![rows](https://s3.eu-central-1.amazonaws.com/static.testbank.az/uploads/files/15-1619433063-ok-image.png)

```
            [
                 'type' => 'radio',
                  'attributes' =>[
                 'name' => 'x'
                        ],
        'options' => [
            'rows' => [
                [
                    'id' => 1,
                    'isim' => 'a'
                ],
                [
                    'id' => 2,
                    'isim' => 'b'
                ]
            ]
        ]
    ],
                                    
               
```


###### In Which Situations Is It Used

It is used where the select box is needed.

Important Note: Special input types have their own data retrieval methods.



![Checkbox1](https://s3.eu-central-1.amazonaws.com/static.testbank.az/uploads/files/15-1619012948-ok-image.png)

###### What Does This Class Do?

Checkbox provides options for the user to be able to select one, several or all of them.
used. Thanks to the Checked feature, we can check whether it is selected or not.

###### How to use

```
            [
                'type' => 'checkbox',
                'attributes' => [
                    'name' => 'parent_id'
                ],
                'option_settings' => [
                    'key' => 'id',
                    'label' => 'name',

                ],
                'options' => [
                    'data' => [
                        'from' => 'sql',
                        'sql' => "SELECT * FROM c" 
                    ],
                    'control' => [
                        'sql' => "SELECT * FROM d WHERE type= '1' AND type2='3' ",
                        'parameters' => [
                            'this_field' => 'id',
                            'foreign_field' => 'parent_id'
                        ]
                    ]
                ],
                'label' => 'Parent'
            ]
```

The selected values are passed to the parent_id array. In the Attributes section, the name or other properties of the input are specified.
Two tables are used here. sql table and foreign table. The sql table querying in Options is this_field in the control part.
The sql query in the control array matches foreign_field.

###### In Which Situations Is It Used

For example, when we ask about your interests, you may have one or more interests.
we use the checkbox object wherever we want.

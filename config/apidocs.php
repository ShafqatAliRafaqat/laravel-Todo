<?php

return [
/*
    |--------------------------------------------------------------------------
    | HRM-System API Documentation
    |--------------------------------------------------------------------------
    |
    | This array includes module names and menu list under each modules. module name and title should be unique .
    | this will return array that will use to render API documentation
    */

    0 =>[   'title'=>"Login",
            'menu' =>  [
                'Admin Login',                      //0
                'Registration',                     //1
                'Admin Logout',                     //2
                'Code Verification',                //3
                'Resend Code',                      //4
            ],
        ],
    1 =>[   'title'=>"ToDos",
        'menu' =>  [
            'All ToDos',                        //0
            'Create ToDo',                      //1
            'Show ToDo detail',                 //2
            'Update ToDos',                     //3
            'Delete ToDos',                     //4
        ],
    ],
];
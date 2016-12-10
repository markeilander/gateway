<?php
/*
|--------------------------------------------------------------------------
| Eilander Gateway Config
|--------------------------------------------------------------------------
|
|
*/
return [
    /*
    |--------------------------------------------------------------------------
    | Fractal Presenter Config
    |--------------------------------------------------------------------------
    |
    Available serializers:
    ArraySerializer
    DataArraySerializer
    JsonApiSerializer
    */
    'fractal'=> [
        'params'=> [
            'include'=> 'include',
        ],
        'serializer' => League\Fractal\Serializer\DataArraySerializer::class,
    ],
];

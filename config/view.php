<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most applications have a single path for Blade templates, but an array
    | keeps this flexible and matches Laravel's expected config structure.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | On shared hosting, being explicit here avoids edge cases where Laravel
    | cannot resolve a compiled view cache path during optimize / clear steps.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        storage_path('framework/views')
    ),

];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Praktikum Auth Method
    |--------------------------------------------------------------------------
    |
    | Defines how users are authenticated and created in the system.
    |
    | Supported: "local", "sso"
    |
    | local : Development mode. Auto-creates users from CSV if they don't exist.
    |         Password will be Hash(nim).
    | sso   : Production mode. Users MUST exist in the SSO database.
    |         No auto-creation is allowed.
    |
    */
    'auth_method' => env('PRAKTIKUM_AUTH_METHOD', 'local'),

];

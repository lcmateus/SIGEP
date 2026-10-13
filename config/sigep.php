<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Limite de upload (em KB)
    |--------------------------------------------------------------------------
    |
    | Limite maximo de tamanho de arquivo aceito pelas validacoes da aplicacao.
    | Deve ser sempre menor ou igual ao "post_max_size" e ao
    | "upload_max_filesize" configurados no php.ini.
    |
    */
    'upload_max_kb' => (int) env('UPLOAD_MAX_KB', 10240),
];

<?php

declare(strict_types=1);

$intervaloPing = (int) env('TREINA_INTERVALO_PING', 10);

return [
    'percentual_conclusao' => (int) env('TREINA_PERCENTUAL_CONCLUSAO', 90),
    'intervalo_ping' => $intervaloPing,
    'tolerancia_salto' => $intervaloPing * 2.5,
    'limite_comentarios_minuto' => 10,
    'upload' => [
        'max_mb' => 20,
        'tipos' => ['pdf', 'xlsx', 'docx', 'pptx', 'png', 'jpg', 'zip'],
    ],
];

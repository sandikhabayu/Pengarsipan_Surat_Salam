<?php

return [

    'show_warnings' => false,
    'orientation' => 'portrait',

    'defines' => [
        "font_dir" => base_path('resources/fonts/'), // Default dir
        "font_cache" => storage_path('fonts/'), // Cache dir

        "temp_dir" => sys_get_temp_dir(),
        "chroot" => base_path(),

        'enable_remote' => true,
        'log_output_file' => storage_path('logs/dompdf.html'),
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_font' => 'timesnewroman',

        'dpi' => 96,
        'font_height_ratio' => 1.1,

        'enable_php' => false,
        'enable_javascript' => false,
        'enable_html5_parser' => true,
    ],

    // 🔽 Tambahkan ini untuk custom font
    'custom_font_dir' => base_path('resources/fonts/times/'),

    'custom_font_data' => [
        'timesnewroman' => [
            'R' => 'times.ttf',
            'B' => 'timesbd.ttf',
            'I' => 'timesi.ttf',
            'BI' => 'timesbi.ttf',
            'useOTL' => 0,
            'useKashida' => 75,
        ]
    ],

    'auto_load_custom_fonts' => true,

    'log_output_file' => storage_path('logs/dompdf.html'),

];

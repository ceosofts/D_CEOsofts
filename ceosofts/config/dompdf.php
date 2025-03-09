<?php

return [
    'font_dir' => storage_path('fonts'),
    'font_cache' => storage_path('fonts'),
    'temp_dir' => sys_get_temp_dir(),
    'chroot' => realpath(base_path()),
    'allowed_protocols' => [
        'file://' => ['*'],
        'http://' => ['*'],
        'https://' => ['*'],
    ],
    'log_output_file' => null,
    'default_media_type' => 'print',
    'default_paper_size' => 'a4',
    'default_font' => 'THSarabunNew',
    'dpi' => 96,
    'enable_php' => true,
    'enable_javascript' => true,
    'enable_remote' => true,
    'font_height_ratio' => 1.1,
    'is_php_enabled' => true,
    'is_html5_parser_enabled' => true,
    'unicode_enabled' => true,
    'enable_font_subsetting' => true,
    'pdf_backend' => 'CPDF',

    'fonts' => [
        'THSarabunNew' => [
            'normal' => storage_path('fonts/THSarabunNew.ttf'),
            'bold' => storage_path('fonts/THSarabunNew-Bold.ttf'),
            'italic' => storage_path('fonts/THSarabunNew-Italic.ttf'),
            'bold_italic' => storage_path('fonts/THSarabunNew-BoldItalic.ttf')
        ],
    ],
];

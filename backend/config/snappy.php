<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    |
    | This option contains settings for PDF generation.
    |
    | Enabled:
    |
    |    Whether to load PDF / Image generation.
    |
    | Binary:
    |
    |    The file path of the wkhtmltopdf / wkhtmltoimage executable.
    |
    | Timout:
    |
    |    The amount of time to wait (in seconds) before PDF / Image generation is stopped.
    |    Setting this to false disables the timeout (unlimited processing time).
    |
    | Options:
    |
    |    The wkhtmltopdf command options. These are passed directly to wkhtmltopdf.
    |    See https://wkhtmltopdf.org/usage/wkhtmltopdf.txt for all options.
    |
    | Env:
    |
    |    The environment variables to set while running the wkhtmltopdf process.
    |
    */
    ///usr/bin/wkhtmltopdf
    //./usr/local/bin/wkhtmltopdf

    'pdf' => [
        'enabled' => true,
         'binary'  => env('WKHTML_PDF_BINARY', base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64')),
        //'binary'  => env('WKHTML_PDF_BINARY', '/usr/local/bin/wkhtmltopdf2'),
        'timeout' => false,
        'options' => [
            'enable-local-file-access' => true,
            'encoding'      => 'UTF-8',
            'no-stop-slow-scripts' => true,
            'javascript-delay' => 1000,
            'enable-internal-links' => true,
            'enable-external-links' => true,
            'disable-smart-shrinking' => true,
            'viewport-size' => '1024×768',
            'zoom' => '0.78'
        ],
        'env'     => [
            'QT_LOGGING_RULES' => '*.debug=false;qt.webengine*.debug=false',
            'QTWEBENGINE_CHROMIUM_FLAGS' => '--disable-web-security --disable-features=VizDisplayCompositor',
        ],
    ],

    'image' => [
        'enabled' => true,
        'binary'  => env('WKHTML_IMG_BINARY', '/usr/local/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];

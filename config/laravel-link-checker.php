<?php

return [

    /**
     * The base url of your app.  Leave this empty to use
     * the url configured in config/app.php
     */
    'url' => 'http://localhost:8888',

    /**
     * The profile determines which links needs to be checked.
     */
    'defaultProfile' => Spatie\LinkChecker\CheckAllLinks::class,

    /**
     * The reporter determine what needs to be done when the
     * the crawler has visited a link.
     */
    'defaultReporter' => Spatie\LinkChecker\Reporters\LogBrokenLinks::class,


    /**
     *  Here you can specify configuration regarding the used reporters
     */
    'reporters' => [


        'mail' => [
            
            /**
             * The from address to be used by the mail reporter.
             */
            'fromAddress' => '',
            
            /**
             * The to address to be used by the mail reporter.
             */
            'toAddress' => '',
        ]
    ]
];

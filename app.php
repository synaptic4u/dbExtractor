<?php
    /**
     * Application entry script.
     * Called from the command line.
     * App relies on the config file: config.json
     * Shows the user the link to the Progress Report and activity log.
     * Creates an instance of the App class.
     */
    if (file_exists(dirname(__FILE__, 1).'/vendor/autoload.php')) {
        require_once dirname(__FILE__, 1).'/vendor/autoload.php';
    }
    use Synaptic4u\App\App;

    new App();
<?php

namespace Synaptic4u\Structure\Views;

use Exception;
use Synaptic4u\Core\Template;
use Synaptic4u\Log\Activity;
use Synaptic4u\Log\Error;
use Synaptic4u\Log\Log;

/**
 * IN USE.
 */
class CLI implements IStructureUI
{
    public function __construct()
    {
        try {
            $this->template = new Template(__DIR__.'/CLI/');
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'Error' => $e->__toString(),
            ]);
        }
    }

    public function exists(array $params = []): int
    {
        $template = $params;
        $template['eol'] = PHP_EOL;

        if (print_r($this->template->build('exists', $template))) {
            return 1;
        }

        return 0;
    }

    public function created(array $params = []): int
    {
        $template = $params;
        $template['eol'] = PHP_EOL;

        if (print_r($this->template->build('created', $template))) {
            return 1;
        }

        return 0;
    }

    public function processing(): int
    {
        $template['eol'] = PHP_EOL;

        if (print_r($this->template->build('processing', $template))) {
            return 1;
        }

        return 0;
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    protected function error($msg)
    {
        new Log($msg, new Error());
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    protected function log($msg)
    {
        new Log($msg, new Activity());
    }
}

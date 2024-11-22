<?php

namespace Nginx\Cli;

class Version
{
    public static function get()
    {
        $contents = @file_get_contents('../composer.json');

        if (!$contents) {
            return 'UNKNOWN';
        }

        $data = json_decode($contents, true);

        return $data['version'];
    }
}

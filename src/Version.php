<?php

namespace Nginx\Cli;

class Version
{
    public static function get()
    {
//        $contents = @file_get_contents(__DIR__.'/../../../../composer.lock');
//
//        if (! $contents) {
//            return 'UNKNOWN';
//        }
//
//        $lock = json_decode($contents, true);
//
//        $packages = $lock['packages'];
//
//        $i = array_search('nginx/cli', array_column($packages, 'name'));
//
//        return $packages[$i]['version'];
        return '1.0.0';
    }
}

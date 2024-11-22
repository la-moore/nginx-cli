<?php

namespace Nginx\Cli\Helpers;

class NginxConfig
{
    public int $port = 80;
    public string $domain = '';
    public string $root = '/var/www';
    public string $index = 'index.php index.html';
    public string $phpSock = '/var/run/php/php-fpm';
    public bool $ssl = false;
    public bool $http2 = false;
    public bool $redirectToSsl = false;
    public bool $indexFallback = false;
    public bool $enablePhpFastCgi = false;
    public bool $enableSecurity = false;
    public bool $enableGzip = false;

    public function __construct()
    {}

    private function arrayToNginxConfig2($config, $level = 0) {
        $nginxConfig = "";

        foreach ($config as $key => $value) {
            if ($level > 0) {
                $nginxConfig .= str_repeat("\t", $level);
            }

            if (is_array($value)) {
                $nginxConfig .= "$key {\n" . $this->arrayToNginxConfig($value, $level + 1) . str_repeat("\t", $level) . "}";
            } else {
                $nginxConfig .= "$key $value;";
            }

            $nginxConfig .= "\n";
        }

        return $nginxConfig;
    }

    private function arrayToNginxConfig($array, $level = 0) {
        $config = '';
        $space = str_repeat("\t", $level);

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($key === 'servers') {
                    foreach ($value as $server) {
                        $config .= $space . "server {\n";
                        $config .= $this->arrayToNginxConfig($server['server'], $level + 1);
                        $config .= $space . "}\n";
                    }
                } else {
                    $config .= $space . $key . " {\n";
                    $config .= $this->arrayToNginxConfig($value, $level + 1);
                    $config .= $space . "}\n";
                }
            } else {
                $config .= $space . "$key $value;\n";
            }
        }

        return $config;
    }


    public function getLocations() {
        $location = [
            '= /favicon.ico' => [
                'log_not_found' => 'off'
            ],
            '= /robots.txt' => [
                'log_not_found' => 'off'
            ]
        ];

        if ($this->indexFallback) {
            $location['/'] = [
                'try_files' => '$uri $uri/ /'. explode(' ', $this->index)[0] .'?$query_string',
            ];
        }

        if ($this->enablePhpFastCgi) {
            $location['~\.php$'] = [
                'fastcgi_pass' => "unix:$this->phpSock.sock",

//                'fastcgi_param' => 'SCRIPT_FILENAME $realpath_root$fastcgi_script_name',

                'include' => 'fastcgi_params',

                'try_files' => '$fastcgi_script_name =404',

                'fastcgi_index' => 'index.php',
                'fastcgi_buffers' => '8 16k',
                'fastcgi_buffer_size' => '32k',

                'fastcgi_param DOCUMENT_ROOT' => '$realpath_root',
                'fastcgi_param SCRIPT_FILENAME' => '$realpath_root$fastcgi_script_name',
                'fastcgi_param PHP_ADMIN_VALUE' => 'open_basedir=$base/:/usr/lib/php/:/tmp/',
            ];
        }

        if ($this->enableSecurity) {
            $location['~ /\.(?!well-known)'] = [
                'deny' => 'all'
            ];
        }

        return $location;
    }

    public function getBaseConfig() {
        $config = [
            'charset' => 'utf-8',

            'root' => $this->root,
            'index' => $this->index,
            'location' => $this->getLocations()
        ];

        if ($this->enableSecurity) {
            $config['add_header X-Frame-Options'] = '"SAMEORIGIN"';
            $config['add_header X-XSS-Protection'] = '"1; mode=block" always';
            $config['add_header X-Content-Type-Options'] = '"nosniff" always';
            $config['add_header Referrer-Policy'] = '"no-referrer-when-downgrade" always';
            $config['add_header Content-Security-Policy'] = '"default-src \'self\' http: https: ws: wss: data: blob: \'unsafe-inline\'; frame-ancestors \'self\';" always';
            $config['add_header Permissions-Policy'] = '"interest-cohort=()" always';
            $config['add_header Strict-Transport-Security'] = '"max-age=31536000; includeSubDomains" always';
        }

        if ($this->enableGzip) {
            $config['gzip'] = 'on';
            $config['gzip_vary'] = 'on';
            $config['gzip_proxied'] = 'any';
            $config['gzip_comp_level'] = '6';
            $config['gzip_types'] = 'text/plain text/css text/xml application/json application/javascript application/rss+xml application/atom+xml image/svg+xml';
        }

        return $config;
    }

    public function getMainServerConfig() {
        $config = [
            'listen' => $this->port,
            'server_name' => $this->domain,
        ];

        if ($this->redirectToSsl && $this->ssl) {
            $config['return'] = '301 https://'.$this->domain.'$request_uri';
        } else {
            return array_merge($config, $this->getBaseConfig());
        }

        return $config;
    }

    public function getSslServerConfig() {
        $config = [
            'listen' => '443 ssl',
            'server_name' => $this->domain,
            'ssl_certificate' => "/etc/letsencrypt/live/$$this->domain/fullchain.pem",
            'ssl_certificate_key' => "/etc/letsencrypt/live/$$this->domain/privkey.pem",
            'include' => "/etc/letsencrypt/options-ssl-nginx.conf",
            'ssl_dhparam' => "/etc/letsencrypt/ssl-dhparams.pem",
        ];

        if ($this->http2) {
            $config['listen'] .= ' http2';
        }

        return array_merge($config, $this->getBaseConfig());
    }


    public function toArray() {
        $servers = [
            [
                'server' => $this->getMainServerConfig()
            ]
        ];

        if ($this->ssl) {
            $servers[] = [
                'server' => $this->getSslServerConfig()
            ];
        }

        return [
            'servers' => $servers,
        ];
    }

    public function __toString() {
        return $this->arrayToNginxConfig($this->toArray());
    }
}

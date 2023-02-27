<?php

namespace TianSchutte\MailwizzSync\Api;

use EmsApi\Base;
use EmsApi\Cache\File;
use EmsApi\Config;
use Exception;
use ReflectionException;

/**
 * @package MailWizzApi
 * @description  Holds the MailWizzApi connection
 * @author: Tian Schutte
 */
class MailWizzApi
{

    public function __construct()
    {
        $this->connect();
    }

    /**
     * @note Make sure filesPath directory is writable in webserver
     */
    public function connect()
    {
        //Configuration object (Get your API info from: https://kb.mailwizz.com/articles/find-api-info/)
        try {
            $filesPath = config('mailwizzsync.cache_file_path');

            $this->createDirectory($filesPath);

            $config = new Config([
                'apiUrl' => config('mailwizzsync.api_url'),
                'apiKey' => config('mailwizzsync.public_key'),
                'components' => [
                    'cache' => [
                        'class' => File::class,
                        'filesPath' => $filesPath,
                    ]
                ]
            ]);

            //Now inject the configuration and we are ready to make api calls
            Base::setConfig($config);

            // start UTC TODO: wasn't included in original, but is it needed?
            //date_default_timezone_set('UTC');


        } catch (ReflectionException|Exception $e) {
            logger()->error("MailWizz API Config Exception: " . $e->getMessage());
        }
    }

    /**
     * @param $path
     * @return void
     */
    private function createDirectory($path): void
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}

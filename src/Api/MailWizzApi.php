<?php

namespace TianSchutte\MailwizzSync\Api;

use EmsApi\Base;
use EmsApi\Cache\File;
use EmsApi\Config;
use Exception;

/**
 * @package MailWizzSync
 * @licence Giant Outsourcing
 * @description  Holds the MailWizzApi connection
 * @author: Tian Schutte
 */
class MailWizzApi
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        try {
            $this->connect();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @note Make sure filesPath directory is writable in webserver
     * @throws Exception
     */
    public function connect()
    {
        $apiUrl = config('mailwizzsync.mailwizz.api_url');
        $apiKey = config('mailwizzsync.mailwizz.public_key');
        $filesPath = config('mailwizzsync.mailwizz.cache_file_path');

        $this->createDirectory($filesPath);

        if (!$apiUrl || !$apiKey) {
            throw new Exception('Missing MailWizz API configuration details');
        }

        $config = new Config([
            'apiUrl' => $apiUrl,
            'apiKey' => $apiKey,
            'components' => [
                'cache' => [
                    'class' => File::class,
                    'filesPath' => $filesPath,
                ]
            ]
        ]);

        //Now inject the configuration and we are ready to make api calls
        Base::setConfig($config);

        // start UTC
        date_default_timezone_set('UTC');
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

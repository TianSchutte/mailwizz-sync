<?php

namespace TianSchutte\MailwizzSync\Api;

use EmsApi\Base;
use EmsApi\Cache\File;
use EmsApi\Config;
use Exception;
use Illuminate\Support\Facades\Log;
use ReflectionException;

/**
 * @package MailWizzApi
 * @description  Holds the MailWizzApi connection
 * @author: Tian Schutte
 */
class MailWizzApi
{

    /**
     * @var Log
     */
    protected $logger;

    public function __construct()
    {
        $this->connect();
        $this->logger = Log::channel('mailwizzsync');
    }

    /**
     * @note Make sure filesPath variable is writable in webserver
     * @return bool
     */
    public function connect(): bool
    {
        //Configuration object (Get your API info from: https://kb.mailwizz.com/articles/find-api-info/)
        try {
            $filesPath = storage_path() . '/MailWizz/data/cache';

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

            return true;
        } catch (ReflectionException $e) {
             $this->logger->error("MailWizz API Config Exception: " . $e->getMessage());
        } catch (Exception $e) {
             $this->logger->error("MailWizz API Exception: " . $e->getMessage());
        }

        return false;
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

<?php

namespace TianSchutte\MailwizzSync;

class Helper
{
    /**
     * @param $response
     * @return bool
     */
    public static function isEmsResponseSuccessful($response): bool
    {
        $isSuccessful = $response->getHttpCode() >= 200 && $response->getHttpCode() < 400;
        if (!$isSuccessful) return false;

        $status = $response->body->itemAt('status');
        if ($status != 'success') return false;

        return true;
    }

    /**
     * @param $country
     * @return mixed
     */
    public static function getListIdFromConfig($country)
    {
        $countryValues = config('mailwizzsync.lists');
        return $countryValues[$country] ?? $countryValues['ROTW'];
    }
}

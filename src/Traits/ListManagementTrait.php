<?php

namespace TianSchutte\MailwizzSync\Traits;


trait ListManagementTrait
{
    /**
     * @return array
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getLists(): array
    {
        $data = [];

        $response = $this->listEndpoint->getLists();

        if (!$this->isEmsResponseSuccessful($response)) {
            throw new \Exception('Error getting lists');
        }

        $records = $response->body->toArray()['data']['records'];

        $chunks = array_chunk($records, config('mailwizzsync.chunk_size'));

        foreach ($chunks as $chunk) {
            foreach ($chunk as $list) {
                $data[] = [
                    'name' => $list['general']['name'],
                    'list_uid' => $list['general']['list_uid'],
                    'display_name' => $list['general']['display_name'],
                    'description' => $list['general']['description'],
                ];
            }
        }

        return $data;
    }

    /**
     * @param $country
     * @return mixed
     */
    public static function getListIdForCountry($country)
    {
        $countryValues = config('mailwizzsync.lists');
        return $countryValues[$country] ?? $countryValues['ROTW'];
    }
}

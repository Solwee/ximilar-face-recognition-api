<?php
namespace Solwee\XimilarFaceRecognition;
use Solwee\XimilarFaceRecognition\Exceptions\DataErrorException;

class Client
{
    private \GuzzleHttp\Client $client;
    private string $serverUrl;
    private string $token;

    public function __construct(
        \GuzzleHttp\Client $client,
        string $serverUrl,
        string $token
    )
    {
        $this->client = $client;
        $this->serverUrl = $serverUrl;
        $this->token = $token;
    }

    /**
     * @param array $imagePaths
     * @return IdentityCollection[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getIdentificationByUrl(array $imagePaths): array
    {

        $output = [];
        $urls = [];
        foreach ($imagePaths as $imagePath) {
            $urls[] = [
                "_url" => $imagePath
            ];
        }

        $data = [
            "records" => $urls
        ];

        $response = $this->client->request('POST', sprintf('%s/identity/v2/identify', $this->serverUrl), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => sprintf('Token %s', $this->token),
                'collection-id' => 'c4ed5dc5-4ca9-4b52-9d01-307c9eb55a1d',
            ], 'json' => $data
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        #Iterace analyzovaných obrázků
        foreach ($data['records'] as $record) {
            $identityCollection = new IdentityCollection($record["_url"], $record["_width"], $record["_height"], []);
            #Iterace detekovaných identit
            foreach($record['_objects'] as $object) {
                if (empty($object['_identification']['best_match']['name'])) {
                    $identity = new UnknownIdentity($object['bound_box'][0], $object['bound_box'][1], $object['bound_box'][2], $object['bound_box'][3], []);
                } else {
                    $identity = new Identity(
                        $object['_identification']['best_match']['name'],
                        $object['_identification']['best_match']['distance'],
                        $object['bound_box'][0],
                        $object['bound_box'][1],
                        $object['bound_box'][2],
                        $object['bound_box'][3],
                        $object['_identification']['best_match']['_url'],
                        []);

                }


                if ($object['_identification']['alternatives']) {
                    foreach ($object['_identification']['alternatives'] as $alternative) {
                        $identity->addAlternativeIdentity(new Identity(
                            $alternative['name'],
                            $alternative['distance'],
                            $object['bound_box'][0],
                            $object['bound_box'][1],
                            $object['bound_box'][2],
                            $object['bound_box'][3],
                            $alternative['_url'],
                            []
                        ));
                    }
                }
                $identityCollection->addIdentity($identity);
            }

            $output[$record["_url"]] = $identityCollection;

        }

        return $output;

    }

    public function getIdentificationByFile(array $imageDataPacks): array
    {

        $output = [];
        $urls = [];
        $type = 'jpeg';
        foreach ($imageDataPacks as $key => $imageData) {
            $urls[] = [
                "_base64" => $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imageData)
            ];
        }
        $data = [
            "records" => $urls
        ];
        $response = $this->client->request('POST', sprintf('%s/identity/v2/identify', $this->serverUrl), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => sprintf('Token %s', $this->token),
                'collection-id' => 'c4ed5dc5-4ca9-4b52-9d01-307c9eb55a1d',
            ], 'json' => $data
        ]);

        //var_dump($response->getBody()->getContents());exit;

        $data = json_decode($response->getBody()->getContents(), true);

        //var_dump($data);exit;

        #Iterace analyzovaných obrázků
        foreach ($data['records'] as $record) {
            $identityCollection = new IdentityCollection($record["_id"], $record["_width"], $record["_height"], []);
            #Iterace detekovaných identit
            foreach($record['_objects'] as $object) {
                if (empty($object['_identification']['best_match']['name'])) {
                    $identity = new UnknownIdentity($object['bound_box'][0], $object['bound_box'][1], $object['bound_box'][2], $object['bound_box'][3], []);
                } else {
                    $identity = new Identity(
                        $object['_identification']['best_match']['name'],
                        $object['_identification']['best_match']['distance'],
                        $object['bound_box'][0],
                        $object['bound_box'][1],
                        $object['bound_box'][2],
                        $object['bound_box'][3],
                        $object['_identification']['best_match']['_url'],
                        []);

                }


                if ($object['_identification']['alternatives']) {
                    foreach ($object['_identification']['alternatives'] as $alternative) {
                        $identity->addAlternativeIdentity(new Identity(
                            $alternative['name'],
                            $alternative['distance'],
                            $object['bound_box'][0],
                            $object['bound_box'][1],
                            $object['bound_box'][2],
                            $object['bound_box'][3],
                            $alternative['_url'],
                            []
                        ));
                    }
                }
                $identityCollection->addIdentity($identity);
            }

            $output[$record["_id"]] = $identityCollection;

        }

        return $output;

    }
}
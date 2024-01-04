<?php
namespace Solwee\XimilarFaceRecognition;
use Psr\Http\Message\ResponseInterface;
use Solwee\XimilarFaceRecognition\Exceptions\DataErrorException;

class Client
{
    private \GuzzleHttp\Client $client;
    private string $serverUrl;
    private string $token;
    private string $collectionId;

    public function __construct(
        \GuzzleHttp\Client $client,
        string $serverUrl,
        string $token,
        string $collectionId
    )
    {
        $this->client = $client;
        $this->serverUrl = $serverUrl;
        $this->token = $token;
        $this->collectionId = $collectionId;
    }

    /**
     * @param array $imagePaths
     * @return IdentityCollection[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getIdentificationByUrl(array $imagePaths): array
    {
        $urls = [];
        foreach ($imagePaths as $key => $imagePath) {
            $urls[] = [
                "_url" => $imagePath,
                "meta_data" => [
                    "own_id" => $key
                ]
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
                'collection-id' => $this->collectionId,
            ], 'json' => $data
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $this->processResponse($response);

    }

    public function getIdentificationByFile(array $imageDataPacks): array
    {

        $urls = [];
        //@TODO: zjistit typ souboru
        $type = 'jpeg';
        foreach ($imageDataPacks as $key => $imageData) {
            $urls[] = [
                "_base64" => $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imageData),
                "meta_data" => [
                    "own_id" => $key
                ]
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
                'collection-id' => $this->collectionId,
            ], 'json' => $data
        ]);



        return $this->processResponse($response);

    }

    private function processResponse(ResponseInterface $response): array
    {
        $output = [];
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

            $output[$record["meta_data"]["own_id"]] = $identityCollection;

        }

        return $output;

    }
}
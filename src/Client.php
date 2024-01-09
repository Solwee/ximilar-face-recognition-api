<?php
namespace Solwee\XimilarFaceRecognition;
use Psr\Http\Message\ResponseInterface;
use Solwee\XimilarFaceRecognition\Exceptions\DataErrorException;

class Client
{
    private \GuzzleHttp\Client $client;
    private string $serverUrl;
    private string $token;
    private string $searchCollectionId;
    private string $workspaceId;
    private int $identityCollectionId;

    public function __construct(
        \GuzzleHttp\Client $client,
        string             $serverUrl,
        string             $token,
        string             $workspaceId,
        string             $searchCollectionId,
        int                $identityCollectionId

    )
    {
        $this->client = $client;
        $this->serverUrl = $serverUrl;
        $this->token = $token;
        $this->searchCollectionId = $searchCollectionId;
        $this->workspaceId = $workspaceId;
        $this->identityCollectionId = $identityCollectionId;
    }

    /**
     * @param array $imagePaths
     * @return FaceCollection[]
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

        $data = ["records" => $urls, "collection_id" => $this->searchCollectionId];

        $response = $this->client->request('POST', sprintf('%s/identity/v2/identify', $this->serverUrl), [
            'headers' => $this->getDefaultHeader(), 'json' => $data
        ]);


        return $this->processIdentifyResponse($response);

    }
    public function createIdentity(string $customIdentityID, string $name, array $metadata = []): Identity
    {

        $_metadata = ["name" => $name];

        $metadata = array_merge($_metadata, $metadata);

        $data = [
            "name" => $name,
            "workspace" => $this->workspaceId,
            "product_collection" => $this->identityCollectionId,
            "meta_data" => $metadata,
            "customer_product_id" => $customIdentityID
        ];

        $response = $this->client->request('POST', sprintf('%s/product/v2/product', $this->serverUrl), [
            'headers' => $this->getDefaultHeader(), 'json' => $data
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return new Identity($data["name"], $data["id"], $data["customer_product_id"]);

    }

    public function updateIdentity(string $identityID, string $customIdentityID, string $name, array $metadata = []): Identity
    {

        $_metadata = ["name" => $name];

        $metadata = array_merge($_metadata, $metadata);

        $data = [
            "name" => $name,
            "workspace" => $this->workspaceId,
            "product_collection" => $this->identityCollectionId,
            "meta_data" => $metadata,
            "customer_product_id" => $customIdentityID
        ];

        $response = $this->client->request('PUT', sprintf('%s/product/v2/product/%s', $this->serverUrl, $identityID), [
            'headers' => $this->getDefaultHeader(), 'json' => $data
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return new Identity($data["name"], $data["id"], $data["customer_product_id"], metadata: $data["meta_data"]);

    }

    public function getIdentityByCustomID(string $customIdentityID): Identity
    {


        $response = $this->client->request('GET', sprintf('%s/product/v2/product/?customer_product_id=%s&workspace=%s', $this->serverUrl, $customIdentityID, $this->workspaceId), [
            'headers' => $this->getDefaultHeader()
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $data = end($data["results"]);
        if(isset($data["name"]) && $data["id"]) {
            return new Identity($data["name"], $data["id"], $data["customer_product_id"], $data["thumb"], $data["meta_data"]);
        }

        throw new DataErrorException("Identity not found");

    }

    public function getIdentity(string $identityID): Identity
    {
        $response = $this->client->request('GET', sprintf('%s/product/v2/product/%s', $this->serverUrl, $identityID), [
            'headers' => $this->getDefaultHeader()
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        //var_dump($data);

        if(isset($data["name"]) && $data["id"]) {
            return new Identity($data["name"], $data["id"], $data["customer_product_id"], $data["thumb"], $data["meta_data"]);
        }

        throw new DataErrorException("Identity not found");


    }

    public function addImageToIdentity(string $identityID, string $imageData): array
    {
        $data = [
            "base64" => 'data:' . $this->getMimeTypeFromImageData($imageData) . ';base64,' . base64_encode($imageData),
            "product" => $identityID,
            "workspace" => $this->workspaceId

        ];

        $response = $this->client->request('POST', sprintf('%s/product/v2/image', $this->serverUrl), [
            'headers' => $this->getDefaultHeader(), 'json' => $data
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    private function getMimeTypeFromImageData(string $imageData): string
    {
        return finfo_buffer(finfo_open(), $imageData, FILEINFO_MIME_TYPE);
    }

    public function getIdentificationByFile(array $imageDataPacks): array
    {
        $urls = [];
        foreach ($imageDataPacks as $key => $imageData) {
            $mimeType = finfo_buffer(finfo_open(), $imageData, FILEINFO_MIME_TYPE);

            $urls[] = [
                "_base64" => 'data:' . $mimeType . ';base64,' . base64_encode($imageData),
                "meta_data" => [
                    "own_id" => $key
                ]
            ];
        }
        $data = ["records" => $urls, "fields_to_return"=> ["customer_product_id","name"], "collection_id" => $this->searchCollectionId];
        $response = $this->client->request('POST', sprintf('%s/identity/v2/identify', $this->serverUrl), [
            'headers' => $this->getDefaultHeader(), 'json' => $data
        ]);


        return $this->processIdentifyResponse($response);
    }

    private function getDefaultHeader(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => sprintf('Token %s', $this->token),
            'collection-id' => $this->searchCollectionId,
        ];
    }

    private function processIdentifyResponse(ResponseInterface $response): array
    {
        $output = [];
        $data = json_decode($response->getBody()->getContents(), true);

        #Iterace analyzovaných obrázků
        foreach ($data['records'] as $record) {
            $faceCollection = new FaceCollection($record["_id"], $record["_width"], $record["_height"], []);
            #Iterace detekovaných identit
            foreach($record['_objects'] as $object) {

                if(!empty($object['_identification']['best_match']['name'])) {
                    $name = $object['_identification']['best_match']['name'];
                } elseif(isset($object['_identification']['best_match']['meta_data']['name'])) {
                    $name = $object['_identification']['best_match']['meta_data']['name'];
                }
                elseif(isset($object['_identification']['best_match']['customer_product_id'])) {
                    $name = $object['_identification']['best_match']['customer_product_id'];
                }
                else {
                    $name = null;
                }


                if (is_null($name)) {
                    $face = new UnknownFace(
                        $object['bound_box'][0],
                        $object['bound_box'][1],
                        $object['bound_box'][2],
                        $object['bound_box'][3],
                        [],
                        ["distance" => $object['_identification']['best_match']['distance']]
                    );
                } else {

                    $identity = new Identity(
                            $name,
                            'unknown',
                            $object['_identification']['best_match']['customer_product_id'],
                            $object['_identification']['best_match']['_url']
                        );

                    $face = new Face(
                        $identity,
                        $object['_identification']['best_match']['distance'],
                        $object['bound_box'][0],
                        $object['bound_box'][1],
                        $object['bound_box'][2],
                        $object['bound_box'][3],
                        []);

                }


                if ($object['_identification']['alternatives']) {
                    foreach ($object['_identification']['alternatives'] as $alternative) {
                        $face->addAlternativeIdentity(new Identity(
                            $alternative['name'] . "(" . $alternative['distance'] . ")",
                            'unknown',
                            'unknown',
                            $alternative['_url']
                        ));
                    }
                }
                $faceCollection->addFace($face);
            }

            $output[$record["meta_data"]["own_id"]] = $faceCollection;

        }

        return $output;

    }
}
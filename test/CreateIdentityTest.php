<?php

namespace Solwee\XimilarFaceRecognition\test;

use PHPUnit\Framework\TestCase;
use Solwee\XimilarFaceRecognition\Identity;

class CreateIdentityTest extends TestCase
{
    private string $serverUrl = "https://api.ximilar.com";
    private static string $bearerToken;

    private string $workspaceId = "66c856e8-95a5-4ebd-8f98-8a7f4ef28403";
    private int $productCollectionId = 20;
    private string $searchCollectionId = "7b6327a0-7af1-44c8-b6e1-7baa0a358a70";

    public static function setUpBeforeClass(): void
    {
        self::$bearerToken = getenv("XIMILAR_AUTH_TOKEN");

        if (self::$bearerToken === false || self::$bearerToken === "") {
            throw new \Exception("No `XIMILAR_AUTH_TOKEN` env variable set - it is needed for auth.");
        }
    }

    public function testCreateIdentity()
    {


        $client = new \Solwee\XimilarFaceRecognition\Client (
            new \GuzzleHttp\Client(),
            $this->serverUrl,
            self::$bearerToken,
            $this->workspaceId,
            $this->searchCollectionId,
            $this->productCollectionId
        );



        $arrayOfIdentityCollections = $client->createIdentity("identityTest-id1", "test number1");

        $this->assertIsArray($arrayOfIdentityCollections);
        $this->assertEquals("identityTest-id1", $arrayOfIdentityCollections["customer_product_id"]);



    }

    public function testGetIdentityByCustomID()
    {


        $client = new \Solwee\XimilarFaceRecognition\Client (
            new \GuzzleHttp\Client(),
            $this->serverUrl,
            self::$bearerToken,
            $this->workspaceId,
            $this->searchCollectionId,
            $this->productCollectionId
        );



        $arrayOfIdentityCollections = $client->getIdentityByCustomID("identityTest-id1");

        $this->assertInstanceOf(Identity::class, $arrayOfIdentityCollections);
        $this->assertEquals("identityTest-id1", $arrayOfIdentityCollections->getCustomId());



    }

    public function testGetIdentity()
    {
        $client = new \Solwee\XimilarFaceRecognition\Client (
            new \GuzzleHttp\Client(),
            $this->serverUrl,
            self::$bearerToken,
            $this->workspaceId,
            $this->searchCollectionId,
            $this->productCollectionId
        );
        $arrayOfIdentityCollections = $client->getIdentity("0fe81e3a-2bb4-47da-a236-e675bd7f421e");
        $this->assertInstanceOf(Identity::class, $arrayOfIdentityCollections);
        $this->assertEquals("identityTest-id1", $arrayOfIdentityCollections->getCustomId());

    }

    public function testAddImageIdentity()
    {
        $client = new \Solwee\XimilarFaceRecognition\Client (
            new \GuzzleHttp\Client(),
            $this->serverUrl,
            self::$bearerToken,
            $this->workspaceId,
            $this->searchCollectionId,
            $this->productCollectionId
        );
        $arrayOfIdentityCollections = $client->addImageToIdentity("0fe81e3a-2bb4-47da-a236-e675bd7f421e", file_get_contents("https://www.transparentnivolby.cz/hrad2023/wp-content/uploads/sites/13/2022/11/andrejbabis.png"));
        $this->assertIsArray($arrayOfIdentityCollections);
    }

}

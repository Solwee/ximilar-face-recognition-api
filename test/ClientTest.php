<?php

namespace Solwee\XimilarFaceRecognition\test;

use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private string $serverUrl = "https://api.ximilar.com";
    private static string $bearerToken;

    public static function setUpBeforeClass(): void
    {
        self::$bearerToken = getenv("XIMILAR_AUTH_TOKEN");

        if (self::$bearerToken === false || self::$bearerToken === "") {
            throw new \Exception("No `XIMILAR_AUTH_TOKEN` env variable set - it is needed for auth.");
        }
    }

    public function testReal()
    {


        $client = new \Solwee\XimilarFaceRecognition\Client (
            new \GuzzleHttp\Client(),
            $this->serverUrl,
            self::$bearerToken
        );

        $imagePaths = [
            "https://d17-a.sdn.cz/d_17/c_img_QL_r/flhVS.jpeg?fl=cro,0,32,799,533%7Cres,1200,,1%7Cjpg,80,,1",
            "https://1884403144.rsc.cdn77.org/foto/vaclav-havel/Zml0LWluLzEwNTF4NjIxL2ZpbHRlcnM6cXVhbGl0eSg4NSkvaW1n/2831207.jpg?v=0&st=yQemfGiMkunyz9faKNljMO6lVSNAKlvbBiZhl3r2mVc&ts=1600812000&e=0",
            //"https://api.solwee.com/data/large-preview/4/29726/0283707353/profimedia-0283707353.jpg"
        ];

        $arrayOfIdentityCollections = $client->getIdentificationByUrl($imagePaths);

        $this->assertIsArray($arrayOfIdentityCollections);
        $this->assertCount(2, $arrayOfIdentityCollections);

        $this->assertInstanceOf(\Solwee\XimilarFaceRecognition\IdentityCollection::class, $arrayOfIdentityCollections["https://d17-a.sdn.cz/d_17/c_img_QL_r/flhVS.jpeg?fl=cro,0,32,799,533%7Cres,1200,,1%7Cjpg,80,,1"]);
        $this->assertInstanceOf(\Solwee\XimilarFaceRecognition\IdentityCollection::class, $arrayOfIdentityCollections["https://1884403144.rsc.cdn77.org/foto/vaclav-havel/Zml0LWluLzEwNTF4NjIxL2ZpbHRlcnM6cXVhbGl0eSg4NSkvaW1n/2831207.jpg?v=0&st=yQemfGiMkunyz9faKNljMO6lVSNAKlvbBiZhl3r2mVc&ts=1600812000&e=0"]);

        $havel2 = $arrayOfIdentityCollections["https://1884403144.rsc.cdn77.org/foto/vaclav-havel/Zml0LWluLzEwNTF4NjIxL2ZpbHRlcnM6cXVhbGl0eSg4NSkvaW1n/2831207.jpg?v=0&st=yQemfGiMkunyz9faKNljMO6lVSNAKlvbBiZhl3r2mVc&ts=1600812000&e=0"];

        $this->assertCount(1, $havel2);

        foreach ($havel2 as $identity) {
            $this->assertInstanceOf(\Solwee\XimilarFaceRecognition\Identity::class, $identity);
        }
    }


    public function testReal2()
    {

        $client = new \Solwee\XimilarFaceRecognition\Client (
            new \GuzzleHttp\Client(),
            $this->serverUrl,
            self::$bearerToken
        );

        $imagePaths = [
            "test1" => "https://showroom.profimedia.com/face_test1.jpeg",
            "test2" => "https://showroom.profimedia.com/face_test1.jpeg",

        ];

        $dataPacks = [];
        foreach ($imagePaths as $key => $imagePath) {
            $dataPacks[$key] = file_get_contents($imagePath);
        }

        $arrayOfIdentityCollections = $client->getIdentificationByFile($dataPacks);

        $this->assertIsArray($arrayOfIdentityCollections);

        //var_dump($arrayOfIdentityCollections);

    }

}

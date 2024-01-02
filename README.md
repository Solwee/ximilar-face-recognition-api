# ximilar-face-recognition-api



## Getting started

### Installation

To install latest version of `solwee/ximilar-face-recognition-api` use [Composer](https://getcomposer.org).

### Documentation

### Example

```php
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

        $arrayOfIdentityCollections = $client->getIdentification($imagePaths);
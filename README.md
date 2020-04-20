# Luigi's Box Bundle
[Luigi's Box](https://www.luigisbox.com/) integration for Symfony.
Luigi's Box documentation can be found here: https://live.luigisbox.com/.

Installation
------------

* install with Composer
```
composer require answear/luigis-box-bundle
```
* consider using specific version (ex. `0.1.1`, `0.1.2`) until we provide `1.0.0` version (`^1.0.0`)

Setup
------------
* provide required config data: `publicKey` and `privateKey`
```yaml
# config/packages/answear_luigis_box.yaml
answear_luigis_box:
    host: 'https://live.luigisbox.com' #default
    publicKey: 'your_public_key'
    privateKey: 'your_private_key'
    connectionTimeout: 5.0 #default
    requestTimeout: 5.0 #default
```

config will be passed to `\Answear\LuigisBoxBundle\Service\ConfigProvider` class.

Usage
------------
1. Full [content update](https://live.luigisbox.com/?php#content-updates-content-update) document
```php
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;

// ...

$collection = new ContentUpdateCollection([new ContentUpdate('product/url', 'object type', ['title' => 'product title'])]);

/** @var \Answear\LuigisBoxBundle\Service\Request $request **/
$apiResponse = $request->contentUpdate($collection);
```

2. [Partial update](https://live.luigisbox.com/?php#content-updates-partial-content-update)
```php
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\PartialContentUpdate;

// ...

$collection = new ContentUpdateCollection([new PartialContentUpdate('product/url', 'object type', ['title' => 'product title'])]);

/** @var \Answear\LuigisBoxBundle\Service\Request $request **/
$apiResponse = $request->partialContentUpdate($collection);
```

3. [Content removal](https://live.luigisbox.com/?php#content-updates-content-removal)
```php
use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;

// ...

$collection = new ContentRemovalCollection([new ContentRemoval('product/url')]);

/** @var \Answear\LuigisBoxBundle\Service\Request $request **/
$apiResponse = $request->contentRemoval($collection);
```

4. Change availability

Additional method to simply enable/disable objects - partial update will be used.
```php
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;

// ...

$isAvailable = true;
$collection = new ContentAvailabilityCollection([new ContentAvailability('product/url', $isAvailable)]);

/** @var \Answear\LuigisBoxBundle\Service\Request $request **/
$apiResponse = $request->changeAvailability($collection);

// ... or pass one object

$isAvailable = true;
/** @var \Answear\LuigisBoxBundle\Service\Request $request **/
$apiResponse = $request->changeAvailability(new ContentAvailability('product/url', $isAvailable));
```

---
In all request you can catch some exceptions:
* `BadRequestException` - bad request,
* `TooManyItemsException` - make request with fewer items,
* `MalformedResponseException` - something went wrong with Luigi's Box api response,
* `TooManyRequestsException` - delay request rate,
* `ServiceUnavailableException`

Consider catching them separately:
```php

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\TooManyRequestsException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;

try {
    // ... request
} catch (BadRequestException $e){
    //bad request
    $request = $e->getRequest();
    $response = $e->getResponse();
} catch (TooManyItemsException $e){
    //items limit reached
    $limit = $e->getLimit();
} catch (MalformedResponseException $e){
    //bad response 
    $response = $e->getResponse();
} catch (TooManyRequestsException $e){
    //repeat request after $retryAfter seconds
    $retryAfter = $e->getRetryAfterSeconds();
} catch (ServiceUnavailableException $e){
    //delay request
}

```

Response
------------

`\Answear\LuigisBoxBundle\Response\ApiResponse`:
* (bool) `$success` - `true` if all documents will be passed successfully,
* (int) `$okCount` - number of successfully passed documents,
* (int) `$errorsCount` - number of failed documents,
* (array) `$errors` - array of `\Answear\LuigisBoxBundle\Response\ApiResponseError` objects,
* (array) `$rawResponse` - decoded response from api.

`ApiResponseError`:
* (string) `$url` - url of document
* (string) `$type` - type of error (ex. `malformed_input`)
* (string) `$reason` - failure text (ex. `incorrect object format`)
* (array|null) `$causedBy` - specific reason of error (ex. `["url": ["is missing"]]`)


Note!

If some document will fail, `ApiResponse::$success` will be `false`. Check `$okCount` if you want to know how many documents were been updated and `$errors` to check exactly which documents fails.


Final notes
------------

Feel free to make pull requests with new features, improvements or bug fixes. The Answear team will be grateful for any comments.


### Have fun!

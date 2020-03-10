# Luigi's Box Bundle
[Luigi's Box](https://www.luigisbox.com/) integration for Symfony.
Luigi's Box documentation can be found here: https://live.luigisbox.com/.

Installation
------------

* install with Composer
```
composer require answear/luigis-box-bundle
```
* consider to use specific version (ex. `0.1.1`, `0.1.2`) until we provide `1.0.0` version (`^1.0.0`)

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

Additional method to simply enable/disable object - used partial update.
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
* `ToManyItemsException` - make request with less items,
* `MalformedResponseException` - something went wrong with Luigi's Box api response,
* `ToManyRequestsException` - delay request rate,
* `ServiceUnavailableException`

Consider to catch them separately:
```php

use Answear\LuigisBoxBundle\Exception\ToManyItemsException;
use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ToManyRequestsException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;

try {
    // ... request
} catch (ToManyItemsException $e){
    //items limit reached
    $limit = $e->getLimit();
} catch (MalformedResponseException $e){
    //bad response 
    $textResponse = $e->getResponse();
} catch (ToManyRequestsException $e){
    //consider to repeat request after $retryAfter seconds
    $retryAfter = $e->getRetryAfterSeconds();
} catch (ServiceUnavailableException $e){
    //consider to delay request
}

```

Response
------------

//TODO - write it

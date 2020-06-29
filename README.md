# Luigi's Box Bundle
[Luigi's Box](https://www.luigisbox.com/) integration for Symfony.
Luigi's Box documentation can be found here: https://live.luigisbox.com/.

Installation
------------

* install with Composer
```
composer require answear/luigis-box-bundle
```

Setup
------------
* provide required config data: `publicKey` and `privateKey`
```yaml
# config/packages/answear_luigis_box.yaml
answear_luigis_box:
    host: 'https://live.luigisbox.com' #default
    publicKey: 'your_public_key'
    privateKey: 'your_private_key'
    connectionTimeout: 10.0 #default
    requestTimeout: 10.0 #default
```

config will be passed to `\Answear\LuigisBoxBundle\Service\ConfigProvider` class.

Usage
------------
1. Full [content update](https://live.luigisbox.com/?php#content-updates-content-update) document
```php
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;

// ...

$collection = new ContentUpdateCollection([new ContentUpdate('product title', 'product/url', 'object type', ['field' => 'field 1'])]);

/** @var \Answear\LuigisBoxBundle\Service\RequestInterface $request **/
$apiResponse = $request->contentUpdate($collection);
```

First argument (`$title`) will be used as product's title in Luigi's Box unless a `title` field is present in the `$fields` argument. 

2. [Partial update](https://live.luigisbox.com/?php#content-updates-partial-content-update)
```php
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\PartialContentUpdate;

// ...

$collection = new ContentUpdateCollection([new PartialContentUpdate('product/url', 'object type', ['title' => 'product title'])]);

/** @var \Answear\LuigisBoxBundle\Service\RequestInterface $request **/
$apiResponse = $request->partialContentUpdate($collection);
```

3. [Content removal](https://live.luigisbox.com/?php#content-updates-content-removal)
```php
use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;

// ...

$collection = new ContentRemovalCollection([new ContentRemoval('product/url', 'product')]);

/** @var \Answear\LuigisBoxBundle\Service\RequestInterface $request **/
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

/** @var \Answear\LuigisBoxBundle\Service\RequestInterface $request **/
$apiResponse = $request->changeAvailability($collection);

// ... or pass one object

$isAvailable = true;
/** @var \Answear\LuigisBoxBundle\Service\RequestInterface $request **/
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

#### Content response

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

`ApiResponse::$success` will be set to `false` if any of passed documents fails. Check `$okCount` if you want to know how many documents were updated and `$errors` to check exactly which documents failed.

### Searching (documentation [here](https://live.luigisbox.com/#search-as-a-service))

1. Request

```php
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;

// ...

$page = 3;
$urlBuilder = new SearchUrlBuilder($page);
$urlBuilder
    ->setQuery('nice top')
    ->addFilter('type', 'product')
    ->addFilter('category', 'top')
    ->addFilter('brand', 'Medicine')
    ->addFilter('brand', 'Answear')
    ->addPrefer('brand', 'Answear')
    ->setSort('size', 'asc');

//the above code produces a url query like `size=10&page=3&q=nice+top&f%5B0%5D=type%3Aproduct&f%5B1%5D=category%3Atop&f%5B2%5D=brand%3AMedicine&f%5B3%5D=brand%3AAnswear&sort=size%3Aasc&prefer%5B0%5D=brand%3AAnswear`

/** @var \Answear\LuigisBoxBundle\Service\SearchRequestInterface $request **/
$searchResponse = $request->search($urlBuilder);

```
Check the Luigi's Box documentation to find out exact purpose of each field `SearchUrlBuilder` is exposing.

2. Response

`SearchRequest::search()` will return a `SearchResponse` object with following fields:
* (string) $searchUrl
* (string) $query
* (string|null) $correctedQuery
* (array) $filters
* (Hit[]) $hits
    
    `Hit`:
    * (string) $url;
    * (array) $attributes;
    * (array) $nested;
    * (string) $type;
    * (array) $highlight;
    * (bool) $exact;
    * (bool) $alternative;
* (Hit[]) $quickSearchHits
    * like above
* (Facet[]) $facets
    
    `Facet`:
    * (string) $name;
    * (string) $type;
    * (array) $values;
* (int) $totalHits
* (int) $currentSize


Final notes
------------

Feel free to make pull requests with new features, improvements or bug fixes. The Answear team will be grateful for any comments.


### Have fun!

v3.x
===================

* 3.1.0
  * allow dynamic configuration
  * add support for dynamic facets

* 3.0.0
  * drop Support for Symfony < 5.4
  * add support for Symfony 6

v2.x
===================

* 2.5.0
  * allowed change search cache ttl: added `searchCacheTtl` option with default 0 (no cache)

* 2.4.0
  * `guzzlehttp` is used instead of `guzzle adapter`

* 2.3.0
  * `symfony/*` dependency updated

* 2.2.0
  * added `(array) $headers` to ConfigProvider. You can now pass some headers on searching.
  * (Deprecated) `ConfigProvider::getRequestHeaders()` method. Use `ConfigProvider::getAuthorizationHeaders` if you need to. 

* 2.1.0
  * added `searchTimeout` option (default 6 seconds) to split timeouts between API updates and searching.
  * revert default value for `requestTimeout` to 10 seconds

* 2.0.0
  * [BC BREAK] change default connection timeout from 10s to 4s, request timeout from 10s to 8s
  * [BC BREAK] change bundle configuration:

    from
    ```php
    answear_luigis_box:
      publicKey: 'public'
      privateKey: 'private'
    ```
    to
    ```php
    answear_luigis_box:
      configs:
          your_config_name:
              publicKey: 'public'
              privateKey: 'private'
    ```
    See [README](README.md) for more details.

v1.x
===================
* 1.4.0
  * [BC BREAK] remove casting bool values in url filters.

* 1.3.0
  * (feature) Update by query

* 1.2.0
  * Added \Answear\LuigisBoxBundle\Service\RequestInterface for use instead of Request service
  * Added \Answear\LuigisBoxBundle\Service\SearchRequestInterface for use instead of SearchRequest service

* 1.1.0
  * (feature) Searching

* 1.0.0 (2020-05-08)
  * (feature) Content update
  * (feature) Partial content update
  * (feature) Content availability
  * (feature) Content removal

v0.x
===================

* 0.1.0 (2020-03-02)
  * starting application

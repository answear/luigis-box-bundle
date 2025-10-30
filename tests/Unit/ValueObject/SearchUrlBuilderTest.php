<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\ValueObject;

use Answear\LuigisBoxBundle\ValueObject\Search\Context;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SearchUrlBuilderTest extends TestCase
{
    #[Test]
    public function buildValidUrlTest(): void
    {
        $query = [
            'size' => '10',
            'page' => '1',
        ];

        $searchBuilder = new SearchUrlBuilder();
        $this->assertOk($query, $searchBuilder);

        $searchBuilder = new SearchUrlBuilder(3);
        $query['page'] = '3';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setQuery('to search query');
        $query['q'] = 'to search query';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setUserId('user-id');
        $searchBuilder->setClientId('client-id');
        $searchBuilder->enableQueryUnderstanding();
        $query['qu'] = '1';
        $query['user_id'] = 'user-id';
        $query['client_id'] = 'client-id';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->addFilter('category', 'Top & Top');
        $query['f[]'] = 'category:Top & Top';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->addFilter('category', 'Jeans');
        $query['f[]'] = [
            'category:Top & Top',
            'category:Jeans',
        ];
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->addFilter('available', false);
        $query['f[]'] = [
            'category:Top & Top',
            'category:Jeans',
            'available:false',
        ];
        $this->assertOk($query, $searchBuilder);

        $this->assertSame(
            'size=10&page=3&q=to+search+query&qu=1&user_id=user-id&client_id=client-id&f%5B%5D=category%3ATop+%26+Top&f%5B%5D=category%3AJeans&f%5B%5D=available%3Afalse',
            $searchBuilder->toUrlQuery()
        );

        $searchBuilder->resetFilters();
        $searchBuilder->setFilters(
            [
                'brand' => 'Elo & Hot16',
                'category' => ['Top', 'Jeans'],
                'price' => '5|2',
            ]
        );
        $query['f[]'] = [
            'brand:Elo & Hot16',
            'category:Top',
            'category:Jeans',
            'price:5|2',
        ];
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->addMustFilter('category', 'Hannah');
        $query['f_must[]'] = 'category:Hannah';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->resetMustFilters();
        $query['f_must[]'] = [
            'brand:Brand16',
            'category:George',
            'category:Prince',
        ];
        $searchBuilder->setMustFilters(
            [
                'brand' => 'Brand16',
                'category' => ['George', 'Prince'],
            ]
        );
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setSize(13);
        $query['size'] = '13';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setSort('price', 'asc');
        $query['sort'] = 'price:asc';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setQuicksearchTypes(['price', 'title']);
        $query['quicksearch_types'] = 'price,title';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setFacets(['brand', 'attribute']);
        $query['facets'] = 'brand,attribute';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setDynamicFacetsSize(5);
        $query['dynamic_facets_size'] = '5';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setFixits(false);
        $query['use_fixits'] = '0';
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->addPrefer('category', 'Gadgets');
        $searchBuilder->addPrefer('category', 'Ona');
        $query['prefer[]'] = [
            'category:Gadgets',
            'category:Ona',
        ];
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setPreferArray(
            [
                'category' => 'Gadgets',
                'type' => ['Products', 'Category'],
            ]
        );
        $query['prefer[]'] = [
            'category:Gadgets',
            'type:Products',
            'type:Category',
        ];
        $this->assertOk($query, $searchBuilder);

        $searchBuilder->setHitFields(['brand', 'attribute']);
        $query['hit_fields'] = 'brand,attribute';
        $this->assertOk($query, $searchBuilder);

        $context = new Context();
        $context->setGeoLocation(12.31, 24.271);
        $context->setGeoLocationField('geolocation');
        $context->setAvailabilityField('availability');
        $context->setBoostField('boost');
        $context->setFreshnessField('freshness');
        $searchBuilder->setContext($context);
        $query['context[geo_location]'] = '12.31,24.271';
        $query['context[geo_location_field]'] = 'geolocation';
        $query['context[availability_field]'] = 'availability';
        $query['context[boost_field]'] = 'boost';
        $query['context[freshness_field]'] = 'freshness';
        $this->assertOk($query, $searchBuilder);

        // simply check url string
        $this->assertSame(
            'size=13&page=3&q=to+search+query&qu=1&user_id=user-id&client_id=client-id&f%5B%5D=brand%3AElo+%26+Hot16&f%5B%5D=category%3ATop&f%5B%5D=category%3AJeans&f%5B%5D=price%3A5%7C2&f_must%5B%5D=brand%3ABrand16&f_must%5B%5D=category%3AGeorge&f_must%5B%5D=category%3APrince&sort=price%3Aasc&quicksearch_types=price%2Ctitle&facets=brand%2Cattribute&dynamic_facets_size=5&use_fixits=0&prefer%5B%5D=category%3AGadgets&prefer%5B%5D=type%3AProducts&prefer%5B%5D=type%3ACategory&hit_fields=brand%2Cattribute&context%5Bgeo_location%5D=12.31%2C24.271&context%5Bgeo_location_field%5D=geolocation&context%5Bavailability_field%5D=availability&context%5Bboost_field%5D=boost&context%5Bfreshness_field%5D=freshness',
            $searchBuilder->toUrlQuery()
        );
    }

    private function assertOk(array $query, SearchUrlBuilder $searchBuilder): void
    {
        $this->assertSame($query, $this->parse($searchBuilder->toUrlQuery()));
    }

    /**
     * @see \GuzzleHttp\Psr7\Query::parse
     */
    private function parse(string $string): array
    {
        $decoder = static function ($value) {
            return rawurldecode(str_replace('+', ' ', (string) $value));
        };

        $result = [];
        foreach (explode('&', $string) as $kvp) {
            $parts = explode('=', $kvp, 2);
            $key = $decoder($parts[0]);
            $value = isset($parts[1]) ? $decoder($parts[1]) : null;
            if (!isset($result[$key])) {
                $result[$key] = $value;
            } else {
                if (!is_array($result[$key])) {
                    $result[$key] = [$result[$key]];
                }
                $result[$key][] = $value;
            }
        }

        return $result;
    }
}

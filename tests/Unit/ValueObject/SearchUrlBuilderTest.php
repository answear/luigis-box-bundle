<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\ValueObject;

use Answear\LuigisBoxBundle\ValueObject\Search\Context;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use PHPUnit\Framework\TestCase;

class SearchUrlBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function buildValidUrlTest(): void
    {
        $searchBuilder = new SearchUrlBuilder('123-345');
        $this->assertSame('size=10&tracker_id=123-345&page=1', $searchBuilder->toUrl());

        $searchBuilder = new SearchUrlBuilder('123-345', 3);
        $this->assertSame('size=10&tracker_id=123-345&page=3', $searchBuilder->toUrl());

        $searchBuilder->setQuery('to search query');
        $this->assertSame('size=10&tracker_id=123-345&page=3&q=to+search+query', $searchBuilder->toUrl());

        $searchBuilder->setUserId('user-id');
        $searchBuilder->enableQueryUnderstanding();
        $this->assertSame(
            'size=10&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id',
            $searchBuilder->toUrl()
        );

        $searchBuilder->addFilter('category', 'Top & Top');
        $this->assertSame(
            'size=10&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=category%3ATop+%26+Top',
            $searchBuilder->toUrl()
        );

        $searchBuilder->addFilter('category', 'Jeans');
        $this->assertSame(
            'size=10&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=category%3ATop+%26+Top&f%5B1%5D=category%3AJeans',
            $searchBuilder->toUrl()
        );

        $searchBuilder->setFilters(
            [
                'brand' => 'Elo & Hot16',
                'category' => ['Top', 'Jeans'],
                'price' => '5|2',
            ]
        );
        $this->assertSame(
            'size=10&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2',
            $searchBuilder->toUrl()
        );

        $searchBuilder->setSize(13);
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2',
            $searchBuilder->toUrl()
        );

        $searchBuilder->setSort('price', 'asc');
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2&sort=price%3Aasc',
            $searchBuilder->toUrl()
        );

        $searchBuilder->setQuicksearchTypes(['price', 'title']);
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2&sort=price%3Aasc&quicksearch_types=price%2Ctitle',
            $searchBuilder->toUrl()
        );

        $searchBuilder->setFacets(['brand', 'attribute']);
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2&sort=price%3Aasc&quicksearch_types=price%2Ctitle&facets=brand%2Cattribute',
            $searchBuilder->toUrl()
        );

        $searchBuilder->setFixits(false);
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2&sort=price%3Aasc&quicksearch_types=price%2Ctitle&facets=brand%2Cattribute&use_fixits=0',
            $searchBuilder->toUrl()
        );

        $searchBuilder->addPrefer('category', 'Gadgets');
        $searchBuilder->addPrefer('category', 'Ona');
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2&sort=price%3Aasc&quicksearch_types=price%2Ctitle&facets=brand%2Cattribute&use_fixits=0&prefer%5B0%5D=category%3AGadgets&prefer%5B1%5D=category%3AOna',
            $searchBuilder->toUrl()
        );

        $searchBuilder->setPreferArray(
            [
                'category' => 'Gadgets',
                'type' => ['Products', 'Category'],
            ]
        );
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2&sort=price%3Aasc&quicksearch_types=price%2Ctitle&facets=brand%2Cattribute&use_fixits=0&prefer%5B0%5D=category%3AGadgets&prefer%5B1%5D=type%3AProducts&prefer%5B2%5D=type%3ACategory',
            $searchBuilder->toUrl()
        );

        $searchBuilder->setHitFields(['brand', 'attribute']);
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2&sort=price%3Aasc&quicksearch_types=price%2Ctitle&facets=brand%2Cattribute&use_fixits=0&prefer%5B0%5D=category%3AGadgets&prefer%5B1%5D=type%3AProducts&prefer%5B2%5D=type%3ACategory&hit_fields=brand%2Cattribute',
            $searchBuilder->toUrl()
        );

        $context = new Context();
        $context->setGeoLocation(12.31, 24.271);
        $context->setGeoLocationField('geolocation');
        $context->setAvailabilityField('availability');
        $context->setBoostField('boost');
        $context->setFreshnessField('freshness');
        $searchBuilder->setContext($context);
        $this->assertSame(
            'size=13&tracker_id=123-345&page=3&q=to+search+query&qu=1&user_id=user-id&f%5B0%5D=brand%3AElo+%26+Hot16&f%5B1%5D=category%3ATop&f%5B2%5D=category%3AJeans&f%5B3%5D=price%3A5%7C2&sort=price%3Aasc&quicksearch_types=price%2Ctitle&facets=brand%2Cattribute&use_fixits=0&prefer%5B0%5D=category%3AGadgets&prefer%5B1%5D=type%3AProducts&prefer%5B2%5D=type%3ACategory&hit_fields=brand%2Cattribute&context%5Bgeo_location%5D=12.31%2C24.271&context%5Bgeo_location_field%5D=geolocation&context%5Bavailability_field%5D=availability&context%5Bboost_field%5D=boost&context%5Bfreshness_field%5D=freshness',
            $searchBuilder->toUrl()
        );
    }
}
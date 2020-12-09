<?php

declare(strict_types=1);

namespace Tests\FrontendApiBundle\Functional\FriendlyUrl;

use Tests\FrontendApiBundle\Test\GraphQlTestCase;

class FriendlyUrlTest extends GraphQlTestCase
{
    public function testGetEntityNameByFriendlyUrl(): void
    {
        foreach ($this->getEntityNameByFriendlyUrlProvider() as $dataSet) {
            $graphQlType = $dataSet['graphQlType'];
            $urlSlug = $dataSet['urlSlug'];
            $expectedName = $dataSet['expectedName'];

            $query = $this->getQuery($graphQlType, $urlSlug);
            $response = $this->getResponseContentForQuery($query);
            $this->assertResponseContainsArrayOfDataForGraphQlType($response, $graphQlType);
            $responseData = $this->getResponseDataForGraphQlType($response, $graphQlType);

            $this->assertArrayHasKey('name', $responseData);
            $this->assertSame($expectedName, $responseData['name']);
        }
    }

    public function testFriendlyUrlNotFoundForRouteBySlug(): void
    {
        foreach ($this->getFriendlyUrlNotFoundForRouteBySlug() as $dataSet) {
            $graphQlType = $dataSet['graphQlType'];
            $urlSlug = $dataSet['urlSlug'];
            $errorMessage = $dataSet['errorMessage'];

            $query = $this->getQuery($graphQlType, $urlSlug);
            $response = $this->getResponseContentForQuery($query);
            $this->assertResponseContainsArrayOfErrors($response);
            $errors = $this->getErrorsFromResponse($response);

            $this->assertArrayHasKey(0, $errors);
            $this->assertArrayHasKey('message', $errors[0]);
            $this->assertSame($errorMessage, $errors[0]['message']);
        }
    }

    /**
     * @param string $graphQlType
     * @param string $urlSlug
     * @return string
     */
    private function getQuery(string $graphQlType, string $urlSlug): string
    {
        return '
            query {
                ' . $graphQlType . ' (urlSlug: "' . $urlSlug . '") {
                    name
                }
            }
        ';
    }

    /**
     * @return array
     */
    private function getEntityNameByFriendlyUrlProvider(): array
    {
        return [
            [
                'graphQlType' => 'brand',
                'urlSlug' => 'canon/',
                'expectedName' => t('Canon', [], 'dataFixtures', $this->getFirstDomainLocale()),
            ],
            [
                'graphQlType' => 'brand',
                'urlSlug' => 'canon',
                'expectedName' => t('Canon', [], 'dataFixtures', $this->getFirstDomainLocale()),
            ],
            [
                'graphQlType' => 'article',
                'urlSlug' => 'terms-and-conditions/',
                'expectedName' => t('Terms and conditions', [], 'dataFixtures', $this->getFirstDomainLocale()),
            ],
            [
                'graphQlType' => 'category',
                'urlSlug' => 'electronics/',
                'expectedName' => t('Electronics', [], 'dataFixtures', $this->getFirstDomainLocale()),
            ],
            [
                'graphQlType' => 'product',
                'urlSlug' => 'canon-mg3550/',
                'expectedName' => t('Canon MG3550', [], 'dataFixtures', $this->getFirstDomainLocale()),
            ],
        ];
    }

    /**
     * @return array
     */
    private function getFriendlyUrlNotFoundForRouteBySlug(): array
    {
        return [
            [
                'graphQlType' => 'brand',
                'urlSlug' => 'canonNotExist/',
                'errorMessage' => 'Friendly url not found for route `front_brand_detail` by slug `canonNotExist/` on domain `1`',
            ],
            [
                'graphQlType' => 'article',
                'urlSlug' => 'termsAndConditionsNotExist/',
                'errorMessage' => 'Article with URL slug `termsAndConditionsNotExist/` does not exist.',
            ],
            [
                'graphQlType' => 'category',
                'urlSlug' => 'electronicsNotExist/',
                'errorMessage' => 'Friendly url not found for route `front_product_list` by slug `electronicsNotExist/` on domain `1`',
            ],
            [
                'graphQlType' => 'product',
                'urlSlug' => 'canon-mg3550NotExist/',
                'errorMessage' => 'Friendly url not found for route `front_product_detail` by slug `canon-mg3550NotExist/` on domain `1`',
            ],
        ];
    }
}

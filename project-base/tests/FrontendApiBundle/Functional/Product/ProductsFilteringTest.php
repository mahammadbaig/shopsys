<?php

declare(strict_types=1);

namespace Tests\FrontendApiBundle\Functional\Product;

use App\DataFixtures\Demo\BrandDataFixture;
use App\DataFixtures\Demo\CategoryDataFixture;
use App\DataFixtures\Demo\FlagDataFixture;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterFacade;
use Tests\FrontendApiBundle\Test\GraphQlTestCase;

class ProductsFilteringTest extends GraphQlTestCase
{
    private const PARAMETER_NUMBER_OF_BUTTONS_ID = 9;

    /**
     * @var string
     */
    private string $firstDomainLocale;

    public function setUp(): void
    {
        parent::setUp();

        $this->firstDomainLocale = $this->getLocaleForFirstDomain();
    }

    public function testFilterByBrand(): void
    {
        $brand = $this->getReference(BrandDataFixture::BRAND_APPLE);

        $query = '
            query {
                products (first: 1, filter: { brands: ["' . $brand->getUuid() . '"] }) {
                    edges {
                        node {
                            name
                        }
                    }
                }
            }
        ';

        $productsExpected = [
            ['name' => t('Apple iPhone 5S 64GB, gold', [], 'dataFixtures', $this->firstDomainLocale)],
        ];

        $this->assertProductsFound($query, 'products', $productsExpected);
    }

    public function testFilterByFlag(): void
    {
        $flag = $this->getReference(FlagDataFixture::FLAG_ACTION_PRODUCT);

        $query = '
            query {
                products (first: 1, filter: { flags: ["' . $flag->getUuid() . '"] }) {
                    edges {
                        node {
                            name
                        }
                    }
                }
            }
        ';

        $productsExpected = [
            ['name' => t('22" Sencor SLE 22F46DM4 HELLO KITTY', [], 'dataFixtures', $this->firstDomainLocale)],
        ];

        $this->assertProductsFound($query, 'products', $productsExpected);
    }

    public function testFilterByMinimalPrice(): void
    {
        $minimalPrice = $this->getFormattedMoneyAmountConvertedToDomainDefaultCurrency('75000');

        $query = '
            query {
                products (first: 1, filter: { minimalPrice: "' . $minimalPrice . '" }) {
                    edges {
                        node {
                            name
                        }
                    }
                }
            }
        ';

        $productsExpected = [
            ['name' => t('OKI MC861cdxm', [], 'dataFixtures', $this->firstDomainLocale)],
        ];

        $this->assertProductsFound($query, 'products', $productsExpected);
    }

    public function testFilterByMaximalPrice(): void
    {
        $maximalPrice = $this->getFormattedMoneyAmountConvertedToDomainDefaultCurrency('2500');

        $query = '
            query {
                products (last: 1, filter: { maximalPrice: "' . $maximalPrice . '" }) {
                    edges {
                        node {
                            name
                        }
                    }
                }
            }
        ';

        $productsExpected = [
            ['name' => t(
                'ZN-8009 steam iron Ferrato stainless steel 2200 Watt Blue',
                [],
                'dataFixtures',
                $this->firstDomainLocale
            )],
        ];

        $this->assertProductsFound($query, 'products', $productsExpected);
    }

    public function testFilterByParameter(): void
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_ELECTRONICS);

        $parameterFacade = $this->getContainer()->get(ParameterFacade::class);
        $parameter = $parameterFacade->getById(self::PARAMETER_NUMBER_OF_BUTTONS_ID);

        $parameterValue = $parameterFacade->getParameterValueByValueTextAndLocale(
            t('5', [], 'dataFixtures', $this->firstDomainLocale),
            $this->firstDomainLocale
        );

        $query = '
            query {
                category (uuid: "' . $category->getUuid() . '") {
                    products (
                        first: 1,
                        filter: {
                            parameters: [
                                {
                                    parameter: "' . $parameter->getUuid() . '",
                                    values: [
                                        "' . $parameterValue->getUuid() . '"
                                    ]
                                }
                            ]
                        }
                    ) {
                        edges {
                            node {
                                name
                            }
                        }
                    },
                }
            }
        ';

        $productsExpected = [
            ['name' => t(
                'A4tech mouse X-710BK, OSCAR Game, 2000DPI, black,',
                [],
                'dataFixtures',
                $this->firstDomainLocale
            )],
        ];

        $this->assertProductsFound($query, 'category', $productsExpected);
    }

    /**
     * @param string $query
     * @param string $graphQlType
     * @param array $productsExpected
     */
    private function assertProductsFound(string $query, string $graphQlType, array $productsExpected): void
    {
        $response = $this->getResponseContentForQuery($query);

        $this->assertResponseContainsArrayOfDataForGraphQlType($response, $graphQlType);
        $responseData = $this->getResponseDataForGraphQlType($response, $graphQlType);

        if ($graphQlType !== 'products') {
            $responseData = $responseData['products'];
        }

        $this->assertArrayHasKey('edges', $responseData);

        $queryResult = [];
        foreach ($responseData['edges'] as $edge) {
            $this->assertArrayHasKey('node', $edge);
            $queryResult[] = $edge['node'];
        }

        $this->assertEquals($productsExpected, $queryResult, json_encode($queryResult));
    }
}

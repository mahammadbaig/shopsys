<?php

declare(strict_types=1);

namespace Tests\FrontendApiBundle\Functional\Product;

use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Tests\FrontendApiBundle\Test\GraphQlTestCase;

class ProductsFilteringOptionsTest extends GraphQlTestCase
{
    private const CATEGORY_ELECTRONICS_ID = 2;

    /**
     * @var string
     */
    private string $firstDomainLocale;

    public function setUp(): void
    {
        parent::setUp();

        $this->firstDomainLocale = $this->getLocaleForFirstDomain();
    }

    public function testGetElectornicsFilterOptions(): void
    {
        $categoryFacade = $this->getContainer()->get(CategoryFacade::class);
        $category = $categoryFacade->getById(self::CATEGORY_ELECTRONICS_ID);

        $query = '
            query {
                category (uuid: "' . $category->getUuid() . '") {
                    products {
                        productFilterOptions {
                            flags {
                                flag {
                                    name
                                }
                                count
                                isAbsolute
                            },
                            brands {
                                brand {
                                    name
                                }
                                count
                                isAbsolute
                            },
                            inStock,
                            minimalPrice,
                            maximalPrice,
                            parameters {
                                parameter {
                                    name
                                }
                                values {
                                    value {
                                        text
                                    }
                                    count
                                    isAbsolute
                                }
                            }
                        }
                    },
                }
            }
        ';

        $minimalPrice = $this->getFormattedMoneyAmountConvertedToDomainDefaultCurrency('318.75');
        $maximalPrice = $this->getFormattedMoneyAmountConvertedToDomainDefaultCurrency('21590');

        $expectedResult = '{
    "data": {
        "category": {
            "products": {
                "productFilterOptions": {
                    "flags": [
                        {
                            "flag": {
                                "name": "' . t('Action', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "count": 1,
                            "isAbsolute": true
                        },
                        {
                            "flag": {
                                "name": "' . t('TOP', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "count": 2,
                            "isAbsolute": true
                        }
                    ],
                    "brands": [
                        {
                            "brand": {
                                "name": "' . t('A4tech', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "count": 1,
                            "isAbsolute": true
                        },
                        {
                            "brand": {
                                "name": "' . t('LG', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "count": 1,
                            "isAbsolute": true
                        },
                        {
                            "brand": {
                                "name": "' . t('Philips', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "count": 1,
                            "isAbsolute": true
                        },
                        {
                            "brand": {
                                "name": "' . t('Sencor', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "count": 1,
                            "isAbsolute": true
                        }
                    ],
                    "inStock": 4,
                    "minimalPrice": "' . $minimalPrice . '",
                    "maximalPrice": "' . $maximalPrice . '",
                    "parameters": [
                        {
                            "parameter": {
                                "name": "' . t('Ergonomics', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t(
            'Right-handed',
            [],
            'dataFixtures',
            $this->firstDomainLocale
        ) . '"
                                    },
                                    "count": 1,
                                    "isAbsolute": true
                                }
                            ]
                        },
                        {
                            "parameter": {
                                "name": "' . t('Gaming mouse', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t('Yes', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 1,
                                    "isAbsolute": true
                                }
                            ]
                        },
                        {
                            "parameter": {
                                "name": "' . t('HDMI', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t('No', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 2,
                                    "isAbsolute": true
                                },
                                {
                                    "value": {
                                        "text": "' . t('Yes', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 1,
                                    "isAbsolute": true
                                }
                            ]
                        },
                        {
                            "parameter": {
                                "name": "' . t('Number of buttons', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t('5', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 1,
                                    "isAbsolute": true
                                }
                            ]
                        },
                        {
                            "parameter": {
                                "name": "' . t('Resolution', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t(
            '1920×1080 (Full HD)',
            [],
            'dataFixtures',
            $this->firstDomainLocale
        ) . '"
                                    },
                                    "count": 3,
                                    "isAbsolute": true
                                }
                            ]
                        },
                        {
                            "parameter": {
                                "name": "' . t('Screen size', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t('27\"', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 1,
                                    "isAbsolute": true
                                },
                                {
                                    "value": {
                                        "text": "' . t('30\"', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 1,
                                    "isAbsolute": true
                                },
                                {
                                    "value": {
                                        "text": "' . t('47\"', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 1,
                                    "isAbsolute": true
                                }
                            ]
                        },
                        {
                            "parameter": {
                                "name": "' . t('Supported OS', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t(
            'Windows 2000/XP/Vista/7',
            [],
            'dataFixtures',
            $this->firstDomainLocale
        ) . '"
                                    },
                                    "count": 1,
                                    "isAbsolute": true
                                }
                            ]
                        },
                        {
                            "parameter": {
                                "name": "' . t('Technology', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t('LED', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 3,
                                    "isAbsolute": true
                                }
                            ]
                        },
                        {
                            "parameter": {
                                "name": "' . t('USB', [], 'dataFixtures', $this->firstDomainLocale) . '"
                            },
                            "values": [
                                {
                                    "value": {
                                        "text": "' . t('Yes', [], 'dataFixtures', $this->firstDomainLocale) . '"
                                    },
                                    "count": 3,
                                    "isAbsolute": true
                                }
                            ]
                        }
                    ]
                }
            }
        }
    }
}';

        $this->assertQueryWithExpectedJson($query, $expectedResult);
    }
}

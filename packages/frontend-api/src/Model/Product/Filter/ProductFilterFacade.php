<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Product\Filter;

use Overblog\GraphQLBundle\Definition\Argument;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfigFactory;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;

class ProductFilterFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected Domain $domain;

    /**
     * @var \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterDataMapper
     */
    protected ProductFilterDataMapper $productFilterDataMapper;

    /**
     * @var \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterValidator
     */
    protected ProductFilterValidator $productFilterValidator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfigFactory
     */
    protected ProductFilterConfigFactory $productFilterConfigFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig[]
     */
    protected array $productFilterConfigCache = [];

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterDataMapper $productFilterDataMapper
     * @param \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterValidator $productFilterValidator
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfigFactory $productFilterConfigFactory
     */
    public function __construct(
        Domain $domain,
        ProductFilterDataMapper $productFilterDataMapper,
        ProductFilterValidator $productFilterValidator,
        ProductFilterConfigFactory $productFilterConfigFactory
    ) {
        $this->productFilterDataMapper = $productFilterDataMapper;
        $this->productFilterValidator = $productFilterValidator;
        $this->productFilterConfigFactory = $productFilterConfigFactory;
        $this->domain = $domain;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig
     */
    public function getProductFilterConfigForAll(): ProductFilterConfig
    {
        $cacheKey = 'all';

        if (array_key_exists($cacheKey, $this->productFilterConfigCache)) {
            return $this->productFilterConfigCache[$cacheKey];
        }

        return $this->productFilterConfigCache[$cacheKey] = $this->productFilterConfigFactory->createForAll(
            $this->domain->getId(),
            $this->domain->getLocale()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig
     */
    public function getProductFilterConfigForBrand(Brand $brand): ProductFilterConfig
    {
        $cacheKey = 'brand_' . $brand->getId();

        if (array_key_exists($cacheKey, $this->productFilterConfigCache)) {
            return $this->productFilterConfigCache[$cacheKey];
        }

        return $this->productFilterConfigCache[$cacheKey] = $this->productFilterConfigFactory->createForBrand(
            $this->domain->getId(),
            $this->domain->getLocale(),
            $brand
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig
     */
    public function getProductFilterConfigForCategory(Category $category): ProductFilterConfig
    {
        $cacheKey = 'category_' . $category->getId();

        if (array_key_exists($cacheKey, $this->productFilterConfigCache)) {
            return $this->productFilterConfigCache[$cacheKey];
        }

        return $this->productFilterConfigCache[$cacheKey] = $this->productFilterConfigFactory->createForCategory(
            $this->domain->getId(),
            $this->domain->getLocale(),
            $category
        );
    }

    /**
     * @param \Overblog\GraphQLBundle\Definition\Argument $argument
     * @param callable $productFilterConfigClosure
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData
     */
    public function getValidatedProductFilterData(Argument $argument, callable $productFilterConfigClosure): ProductFilterData
    {
        if ($argument['filter'] === null) {
            return new ProductFilterData();
        }

        $productFilterData = $this->productFilterDataMapper->mapFrontendApiFilterToProductFilterData($argument['filter']);

        $this->productFilterValidator->removeExcessiveFilters($productFilterData, $productFilterConfigClosure());

        return $productFilterData;
    }
}

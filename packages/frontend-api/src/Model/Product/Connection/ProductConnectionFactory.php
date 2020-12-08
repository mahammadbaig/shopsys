<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Product\Connection;

use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptionsFactory;

class ProductConnectionFactory
{
    /**
     * @var \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptionsFactory
     */
    protected ProductFilterOptionsFactory $productFilterOptionsFactory;

    /**
     * @param \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptionsFactory $productFilterOptionsFactory
     */
    public function __construct(ProductFilterOptionsFactory $productFilterOptionsFactory)
    {
        $this->productFilterOptionsFactory = $productFilterOptionsFactory;
    }

    /**
     * @param callable $retrieveProductClosure
     * @param int $countOfProducts
     * @param \Overblog\GraphQLBundle\Definition\Argument $argument
     * @param callable $getProductFilterConfigClosure
     * @return \Shopsys\FrontendApiBundle\Model\Product\Connection\ProductConnection
     */
    protected function createConnection(
        callable $retrieveProductClosure,
        int $countOfProducts,
        Argument $argument,
        callable $getProductFilterConfigClosure
    ): ProductConnection {
        $paginator = new Paginator($retrieveProductClosure);
        $connection = $paginator->auto($argument, $countOfProducts);

        return new ProductConnection(
            $connection->getEdges(),
            $connection->getPageInfo(),
            $connection->getTotalCount(),
            $getProductFilterConfigClosure
        );
    }

    /**
     * @param callable $retrieveProductClosure
     * @param int $countOfProducts
     * @param \Overblog\GraphQLBundle\Definition\Argument $argument
     * @param callable $getProductFilterConfigClosure
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrontendApiBundle\Model\Product\Connection\ProductConnection
     */
    public function createConnectionForAll(
        callable $retrieveProductClosure,
        int $countOfProducts,
        Argument $argument,
        callable $getProductFilterConfigClosure,
        ProductFilterData $productFilterData
    ): ProductConnection {
        $productFilterOptionsClosure = function () use ($getProductFilterConfigClosure, $productFilterData) {
            return $this->productFilterOptionsFactory->createProductFilterOptionsForAll(
                $getProductFilterConfigClosure,
                $productFilterData
            );
        };

        return $this->createConnection(
            $retrieveProductClosure,
            $countOfProducts,
            $argument,
            $productFilterOptionsClosure
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param callable $retrieveProductClosure
     * @param int $countOfProducts
     * @param \Overblog\GraphQLBundle\Definition\Argument $argument
     * @param callable $getProductFilterConfigClosure
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrontendApiBundle\Model\Product\Connection\ProductConnection
     */
    public function createConnectionForBrand(
        Brand $brand,
        callable $retrieveProductClosure,
        int $countOfProducts,
        Argument $argument,
        callable $getProductFilterConfigClosure,
        ProductFilterData $productFilterData
    ): ProductConnection {
        $productFilterOptionsClosure = function () use ($brand, $getProductFilterConfigClosure, $productFilterData) {
            return $this->productFilterOptionsFactory->createProductFilterOptionsForBrand(
                $brand,
                $getProductFilterConfigClosure,
                $productFilterData
            );
        };

        return $this->createConnection(
            $retrieveProductClosure,
            $countOfProducts,
            $argument,
            $productFilterOptionsClosure
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param callable $retrieveProductClosure
     * @param int $countOfProducts
     * @param \Overblog\GraphQLBundle\Definition\Argument $argument
     * @param callable $getProductFilterConfigClosure
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrontendApiBundle\Model\Product\Connection\ProductConnection
     */
    public function createConnectionForCategory(
        Category $category,
        callable $retrieveProductClosure,
        int $countOfProducts,
        Argument $argument,
        callable $getProductFilterConfigClosure,
        ProductFilterData $productFilterData
    ): ProductConnection {
        $productFilterOptionsClosure = function () use ($category, $getProductFilterConfigClosure, $productFilterData) {
            return $this->productFilterOptionsFactory->createProductFilterOptionsForCategory(
                $category,
                $getProductFilterConfigClosure,
                $productFilterData
            );
        };

        return $this->createConnection(
            $retrieveProductClosure,
            $countOfProducts,
            $argument,
            $productFilterOptionsClosure
        );
    }
}

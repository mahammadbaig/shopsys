<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Product\Filter;

use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Module\ModuleFacade;
use Shopsys\FrameworkBundle\Model\Module\ModuleList;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\FrameworkBundle\Model\Product\Flag\Flag;
use Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue;
use Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainElasticFacade;

class ProductFilterOptionsFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Module\ModuleFacade
     */
    protected ModuleFacade $moduleFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainElasticFacade
     */
    protected ProductOnCurrentDomainElasticFacade $productOnCurrentDomainElasticFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Module\ModuleFacade $moduleFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainElasticFacade $productOnCurrentDomainFacade
     */
    public function __construct(
        ModuleFacade $moduleFacade,
        ProductOnCurrentDomainElasticFacade $productOnCurrentDomainFacade
    ) {
        $this->moduleFacade = $moduleFacade;
        $this->productOnCurrentDomainElasticFacade = $productOnCurrentDomainFacade;
    }

    /**
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptions
     */
    protected function createProductFilterOptionsInstance(): ProductFilterOptions
    {
        return new ProductFilterOptions();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag $flag
     * @param int $count
     * @param bool $isAbsolute
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\FlagFilterOption
     */
    protected function createFlagFilterOption(Flag $flag, int $count, bool $isAbsolute): FlagFilterOption
    {
        return new FlagFilterOption($flag, $count, $isAbsolute);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param int $count
     * @param bool $isAbsolute
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\BrandFilterOption
     */
    protected function createBrandFilterOption(Brand $brand, int $count, bool $isAbsolute): BrandFilterOption
    {
        return new BrandFilterOption($brand, $count, $isAbsolute);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\FrontendApiBundle\Model\Product\Filter\ParameterValueFilterOption[] $parameterValueFilterOptions
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\ParameterFilterOption
     */
    protected function createParameterFilterOption(Parameter $parameter, array $parameterValueFilterOptions): ParameterFilterOption
    {
        return new ParameterFilterOption($parameter, $parameterValueFilterOptions);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue $brand
     * @param int $count
     * @param bool $isAbsolute
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\ParameterValueFilterOption
     */
    protected function createParameterValueFilterOption(ParameterValue $brand, int $count, bool $isAbsolute): ParameterValueFilterOption
    {
        return new ParameterValueFilterOption($brand, $count, $isAbsolute);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $productFilterConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData $productFilterCountData
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptions
     */
    protected function createProductFilterOptions(
        ProductFilterConfig $productFilterConfig,
        ProductFilterCountData $productFilterCountData,
        ProductFilterData $productFilterData
    ): ProductFilterOptions {
        $productFilterOptions = $this->createProductFilterOptionsInstance();
        $productFilterOptions->minimalPrice = $productFilterConfig->getPriceRange()->getMinimalPrice();
        $productFilterOptions->maximalPrice = $productFilterConfig->getPriceRange()->getMaximalPrice();

        $productFilterOptions->inStock = $productFilterCountData->countInStock ?? 0;

        $this->fillFlags($productFilterOptions, $productFilterConfig, $productFilterCountData, $productFilterData);

        return $productFilterOptions;
    }

    /**
     * @param callable $productFilterConfigClosure
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptions
     */
    public function createProductFilterOptionsForAll(
        callable $productFilterConfigClosure,
        ProductFilterData $productFilterData
    ): ProductFilterOptions {
        if (!$this->moduleFacade->isEnabled(ModuleList::PRODUCT_FILTER_COUNTS)) {
            return $this->createProductFilterOptionsInstance();
        }

        $productFilterCountData = $this->productOnCurrentDomainElasticFacade->getProductFilterCountDataForAll(
            $productFilterData
        );

        $productFilterOptions = $this->createProductFilterOptions(
            $productFilterConfigClosure(),
            $productFilterCountData,
            $productFilterData
        );
        $this->fillBrands(
            $productFilterOptions,
            $productFilterConfigClosure(),
            $productFilterCountData,
            $productFilterData
        );

        return $productFilterOptions;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param callable $productFilterConfigClosure
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptions
     */
    public function createProductFilterOptionsForCategory(
        Category $category,
        callable $productFilterConfigClosure,
        ProductFilterData $productFilterData
    ): ProductFilterOptions {
        if (!$this->moduleFacade->isEnabled(ModuleList::PRODUCT_FILTER_COUNTS)) {
            return $this->createProductFilterOptionsInstance();
        }

        $productFilterCountData = $this->productOnCurrentDomainElasticFacade->getProductFilterCountDataInCategory(
            $category->getId(),
            $productFilterConfigClosure(),
            $productFilterData
        );

        $productFilterOptions = $this->createProductFilterOptions(
            $productFilterConfigClosure(),
            $productFilterCountData,
            $productFilterData
        );
        $this->fillBrands(
            $productFilterOptions,
            $productFilterConfigClosure(),
            $productFilterCountData,
            $productFilterData
        );
        $this->fillParameters(
            $productFilterOptions,
            $productFilterConfigClosure(),
            $productFilterCountData,
            $productFilterData
        );

        return $productFilterOptions;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param callable $productFilterConfigClosure
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptions
     */
    public function createProductFilterOptionsForBrand(
        Brand $brand,
        callable $productFilterConfigClosure,
        ProductFilterData $productFilterData
    ): ProductFilterOptions {
        if (!$this->moduleFacade->isEnabled(ModuleList::PRODUCT_FILTER_COUNTS)) {
            return $this->createProductFilterOptionsInstance();
        }

        $productFilterCountData = $this->productOnCurrentDomainElasticFacade->getProductFilterCountDataForBrand(
            $brand->getId(),
            $productFilterData
        );

        return $this->createProductFilterOptions(
            $productFilterConfigClosure(),
            $productFilterCountData,
            $productFilterData
        );
    }

    /**
     * @param \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptions $productFilterOptions
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $productFilterConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData $productFilterCountData
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     */
    protected function fillFlags(
        ProductFilterOptions $productFilterOptions,
        ProductFilterConfig $productFilterConfig,
        ProductFilterCountData $productFilterCountData,
        ProductFilterData $productFilterData
    ): void {
        $isAbsolute = count($productFilterData->flags) === 0;

        foreach ($productFilterConfig->getFlagChoices() as $flag) {
            $productFilterOptions->flags[] = $this->createFlagFilterOption(
                $flag,
                $productFilterCountData->countByFlagId[$flag->getId()] ?? 0,
                $isAbsolute
            );
        }
    }

    /**
     * @param \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptions $productFilterOptions
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $productFilterConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData $productFilterCountData
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     */
    protected function fillBrands(
        ProductFilterOptions $productFilterOptions,
        ProductFilterConfig $productFilterConfig,
        ProductFilterCountData $productFilterCountData,
        ProductFilterData $productFilterData
    ): void {
        $isAbsolute = count($productFilterData->brands) === 0;

        foreach ($productFilterConfig->getBrandChoices() as $brand) {
            $productFilterOptions->brands[] = $this->createBrandFilterOption(
                $brand,
                $productFilterCountData->countByBrandId[$brand->getId()] ?? 0,
                $isAbsolute
            );
        }
    }

    /**
     * @param \Shopsys\FrontendApiBundle\Model\Product\Filter\ProductFilterOptions $productFilterOptions
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $productFilterConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData $productFilterCountData
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     */
    protected function fillParameters(
        ProductFilterOptions $productFilterOptions,
        ProductFilterConfig $productFilterConfig,
        ProductFilterCountData $productFilterCountData,
        ProductFilterData $productFilterData
    ): void {
        foreach ($productFilterConfig->getParameterChoices() as $parameterFilterChoice) {
            $isAbsolute = !$this->isParameterFiltered($parameterFilterChoice->getParameter(), $productFilterData);

            $parameter = $parameterFilterChoice->getParameter();
            $parameterValueFilterOptions = [];

            foreach ($parameterFilterChoice->getValues() as $parameterValue) {
                $parameterValueCount = $productFilterCountData->countByParameterIdAndValueId[$parameter->getId()][$parameterValue->getId()];
                $parameterValueFilterOptions[] = $this->createParameterValueFilterOption(
                    $parameterValue,
                    $parameterValueCount,
                    $isAbsolute
                );
            }

            $productFilterOptions->parameters[] = $this->createParameterFilterOption(
                $parameter,
                $parameterValueFilterOptions
            );
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return bool
     */
    protected function isParameterFiltered(Parameter $parameter, ProductFilterData $productFilterData): bool
    {
        foreach ($productFilterData->parameters as $parameterFilterData) {
            if ($parameterFilterData->parameter === $parameter) {
                return true;
            }
        }

        return false;
    }
}
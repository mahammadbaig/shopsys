<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Product\Filter;

use Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade;
use Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterFacade;

class ProductFilterDataMapper
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade
     */
    protected FlagFacade $flagFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade
     */
    protected BrandFacade $brandFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterFacade
     */
    protected ParameterFacade $parameterFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade $flagFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade $brandFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterFacade $parameterFacade
     */
    public function __construct(
        FlagFacade $flagFacade,
        BrandFacade $brandFacade,
        ParameterFacade $parameterFacade
    ) {
        $this->flagFacade = $flagFacade;
        $this->brandFacade = $brandFacade;
        $this->parameterFacade = $parameterFacade;
    }

    /**
     * @param array $frontendApiFilter
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData
     */
    public function mapFrontendApiFilterToProductFilterData(array $frontendApiFilter): ProductFilterData
    {
        $productFilterData = new ProductFilterData();
        $productFilterData->minimalPrice = $frontendApiFilter['minimalPrice'] ?? null;
        $productFilterData->maximalPrice = $frontendApiFilter['maximalPrice'] ?? null;
        $productFilterData->brands = $this->getBrandsByUuids($frontendApiFilter['brands'] ?? []);
        $productFilterData->flags = $this->getFlagsByUuids($frontendApiFilter['flags'] ?? []);
        $productFilterData->parameters = $this->getParametersAndValuesByUuids($frontendApiFilter['parameters'] ?? []);

        return $productFilterData;
    }

    /**
     * @param string[] $brandUuids
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    protected function getBrandsByUuids(array $brandUuids): array
    {
        $brands = [];

        foreach ($brandUuids as $brandUuid) {
            $brands[] = $this->brandFacade->getByUuid($brandUuid);
        }

        return $brands;
    }

    /**
     * @param string[] $flagUuids
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    protected function getFlagsByUuids(array $flagUuids): array
    {
        $flags = [];

        foreach ($flagUuids as $flagUuid) {
            $flags[] = $this->flagFacade->getByUuid($flagUuid);
        }

        return $flags;
    }

    /**
     * @param array $parameterAndValueUuids
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData[]
     */
    protected function getParametersAndValuesByUuids(array $parameterAndValueUuids): array
    {
        $parametersFilterData = [];

        foreach ($parameterAndValueUuids as $parameterAndValueUuid) {
            $parameter = $this->parameterFacade->getByUuid($parameterAndValueUuid['parameter']);

            $parameterValues = [];

            foreach ($parameterAndValueUuid['values'] as $parameterValueUuid) {
                $parameterValues[] = $this->parameterFacade->getParameterValueByUuid($parameterValueUuid);
            }

            $parameterFilterData = new ParameterFilterData();
            $parameterFilterData->parameter = $parameter;
            $parameterFilterData->values = $parameterValues;

            $parametersFilterData[] = $parameterFilterData;
        }

        return $parametersFilterData;
    }
}

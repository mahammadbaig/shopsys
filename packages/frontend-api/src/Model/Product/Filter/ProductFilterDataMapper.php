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
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter[]
     */
    protected array $parametersByUuid = [];

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[]
     */
    protected array $parameterValuesByUuid = [];

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
        $productFilterData->brands = isset($frontendApiFilter['brands']) ? $this->brandFacade->getByUuids($frontendApiFilter['brands']) : [];
        $productFilterData->flags = isset($frontendApiFilter['flags']) ? $this->flagFacade->getByUuids($frontendApiFilter['flags']) : [];
        $productFilterData->parameters = $this->getParametersAndValuesByUuids($frontendApiFilter['parameters'] ?? []);

        return $productFilterData;
    }

    /**
     * @param array $parameterAndValueUuids
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData[]
     */
    protected function getParametersAndValuesByUuids(array $parameterAndValueUuids): array
    {
        $parametersFilterData = [];

        $this->loadParametersAndValuesFromArray($parameterAndValueUuids);

        foreach ($parameterAndValueUuids as $parameterAndValueUuid) {
            $parameter = $this->parametersByUuid[$parameterAndValueUuid['parameter']];

            $parameterValues = [];

            foreach ($parameterAndValueUuid['values'] as $parameterValueUuid) {
                $parameterValues[] = $this->parameterValuesByUuid[$parameterValueUuid];
            }

            $parameterFilterData = new ParameterFilterData();
            $parameterFilterData->parameter = $parameter;
            $parameterFilterData->values = $parameterValues;

            $parametersFilterData[] = $parameterFilterData;
        }

        return $parametersFilterData;
    }

    /**
     * @param array $parameterAndValueUuids
     */
    protected function loadParametersAndValuesFromArray(array $parameterAndValueUuids): void
    {
        $parameterUuids = [];
        $parameterValueUuids = [];

        foreach ($parameterAndValueUuids as $parameterAndValueUuid) {
            $parameterUuids[] = $parameterAndValueUuid['parameter'];

            foreach ($parameterAndValueUuid['values'] as $parameterValueUuid) {
                $parameterValueUuids[] = $parameterValueUuid;
            }
        }

        $this->parametersByUuid = $this->parameterFacade->getParametersByUuids($parameterUuids);
        $this->parameterValuesByUuid = $this->parameterFacade->getParameterValuesByUuids($parameterValueUuids);
    }
}

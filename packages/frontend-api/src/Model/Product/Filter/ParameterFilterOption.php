<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Product\Filter;

use Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter;

class ParameterFilterOption
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter
     */
    public Parameter $parameter;

    /**
     * @var \Shopsys\FrontendApiBundle\Model\Product\Filter\ParameterValueFilterOption[]
     */
    public array $values;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\FrontendApiBundle\Model\Product\Filter\ParameterValueFilterOption[] $values
     */
    public function __construct(Parameter $parameter, array $values)
    {
        $this->parameter = $parameter;
        $this->values = $values;
    }
}

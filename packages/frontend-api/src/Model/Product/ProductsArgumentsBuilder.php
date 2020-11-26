<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Product;

use Shopsys\FrontendApiBundle\Component\Arguments\PaginatorArgumentsBuilder;

class ProductsArgumentsBuilder extends PaginatorArgumentsBuilder
{
    /**
     * @param array $config
     * @return array
     */
    public function toMappingDefinition(array $config): array
    {
        $mappingDefinition = parent::toMappingDefinition($config);
        $mappingDefinition['filter'] = ['type' => 'ProductFilter'];

        return $mappingDefinition;
    }
}

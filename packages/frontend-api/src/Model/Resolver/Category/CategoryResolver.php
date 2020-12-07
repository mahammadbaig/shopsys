<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Resolver\Category;

use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Overblog\GraphQLBundle\Error\UserError;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Category\Exception\CategoryNotFoundException;

class CategoryResolver implements ResolverInterface, AliasedInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    protected $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain|null
     */
    protected ?Domain $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain|null $domain
     */
    public function __construct(
        CategoryFacade $categoryFacade,
        ?Domain $domain = null
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->domain = $domain;
    }

    /**
     * @param string $uuid
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function resolver(string $uuid): Category
    {
        try {
            return $this->categoryFacade->getVisibleOnDomainByUuid($this->domain->getId(), $uuid);
        } catch (CategoryNotFoundException $categoryNotFoundException) {
            throw new UserError($categoryNotFoundException->getMessage());
        }
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array
    {
        return [
            'resolver' => 'category',
        ];
    }
}

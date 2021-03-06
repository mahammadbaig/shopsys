<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlNotFoundException;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\Exception\MethodGenerateIsNotSupportedException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouteCompiler;

class FriendlyUrlGenerator extends BaseUrlGenerator
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
     */
    protected $friendlyUrlRepository;

    /**
     * @param \Symfony\Component\Routing\RequestContext $context
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository $friendlyUrlRepository
     */
    public function __construct(
        RequestContext $context,
        FriendlyUrlRepository $friendlyUrlRepository
    ) {
        parent::__construct(new RouteCollection(), $context, null);

        $this->friendlyUrlRepository = $friendlyUrlRepository;
    }

    /**
     * @param \Symfony\Component\Routing\RouteCollection $routeCollection
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param string $routeName
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    public function generateFromRouteCollection(
        RouteCollection $routeCollection,
        DomainConfig $domainConfig,
        $routeName,
        array $parameters = [],
        $referenceType = self::ABSOLUTE_PATH
    ) {
        $route = $routeCollection->get($routeName);
        if ($route === null) {
            $message = 'Unable to generate a URL for the named route "' . $routeName . '" as such route does not exist.';
            throw new RouteNotFoundException($message);
        }
        if (!array_key_exists('id', $parameters)) {
            $message = 'Missing mandatory parameter "id" for route ' . $routeName . '.';
            throw new MissingMandatoryParametersException($message);
        }
        $entityId = $parameters['id'];
        unset($parameters['id']);

        try {
            $friendlyUrl = $this->friendlyUrlRepository->getMainFriendlyUrl(
                $domainConfig->getId(),
                $routeName,
                $entityId
            );
        } catch (FriendlyUrlNotFoundException $e) {
            $message = 'Unable to generate a URL for the named route "' . $routeName . '" as such route does not exist.';
            throw new RouteNotFoundException($message, 0, $e);
        }

        return $this->getGeneratedUrl($routeName, $route, $friendlyUrl, $parameters, $referenceType);
    }

    /**
     * @param string $routeName
     * @param \Symfony\Component\Routing\Route $route
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    public function getGeneratedUrl($routeName, Route $route, FriendlyUrl $friendlyUrl, array $parameters, $referenceType)
    {
        $compiledRoute = RouteCompiler::compile($route);

        $tokens = [
            [
                0 => 'text',
                1 => '/' . $friendlyUrl->getSlug(),
            ],
        ];

        return $this->doGenerate(
            $compiledRoute->getVariables(),
            $route->getDefaults(),
            $route->getRequirements(),
            $tokens,
            $parameters,
            $routeName,
            $referenceType,
            $compiledRoute->getHostTokens(),
            $route->getSchemes()
        );
    }

    /**
     * Not supported method
     *
     * @param mixed $routeName
     * @param mixed $parameters
     * @param mixed $referenceType
     */
    public function generate($routeName, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        throw new MethodGenerateIsNotSupportedException();
    }
}

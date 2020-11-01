<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IconExtension extends AbstractExtension
{
    /**
     * @var \Twig\Environment
     */
    private $twigEnvironment;

    /**
     * @param \Twig\Environment $twigEnvironment
     */
    public function __construct(
        Environment $twigEnvironment
    ) {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'renderIcon'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $iconName
     * @param string $iconType
     * @param array $attributes
     * @return string
     */
    public function renderIcon(string $iconName = '', string $iconType = '', $attributes = array()): string
    {
        return $this->twigEnvironment->render(
            'Front/Inline/Icon/icon.html.twig',
            [
                'name' => $iconName,
                'type' => $iconType,
                'attr' => $attributes,
            ]
        );
    }
}

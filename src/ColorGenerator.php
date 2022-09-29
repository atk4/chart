<?php

declare(strict_types=1);

namespace Atk4\Chart;

/**
 * @todo Replace this with something which would generate infinite colors.
 * Like https://medium.com/code-nebula/automatically-generate-chart-colors-with-chart-js-d3s-color-scales-f62e282b2b41 or something else
 */
class ColorGenerator
{
    /**
     * We will use these colors in charts.
     * First can be used as background color and 2nd as border color.
     *
     * @var array<int, array<int, string>>
     */
    protected $colors = [
        ['rgba(255, 99, 132, 0.2)', 'rgba(255, 99, 132, 1)'],
        ['rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)'],
        ['rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)'],
        ['rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)'],
        ['rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)'],
        ['rgba(255, 159, 64, 0.2)', 'rgba(255, 159, 64, 1)'],
        ['rgba(20, 20, 20, 0.2)', 'rgba(20, 20, 20, 1)'],
    ];

    /** @var int */
    private $currentColorIndex = -1;

    /**
     * Return color by index.
     *
     * @return array<int, string>
     */
    public function getColorPairByIndex(int $i): array
    {
        return $this->colors[$i % count($this->colors)];
    }

    /**
     * Return next color.
     *
     * @return array<int, string>
     */
    public function getNextColorPair(): array
    {
        return $this->getColorPairByIndex(++$this->currentColorIndex);
    }

    /**
     * Return all possible colors.
     *
     * @return array<int, array<int, string>>
     */
    public function getAllColorPairs(): array
    {
        return $this->colors;
    }

    /**
     * Set all possible colors.
     *
     * @param array<int, array<int, string>> $colors
     *
     * @return $this
     */
    public function setAllColorPairs(array $colors)
    {
        $this->colors = $colors;

        return $this;
    }
}

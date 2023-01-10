<?php

declare(strict_types=1);

namespace Atk4\Chart;

/**
 * @TODO Replace this with something which would generate infinite colors.
 * Like https://medium.com/code-nebula/automatically-generate-chart-colors-with-chart-js-d3s-color-scales-f62e282b2b41 or something else
 */
class ColorGenerator
{
    /**
     * We will use these colors in charts.
     * The 1st color is for background and the 2nd is for border.
     *
     * @var array<int, array<int, string>>
     */
    private $colors = [
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
     * @return array<int, string>
     */
    public function getColorPairByIndex(int $i): array
    {
        return $this->colors[$i % count($this->colors)];
    }

    /**
     * @return array<int, string>
     */
    public function getNextColorPair(): array
    {
        return $this->getColorPairByIndex(++$this->currentColorIndex);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getAllColorPairs(): array
    {
        return $this->colors;
    }

    /**
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

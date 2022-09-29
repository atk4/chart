<?php

declare(strict_types=1);

namespace Atk4\Chart;

trait StackedTrait
{
    /**
     * Group columns in stacks.
     *
     * @param array<mixed, array<string>> $stacks Stack name => array of column names in stack
     */
    public function setStacks(array $stacks = []): void
    {
        if ($stacks !== []) {
            $this->setOptions(['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]]]);

            $options = [];
            foreach ($stacks as $stack => $columns) {
                foreach ($columns as $column) {
                    $options[$column]['stack'] = $stack;
                }
            }
            $this->setColumnOptions($options);
        }
    }
}

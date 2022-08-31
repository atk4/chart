<?php

declare(strict_types=1);

namespace Atk4\Chart;

/**
 * Adds stacked chart functionality.
 */
trait StackedTrait
{
    /** @var array<mixed, array{string}> Array of stacks config - stack name => array of columns in stack */
    protected $stacks = [];

    /**
     * Group columns in stacks.
     *
     * @param array<mixed, array{string}> $stacks Stack name => array of column names in stack
     */
    public function setStacks(array $stacks = []): void
    {
        $this->stacks = $stacks;

        if ($stacks !== []) {
            $this->setOptions(['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]]]);

            $options = [];
            foreach ($this->stacks as $stack => $columns) {
                foreach ($columns as $column) {
                    $options[$column]['stack'] = $stack;
                }
            }
            $this->setColumnOptions($options);
        }
    }
}

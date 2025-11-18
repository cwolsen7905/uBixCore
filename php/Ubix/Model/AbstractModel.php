<?php

declare(strict_types=1);

namespace Ubix\Model;

use ReflectionObject;

/**
 * Abstract base model class to track changed properties.
 *
 * @see \Ubix\Tests\Model\AbstractModelTest PHPUnit test case
 */
abstract class AbstractModel
{
    /**
     * Array to keep track of changed properties.
     *
     * @var array<string> List of property names that have changed.
     */
    protected array $changedProperties = [];

    /**
     * Check if any properties have changed.
     *
     * @return bool True if there are changed properties, false otherwise.
     */
    public function hasChanges(): bool
    {
        return !empty($this->changedProperties);
    }

    /**
     * Check if a specific property has changed.
     *
     * @param string $property The property name to check.
     *
     * @return bool True if the property has changed, false otherwise.
     */
    public function hasChanged(string $property): bool
    {
        return in_array($property, $this->changedProperties, true);
    }

    /**
     * Clear the list of changed properties.
     *
     * This method resets the tracking of changed properties.
     *
     * @return void
     */
    public function clearChanges(): void
    {
        $this->changedProperties = [];
    }

    /**
     * Get the value of changedProperties
     *
     * @return array<string> The value of changedProperties
     */
    public function getChangedProperties(): array
    {
        return $this->changedProperties;
    }

    /**
     * Mark a property as changed.
     *
     * @param string $property The property name to mark as changed.
     *
     * @return void
     */
    protected function markChanged(string $property): void
    {
        $this->changedProperties[] = $property;
    }

    /**
     * Mark all properties as changed.
     *
     * @return void
     */
    protected function markAllChanged(): void
    {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $this->changedProperties[] = $property->getName();
        }
    }
}

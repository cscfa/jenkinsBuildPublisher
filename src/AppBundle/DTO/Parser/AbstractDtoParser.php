<?php

namespace AppBundle\DTO\Parser;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractDtoParser
{
    abstract protected function getDtoClass();

    protected function getPublicProperties()
    {
        $reflex = $this->getReflection();

        $properties = array();

        foreach ($reflex->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property instanceof \ReflectionProperty) {
                array_push($properties, $property->getName());
            }
        }

        return $properties;
    }

    protected function hydrateDto($dto, $entity, array $skip = array())
    {
        return $this->hydrate($entity, $dto, $skip);
    }

    protected function hydrateEntity($dto, $entity, array $skip = array())
    {
        return $this->hydrate($dto, $entity, $skip);
    }

    private function hydrate($from, $to, $skip)
    {
        $accessor = $this->getAccessor();

        foreach ($this->getPublicProperties() as $property) {

            if (in_array($property, $skip)) {
                continue;
            }

            $accessor->setValue(
                $to,
                $property,
                $accessor->getValue(
                    $from,
                    $property
                )
            );
        }

        return $to;
    }

    private function getReflection()
    {
        return new \ReflectionClass($this->getDtoClass());
    }

    /**
     * @return PropertyAccessor
     */
    private function getAccessor()
    {
        return PropertyAccess::createPropertyAccessor();
    }
}

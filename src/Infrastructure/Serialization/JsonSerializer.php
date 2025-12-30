<?php 

namespace GioPHP\Infrastructure\Serialization;

class JsonSerializer 
{
	#[\Deprecated(reason: "version 2 is better")]
	public function serialize (object $classObj): string
    {
        $classProperties = get_object_vars($classObj);
        
        return json_encode($classProperties);
    }
    
    // NOTE: Use version 2 instead as "get object vars"
    // ignores variables that are left uninitialized
    public function serializev2 (object $classObj): string
    {
        $classProperties = $this->getProperties($classObj);
        $parsedProperties = $this->parsePropertiesForKvpValues($classProperties);
        
        return json_encode($parsedProperties);
    }
    
    private function parsePropertiesForKvpValues (array $properties): array
    {
        $propertyItems = [];
        
        foreach($properties as $item):
            
			// TODO: Refactor due to unnecessary redundancy
            if(!$item->isNullable && is_null($item->value))
            {
                throw new \Exception("Null value on non-nullable property: {$item->name}");
            }
            
            $propertyItems[$item->name] = $item->value;
            
        endforeach;
        
        return $propertyItems;
    }
    
    private function getProperties (object $classObj): array
    {
        $reflect = new \ReflectionClass($classObj);
		$properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        $finalProperties = [];
        
        foreach($properties as $item):
            
            $propertyObject = [
                'name' => '',
                'type' => '',
                'isNullable' => false,
                'value' => '',
                'isClass' => false
            ];
            
            $name = $item->getName();
            
            $propertyObject['name'] = $name;
            $propertyObject['type'] = $item->getType()->getName();
            $propertyObject['isNullable'] = $item->getType()->allowsNull();
            
            // Checks if the property was properly initialized first
            if(!$item->isInitialized($classObj))
            {
                if($propertyObject['isNullable'])
                {
                    $classObj->{$name} = NULL;
                }
                else
                {
					throw new \Exception("Uninitialized non-nullable property: {$name}");
                }
            }
            
            $propertyObject['value'] = $classObj->{$item->getName()};
            
            if(class_exists($propertyObject['type']))
            {
                $propertyObject['isClass'] = true;
				$this->serializev2($item);
            }
            
            $finalProperties[] = (object) $propertyObject;
            
        endforeach;
        
        return $finalProperties;
    }
}

?>
<?php 

namespace GioPHP\Infrastructure\Serialization;

class JsonSerializer
{
	public function serialize (object $genericObject): string
	{
		$kvp = $this->getKeyValuePairs($genericObject);
		
		return json_encode($kvp, JSON_THROW_ON_ERROR);
	}
	
	private function getKeyValuePairs (object $genericObject, array &$objectIds = []): array
	{
		$reflect = new \ReflectionClass($genericObject);
		
		$properties = $reflect->getProperties(
			\ReflectionProperty::IS_PUBLIC
			| \ReflectionProperty::IS_PRIVATE
			| \ReflectionProperty::IS_PROTECTED
		);
		
		$propertyKeyValues = [];
		
		foreach($properties as $property)
		{
			$property->setAccessible(true);
			
			$name = $property->getName();
			$type = $property->getType();
			$value = NULL;
			
			if(is_null($type))
			{
				throw new \Exception("Non-typed property: {$name}");
			}
			
			$allowsNull = $property->getType()->allowsNull();
			
			// Checks if the property is initialized
			if(!$property->isInitialized($genericObject))
			{
				if(!$allowsNull)
				{
					throw new \Exception("Null value on non-nullable property: {$name}");
				}
				
				$propertyKeyValues[$name] = $value;
				continue;
			}
			
			$value = $property->getValue($genericObject);
			
			// Checks if value is an object
			// stores its object id if it is one
			// in order to avoid infinite recursion
			if(is_object($value))
			{
				$objId = spl_object_id($value);
				
				if(isset($objectIds[$objId]))
				{
					$propertyKeyValues[$name] = "__recursive__";
					continue;
				}
				
				$objectIds[$objId] = true;
			}
			
			// Checks if the type is actually another class
			// if it is then also serializes it 
			if(class_exists($type->getName()))
			{
				$value = $this->getKeyValuePairs($value, $objectIds);
			}
			
			$propertyKeyValues[$name] = $value;
		}
		
		return $propertyKeyValues;
	}
}

?>
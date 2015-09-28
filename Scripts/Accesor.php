<?php
abstract class GetterSetter
{
	// Check for property accessibility
	protected function Accessible($name)
	{
		// Check for available accessors
		if ((method_exists($this, "set_$name")) ||
				(method_exists($this, "get_$name")))
					return true;

				// Inform dev why access to a field was denied
				throw new Exception((property_exists($this, $name) == false)
						? "Property $name does not exist"
						: "Property $name not accessible");
	}

	public function __get($name)
	{
		if ($this->Accessible($name))
		{
			// call get accessor
			if (method_exists($this, "get_$name"))
				return $this->{"get_$name"}();
			else
				throw new Exception("Writeonly Property $name");
		}
	}

	public function __set($name, $value)
	{
		if ($this->Accessible($name))
		{
			// call set accessor (if available)
			if (method_exists($this, "set_$name"))
				$this->{"set_$name"}($value);
			else
				throw new Exception("Readonly Property $name");
		}
	}
}

?>
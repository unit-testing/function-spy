<?php namespace UnitTesting\FunctionSpy;

trait SpyTrait {
	protected $spy;

	/**
	* init the spy
	*/
	protected function initSpy()
	{
		$this->spy = Spy::instance();
	}

	/**
	* flush the spy
	*/
	protected function flushSpy()
	{
		$this->spy->flushRecorders();
	}

	protected function getFunctionUnderTest($method)
	{
		return $this->spy->getRecorder($method);
	}

	/**
	* assert that a method not called at all
	* @param  string $name
	*/
	public function assertFunctionNotCalled($name)
	{
		if (count(func_get_args()) !== 1)
		{
			throw new \InvalidArgumentException('@assertFunctionNotCalled() expects only a method parameter. Did you mean to use @assertFunctionNotCalledWith()?');
		}

		if ($method = $this->getFunctionUnderTest($name) and $method->wasCalled())
		{
			$message = 'Expected [' . $name . '] not to be called, but it was called.';
			$this->fail($message);
		}
	}

	/**
	* assert that a method not called at all
	* @param  string $name
	*/
	public function assertFunctionNotCalledWith($name, array $args)
	{
		if ($method = $this->getFunctionUnderTest($name) and $method->wasCalledWith($args))
		{
			$message = 'Expected [' . $name . '] not to be called with [' . join($args, ', ') . '].';
			$this->fail($message);
		}
	}

	/**
	* assert that a method was not called with specific set of arguments
	* @param  string $name
	* @param  array $args
	*/
	public function assertFunctionCalledWith($name, array $args)
	{
		if (!$method = $this->getFunctionUnderTest($name) or !$method->wasCalledWith($args))
		{
			$message = 'Expected [' . $name . '] to be called with [' . join($args, ', ') . '].';
			$this->fail($message);
		}
	}

	/**
	* assert that a method last called with specific arguments
	* @param  string $name
	* @param  array $args
	*/
	public function assertFunctionLastCalledWith($name, array $args)
	{
		$message = null;
		if (!$method = $this->getFunctionUnderTest($name))
		{
			$message = 'Expected [' . $name . '] to be called, but it was never called.';
		}
		elseif (!$method->wasLastCalledWith($args))
		{
			$message = 'Expected [' . $name . '] last called with [' . join($args, ', ') . '].';
		}
		if ($message)
		{
			$this->fail($message);
		}
	}
}

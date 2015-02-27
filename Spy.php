<?php namespace UnitTesting\FunctionSpy;

class Spy {
	protected static $instance;

	public static function instance()
	{
		if (!self::$instance)
		{
			self::$instance = new Registry();
		}
		return self::$instance;
	}

	/**
	* forward calls to the instance
	* @param  string $method
	* @param  array $args
	* @return mixed
	*/
	public static function __callStatic($method, array $args)
	{
		$instance = self::instance();
		if (!method_exists($instance, $method) and !$args)
		{
			/*
			we're trying to log a method call here, if the args aren't passed then
			try to get them from debug_backtrace so the developer can easily call
			Spy::method() instead of call_user_func_array('Spy::method', func_get_args())
			from within the functions they're spying on. we actually have to go back 3 levels:
				#1 is this function
				#2 is the overloaded method call
				#3 is where the call to the overloaded method actually took place and has the method params
			*/
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
			if (isset($backtrace[2]) and isset($backtrace[2]['args']))
			{
				$args = $backtrace[2]['args'];
			}
		}

		return call_user_func_array(array($instance, $method), $args);
	}

}

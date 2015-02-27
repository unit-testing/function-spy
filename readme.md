# Function Spy #

Sometimes you want to unit test a global or native PHP method, especially when working with a package that has a bunch of globally defined functions (like Wordpress). Other packages/references which address this testing problem:

* https://github.com/lstrojny/phpunit-function-mocker
* http://www.schmengler-se.de/en/2011/03/php-mocking-built-in-functions-like-time-in-unit-tests/
* http://marcelog.github.io/articles/php_mock_global_functions_for_unit_tests_with_phpunit.html

## Installation ##

* require the module in your `composer.json` like this: `"require-dev": { "unit-testing/function-spy": "dev-master" }`
* run `composer update`

### Spy ###

The `Spy` class is a static container that fires up an instance of `Registry`. It has one defined static method `instance` to instantiate or to get the bound instance of `Registry`. All other static calls are forwarded onto the `Registry` instance.

### Registry ###

`Registry` is responsible for logging all method calls and their respective arguments. It also has some functions to analyze calls.

* `getAllSpiedMethods` returns an array with all the methods that have been spied upon. Each key in the array is a method name and has an instance of `Method` as its value
* `flushSpiedMethods` explicitly resets empties all the calls
* `getSpiedMethod($name)` gets the `Method` instance for the matching function call or null if no calls to that function were made.
* `spyMethodCall($name, array $args)` creates an instance of `Method` for the method (if not already created) to log all calls to the method.
* `setMethodResult($name, $result)` creates an instance of `Method` for the method (if not already created) and sets return values to that method

### Method ###
* `getCalls` get all the calls and their respective argument lists
* `wasCalled` checks if the method was called or not
* `wasCalledWith` checks to see if the method was called with a specific list of arguments
* `wasLastCalledWith` checks to see if the method's last call matches a specific list of argumetns

### SpyTrait ##
The `SpyTrait` is a simple trait that can be `use`ed by any of your PHPUnit test cases to automatically set up and flush the `Registry`. With a couple of lines, it will instantiate `Registry` and set `$spy` so you can access it from within your tests. It also extends your test case with a few assertion methods to help check that global function was called.

* `protected initSpy`: call this from within your `setUp()` method to initialize the spy and set the `$spy` member
* `protected flushSpy`: call this from within your `tearDown()` method to clear all calls to empty all calls to the spy between each test.
* `assertFunctionNotCalled`: ensure that a global function was NOT called
* `assertFunctionCalledWith`: ensure that a global function was called with certain parameters
* `assertFunctionLastCalledWith:` ensure that a global function was most recently called with certain parameters

## Annotated example ##

Let's say there is a method `doSomethting` that you've inherited and have to work with. You want to ensure that it is called when it should be with the parameters that it should be called with. Your class `MyNamespace\MyClass` has a method `doFoo` which conditionally calls the `doSomething` function and returns its result.

### Subject Under Test ###
```
<?php namespace MyNamespace;
class MyClass {
	public function doFoo($param1, $param2)
	{
		if ($param1 == 'bar')
		{
			return doSomething($param1, $params);
		}
	}
}

```

### Test Case ###
```
<?php namespace MyNamespace;
class MyTest extends \PHPUnit_Framework_TestCase {
	use \UnitTesting\FunctionSpy\SpyTrait;

	protected function setUp()
	{
		// init the spy
		$this->initSpy();
	}
	protected function tearDown()
	{
		// flush the spy so we can reset the calls after every test
		$this->flushSpy();
	}

	function test_doFoo_WithBar_CallsDoSomethingWithBar()
	{
		$myClass = new MyClass();

		$myClass->doFoo('bar', 'baz');

		// we've ensured that the doSomething() method has been called with bar and baz
		$this->assertFunctionLastCalledWith('doSomething', array('bar', 'baz'));
	}

	function test_doFoo_WithBar_ReturnsResultOfDoSomething()
	{
		Spy::setMethodResult('doSomething', 'doSomething result');
		$myClass = new MyClass();

		$result = $myClass->doFoo('bar', 'baz');

		// we ensure that the doFoo() method returns the result of doSomething();
		$this->assertEquals('doSomething result', $result);
	}

	function doFoo_WithBaz_NeverCallsDoSomething()
	{
		$myClass = new MyClass();

		$myClass->doFoo('some param which does not call doSomething');

		// ensure that doSomething() is never called
		$this->assertFunctionNotCalled('doSomething');
	}

}

// this is needed because we're overriding the globally defined doSomething within our namespace.
function doSomething()
{
	// the Spy static class allows you to simply call any method name and it will log the call with that name.
	// if you don't explicity pass parameters, it will attempt to guess the passed parameters to this function
	// using debug_backtrace()
	return Spy::doSomething();
}

```


<?php namespace Illuminate\Foundation\Console;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Console\Command;
use Illuminate\Routing\RouteCollection;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RoutesCommand extends Command {

    	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'routes';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all registered routes';

	/**
	 * The router instance.
	 *
	 * @var \Illuminate\Routing\Router
	 */
	protected $router;

	/**
	 * An array of all the registered routes.
	 *
	 * @var \Illuminate\Routing\RouteCollection
	 */
	protected $routes;

	/**
	 * The table helper set.
	 *
	 * @var \Symfony\Component\Console\Helper\TableHelper
	 */
	protected $table;

	/**
	 * The table headeers for the command.
	 *
	 * @var array
	 */
	protected $headers = array(
		'Domain', 'URI', 'Name', 'Action', 'Before Filters', 'After Filters'
	);

	/**
	 * Create a new route command instance.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function __construct(Router $router)
	{
		parent::__construct();

		$this->router = $router;
		$this->routes = $router->getRoutes();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->table = $this->getHelperSet()->get('table');

		if (count($this->routes) == 0)
		{
			return $this->error("Your application doesn't have any routes.");
		}

		$this->displayRoutes($this->getRoutes());
	}

	/**
	 * Compile the routes into a displayable format.
	 *
	 * @return array
	 */
	protected function getRoutes()
	{
		$results = array();

		foreach($this->routes as $route)
		{
			$results[] = $this->getRouteInformation($route);
		}

		return $results;
	}

	/**
	 * Get the route information for a given route.
	 *
	 * @param  string  $name
	 * @param  \Illuminate\Routing\Route  $route
	 * @return array
	 */
	protected function getRouteInformation(Route $route)
	{
		$uri = head($route->methods()).' '.$route->uri();

		return array(
			'host'   => $route->domain(),
			'uri'    => $uri,
			'name'   => $route->getName(),
			'action' => $route->getActionName(),
			'before' => $this->getBeforeFilters($route),
			'after'  => $this->getAfterFilters($route));
	}

	/**
	 * Display the route information on the console.
	 *
	 * @param  array  $routes
	 * @return void
	 */
	protected function displayRoutes(array $routes)
	{
		$this->table->setHeaders($this->headers)->setRows($routes);

		$this->table->render($this->getOutput());
	}

	/**
	 * Get before filters
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return string
	 */
	protected function getBeforeFilters($route)
	{
		$before = array_keys($route->beforeFilters());

		$before = array_unique(array_merge($before, $this->getPatternFilters($route)));

		return implode(', ', $before);
	}

	/**
	 * Get all of the pattern filters matching the route.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return array
	 */
	protected function getPatternFilters($route)
	{
		$patterns = array();

		foreach ($route->methods() as $method)
		{
			// For each method supported by the route we will need to gather up the patterned
			// filters for that method. We will then merge these in with the other filters
			// we have already gathered up then return them back out to these consumers.
			$inner = $this->getMethodPatterns($route->uri(), $method);

			$patterns = array_merge($patterns, $inner);
		}

		return $patterns;
	}

	/**
	 * Get the pattern filters for a given URI and method.
	 *
	 * @param  string  $uri
	 * @param  string  $method
	 * @return array
	 */
	protected function getMethodPatterns($uri, $method)
	{
		return $this->router->findPatternFilters(Request::create($uri, $method));
	}

	/**
	 * Get after filters
	 *
	 * @param  Route  $route
	 * @return string
	 */
	protected function getAfterFilters($route)
	{
		return implode(', ', array_keys($route->afterFilters()));
	}

}

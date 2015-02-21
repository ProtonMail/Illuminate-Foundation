<?php namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ListenerMakeCommand extends GeneratorCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:listener';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new event listener class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Listener';

	/**
	 * Build the class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function buildClass($name)
	{
		$stub = parent::buildClass($name);

		$event = $this->option('event');

		if ( ! starts_with($event, $this->getAppNamespace()))
		{
			$event = $this->getAppNamespace().'Events\\'.$event;
		}

		$stub = str_replace(
			'{{event}}', class_basename($event), $stub
		);

		$stub = str_replace(
			'{{fullEvent}}', $event, $stub
		);

		return $stub;
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		if ($this->option('queued'))
		{
			return __DIR__.'/stubs/listener-queued.stub';
		}
		else
		{
			return __DIR__.'/stubs/listener.stub';
		}
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace.'\Listeners';
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('event', null, InputOption::VALUE_REQUIRED, 'The event class the being listened for.'),

			array('queued', null, InputOption::VALUE_NONE, 'Indicates the event listener should be queued.'),
		);
	}

}

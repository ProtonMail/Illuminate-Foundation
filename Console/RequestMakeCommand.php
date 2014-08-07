<?php namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

class RequestMakeCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'request:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new FormRequest class.';

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Create a new request creator command instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		parent::__construct();

		$this->files = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->files->put(
			$this->getPath($this->argument('name')),
			$this->buildRequestClass($this->argument('name'))
		);

		$this->info('Done!');
	}

	/**
	 * Build the request class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function buildRequestClass($name)
	{
		$stub = $this->files->get(__DIR__.'/stubs/request.stub');

		return str_replace('{{class}}', $name, $stub);
	}

	/**
	 * Get the request class path.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function getPath($name)
	{
		return $this->laravel['path'].'/src/Requests/'.$name.'.php';
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'The name of the request class'),
		);
	}

}
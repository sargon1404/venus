<?php
/**
* The Debug Class
* @package Venus
*/

namespace Venus;

/**
* The Debug Class
* Contains debug functionality and outputs debug info
*/
class Debug extends \Mars\Debug
{
	/**
	* @see \Mars\Debug::output()
	* {@inheritDoc}
	*/
	public function output()
	{
		echo '<div id="debug-info">';

		$execution_time = $this->app->timer->getExecutionTime();

		$this->outputInfo($execution_time);
		$this->outputExecutionTime($execution_time);

		$this->outputDbQueries($execution_time);

		$this->outputPlugins($execution_time);
		$this->outputWidgets($execution_time);

		$this->outputLoadedTemplates();

		$this->outputOpcacheInfo();
		
		$this->outputPreloadInfo();

		echo '</div>';
	}

	/**
	* @see \Mars\Debug::outputExecutionTime()
	* {@inheritDoc}
	*/
	protected function outputExecutionTime(float $execution_time)
	{
		$db_time = $this->app->db->queries_time;
		$document_time = 0;
		$plugins_time = $this->getPluginsExecTime();
		$widgets_time = 0;

		if (isset($this->app->document->exec_time)) {
			$document_time = $this->app->document->exec_time;
		}
		if (isset($this->app->widgets)) {
			$widgets_time = $this->getWidgetsExecTime();
		}

		echo '<table class="grid debug-grid" style="width:auto;">';
		echo '<tr><th colspan="3">Execution Time</th></tr>';
		echo '<tr><td><strong>Execution Time</strong></td><td>' . $execution_time . 's</td><td></td></tr>';
		echo "<tr><td><strong>DB Queries</strong></td><td>{$db_time}s</td><td>" . $this->app->format->percentage($db_time, $execution_time) . '%</td></tr>';
		echo "<tr><td><strong>Document</strong></td><td>{$document_time}s</td><td>" . $this->app->format->percentage($document_time, $execution_time) . '%</td></tr>';
		echo "<tr><td><strong>Plugins</strong></td><td>{$plugins_time}s</td><td>" . $this->app->format->percentage($plugins_time, $execution_time) . '%</td></tr>';
		echo "<tr><td><strong>Widgets</strong></td><td>{$widgets_time}s</td><td>" . $this->app->format->percentage($widgets_time, $execution_time) . '%</td></tr>';
		echo "<tr><td><strong>Generate Output</strong></td><td>{$this->info['output_content_time']}s</td><td>" . $this->app->format->percentage($this->info['output_content_time'], $execution_time) . '%</td></tr>';
		echo '</table><br><br>';
	}

	/**
	* Outputs widgets debug info
	* @param float $execution_time The total execution time
	*/
	protected function outputWidgets(float $execution_time)
	{
		if (empty($this->app->widgets->widgets)) {
			return;
		}

		echo '<table class="grid debug-grid debug-grid-widgets" style="width:auto;">';
		echo '<tr><th colspan="3">Widgets</th></tr>';

		foreach ($this->app->widgets->widgets as $widget) {
			$widget_execution_time = $this->app->widgets->exec_time[$widget->wid];

			echo "<tr><td>" . App::e($widget->title) . "</td><td>" . floatval($widget_execution_time) . "</td><td>" . $this->app->format->percentage($widget_execution_time, $execution_time) . '%</td></tr>';
		}

		echo '</table><br><br>';
	}

	/**
	* Computes the exec time for widgets
	* @return int The execution time
	*/
	protected function getWidgetsExecTime() : float
	{
		$time = 0;
		if (!isset($this->app->widgets)) {
			return $time;
		}
		if (!$this->app->widgets->exec_time) {
			return $time;
		}

		return array_sum($this->app->widgets->exec_time);
	}
}

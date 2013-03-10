<?php
/**
 * Piwik tracking component for CakePHP.
 *
 * @author Òscar Casajuana
 * @version 0.1
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
/*
 * Copyright 2013 Òscar Casajuana <elboletaire at underave dot net>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *  http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
class PiwikComponent extends Object
{
	private $piwik_helper;

/**
 * Fills vars from Controller instance
 * 
 * @access private
 * @param Controller $Controller
 * 
 * @return void
 */
	private function fillVars(&$Controller)
	{
		if (empty($this->title) && !empty($Controller->viewVars['title_for_layout']))
		{
			$this->piwik_helper->title = $Controller->viewVars['title_for_layout'];
		}

		if (empty($this->url) && !empty($Controller->here))
		{
			$this->piwik_helper->url = Router::url(str_replace($this->base, '', $this->here), true);
		}
	}
/**
 * Initializes piwik helper
 * Called before Controller::beforeFilter()
 *
 * @access public
 * @param Controller $Controller
 * 
 * @return void
 */
	public function initialize(&$Controller, $params = array())
	{
		App::import('Helper', 'Piwik.Piwik');
		$this->piwik_helper = new PiwikHelper();
		$this->piwik_helper->initPiwik();
	}
/**
 * Tries to fill vars and executes track method if autotrack is enabled
 * Called after Controller::render()
 * 
 * @access public
 * @param Controller $Controller
 * 
 * @return void
 */
	public function shutdown(&$Controller)
	{
		$this->fillVars($Controller);

		if (Configure::read('Piwik.autotrack'))
		{
			$this->track();
		}
	}
/**
 * Tries to fill vars from Controller instance
 * Called after Controller::beforeFilter()
 *
 * @access public
 * @param Controller $Controller
 * 
 * @return void
 */
	public function startup(&$Controller)
	{
		$this->fillVars($Controller);
	}
/**
 * Magic method for calling piwik helper methods
 *
 * @access public
 * @param string $name
 * @param array $arguments
 *
 * @return method instance or false in case of failure
 */
	public function __call($name, $arguments)
	{
		if (method_exists($this->piwik_helper, $name))
		{
			return call_user_func_array(array($this->piwik_helper, $name), $arguments);
		}

		trigger_error("Unknown method $name");
	}

/**
 * Magic method for getting piwik helper variables
 *
 * @access public
 * @param string $name
 *
 * @return mixed
 */
	public function __get($name)
	{
		if (property_exists($this->piwik_helper, $name))
		{
			return $this->piwik_helper->$name;
		}

		trigger_error("Unknown property $name");
	}
}

<?php
/**
 * Piwik tracking helper for CakePHP.
 *
 * You can also use this helper to easily print piwik widgets on your CakePHP website.
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
class PiwikHelper extends AppHelper
{
/**
 * Your piwik site url
 *
 * @var string
 * @access public
 */
	public $piwik_url;
/**
 * Your site id
 *
 * @var int
 * @access public
 */
	public $site;
/**
 * If set, triggers a goal conversion for website $site
 *
 * @var string
 * @access public
 */
	public $goal = false;
/**
 * Only works if $goal is set. Custom revenue for $goal.
 *
 * @var int
 * @access public
 */
	public $revenue = false;
/**
 * Page title to be sent to piwik
 *
 * @var string
 * @access public
 */
	public $title;
/**
 * Page url to be sent to piwik
 *
 * @var string
 * @access public
 */
	public $url;
/**
 * Widget shared options
 *
 * @var array
 * @access public
 */
	public $widget_defaults = array(
		'module'      => 'Widgetize', // This must be the first (so weird)
		'idSite'      => 1,
		'action'      => 'iframe',
		'date'        => 'yesterday',
		'widget'      => 1,
		'disableLink' => 1,
		'period'      => 'day'
	);
/**
 * Widget's modules, actions and default options
 * @var array
 * @access public
 */
	public $widgets = array(
		'Actions'         => array(
			'getPageUrls',
			'getPageTitles',
			'getOutlinks',
			'getDownloads',
			'getEntryPageUrls',
			'getExitPageUrls',
			'getEntryPageTitles',
			'getExitPageTitles',
			'getSiteSearchKeywords',
			'getSiteSearchCategories',
			'getSiteSearchNoResultKeywords',
			'getPageUrlsFollowingSiteSearch',
			'getPageTitlesFollowingSiteSearch'
		),
		'BotTracker'      => array('displayWidget'),
		'CustomVariables' => array('getCustomVariables'),
		'Goals'           => array('widgetGoalsOverview'),
		'Live'            => array(
			'getVisitorLog',
			'widget'
		),
		'Provider'        => array('getProvider'),
		'Referers'        => array(
			'getKeywords',
			'getWebsites',
			'getSocials',
			'getSearchEngines',
			'getCampaigns',
			'getRefererType',
			'getAll'

		),
		'SEO'             => array('getRank'),
		'UserCountry'     => array(
			'getContinent',
			'getCoutry',
			'getRegion',
			'getCity'
		),
		'UserCountryMap'  => array(
			'realtimeMap',
			'visitorMap' => array(
				'period' => 'month'
			)
		),
		'UserSettings'    => array(
			'getResolution',
			'getBrowser',
			'getBrowserVersion',
			'getBrowserType',
			'getPlugin',
			'getWideScreen',
			'getOS',
			'getConfiguration',
			'getOSFamily',
			'getMobileVsDesktop',
			'GetLanguage'
		),
		'VisitFrequency'  => array(
			'getSparklines',
			'getEvolutionGraph'
		),
		'VisitorInterest' => array(
			'getNumberOfVisitsPerVisitDuration',
			'getNumberOfVisitsPerPage',
			'getNumberOfVisitsByVisitCount',
			'getNumberOfVisitsByDaysSinceLast'
		),
		'VisitsSummary'   => array(
			'getEvolutionGraph' => array(
				'columns[]' => 'nb_visits',
			),
			'getSparklines',
			'index'
		),
		'VisitTime'       => array(
			'getVisitInformationPerLocalTime',
			'getVisitInformationPerServerTime',
			'getByDayOfWeek'
		)
	);
/**
 * Piwik tracking php object instance
 * @var object
 * @access private
 */
	private $Piwik;
/**
 * View object instance
 * @var object
 * @access private
 */
	private $View;
/**
 * HtmlHelper instance
 * @var object
 * @access private
 */
	private $Html;
/**
 * Generates the iframe widget URL based on given options
 *
 * @access private
 * @param array $widget_query Key/values to be added to the URL
 * @return string containing the final iframe src URL
 */
	private function buildWidgetUrl($widget_query = array())
	{
		$piwik_url = $this->piwik_url;
		if (!preg_match('/\/?index\.php$/', $piwik_url))
		{
			$piwik_url = rtrim($piwik_url, '/') . '/index.php';
		}
		return $piwik_url . '?' . http_build_query(array_merge($this->widget_defaults, $widget_query));
	}
/**
 * Tracking through CURl
 *
 * @access private
 * @param string $title Default's $title_for_layout
 * @return void
 */
	private function httpTracking($title = null)
	{
		if (!empty($title))
		{
			$this->title = $title;
		}

		$this->Piwik->setUrl($this->url);
		$this->Piwik->doTrackPageView($this->title);

		// Track goal conversions if defined
		if ($this->goal)
		{
			$this->Piwik->doTrackGoal($this->goal, $this->revenue);
		}
	}
/**
 * Generates the Piwik tracking image
 *
 * @access private
 * @param string $title Default's $title_for_layout
 * @return string Html image tag
 */
	private function imageTracking($title = null)
	{
		if (!empty($title))
		{
			$this->title = $title;
		}

		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper();

		$img = $this->goal ? $this->Piwik->getUrlTrackGoal($this->goal, $this->revenue) : $this->Piwik->getUrlTrackPageView($this->title);
		return $this->output($this->Html->image($img, array('alt' => '')));
	}
/**
 * Initializes PiwikTracker class and sets configuration
 * if found using Configure::read('Piwik')
 *
 * @access public
 * @return void
 */
	public function initPiwik()
	{
		if (!empty($this->Piwik))
		{
			return;
		}

		if ($configuration = Configure::read('Piwik'))
		{
			$this->loadConfiguration($configuration);
		}

		App::import('Vendor', 'Piwiktracker', true, array(), 'PiwikTracker.php');
		$this->Piwik = new PiwikTracker($this->site, $this->piwik_url);

		$this->Piwik->setPageCharset(Configure::read('App.encoding'));
	}
/**
 * Loads default configuration values
 *
 * @access private
 * @param array $configuration 
 * 
 * @return void
 */
	private function loadConfiguration($configuration)
	{
		$allowed_vars = array('piwik_url', 'site', 'goal', 'revenue');
		foreach ($configuration as $key => $value)
		{
			if (!in_array($key, $allowed_vars) || (is_string($value) && !strlen($value)))
			{
				continue;
			}

			$this->$key = $value;
		}

		if (empty($this->title) && !empty($this->View->viewVars['title_for_layout']))
		{
			$this->title = $this->View->viewVars['title_for_layout'];
		}

		if (empty($this->url) && !empty($this->here))
		{
			$this->url = $this->url(str_replace($this->base, '', $this->here), true);
		}
	}
/**
 * Easy to use tracking method. 
 *
 * @access public
 * @param string $title The page title. Takes $title_for_layout by default.
 * @param string $type The tracking type, "http" or "image" (note that when using the tracking image you must echo the results)
 *
 * @return mixed CURl response under httpTracking and image tag under imageTracking
 */
	public function track($title = null, $type = 'http')
	{
		$type = $type . 'Tracking';
		if (method_exists($this, $type))
		{
			return $this->{$type}($title);
		}
		trigger_error("Method $type does not exist");
	}
/**
 * Returns an iframe widget
 *
 * @access public
 * @param string $module The "moduleToWidgetize" value
 * @param string $action The "actionToWidgetize" value
 * @param array $widget_options An array with more parameters to be passed to the widget
 * @param array $iframe_options An array containing attributes for the iframe tag
 *
 * @return mixed A formated iframe tag or false in case of failure
 */
	public function widget($module, $action, $widget_options = array(), $iframe_options = array())
	{
		if (array_key_exists($module, $this->widgets))
		{
			$query_options = array();
			if (isset($this->widgets[$module][$action]) && is_array($this->widgets[$module][$action])) // it has properties in an array
			{
				$query_options = $this->widgets[$module][$action];
				if (!empty($widget_options))
				{
					$query_options = array_merge($query_options, $widget_options);
				}

			}
			elseif (!in_array($action, $this->widgets[$module]))
			{
				trigger_error("Action $action for module $module does not exist");
				return false;
			}

			$query_options['moduleToWidgetize'] = $module;
			$query_options['actionToWidgetize'] = $action;

			if (empty($this->Html))
			{
				App::import('Helper', 'Html');
				$this->Html = new HtmlHelper();
			}

			$iframe_properties = array(
				'width'        => '100%',
				'height'       => 350,
				'src'          => $this->buildWidgetUrl($query_options),
				'scrolling'    => 'no',
				'frameborder'  => 0,
				'marginheight' => 0,
				'marginwidth'  => 0
			);

			if (!empty($iframe_options) && is_array($iframe_options))
			{
				$iframe_properties = array_merge($iframe_properties, $iframe_options);
			}

			return $this->Html->tag('iframe', '', $iframe_properties);
		}

		trigger_error("Action $action for module $module does not exist");
		return false;
	}

/**
 * Magic method for calling PiwikTracker.php methods
 *
 * @access public
 * @param string $name
 * @param array $arguments
 *
 * @return method instance or false in case of failure
 */
	public function __call($name, $arguments)
	{
		if (method_exists($this->Piwik, $name))
		{
			return call_user_func_array(array($this->Piwik, $name), $arguments);
		}

		trigger_error("Unknown method $name");
	}

/**
 * Construct method
 *
 * @access public
 * @return void
 */
	public function __construct()
	{
		$this->View =& ClassRegistry::getObject('view');
		$this->initPiwik();
	}
}
// EOF

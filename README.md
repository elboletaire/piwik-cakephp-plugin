# Piwik Analytics plugin for CakePHP 1.3.X

This Piwik plugin has a Component and a Helper with an "autotrack" option that will let you integrate it very easyly in your CakePHP site.



## Installation

Unzip / paste the plugin folder to your CakePHP plugins folder

Go to your bootstrap.php file and add this at the bottom of the file:

```php
Configure::write('Piwik', array(
	'piwik_url' => 'http://url.to.your.piwik/installation',
	'site'      => '2', // idSite
	'autotrack' => true
));
```

Save the file and open your app_controller.php file.

Add the piwik component to your components array:

```php
$components = array(
	'Piwik.Piwik'
);
```

That's all. Now your site is tracking every visit to your piwik stats.

By default it takes $title_for_layout as page title.

## Usage and examples

Obviously you can also disable the "autotrack" feature and use the component and the helper feeting it to your own needs.

```php
// Easy tracking method (with curl)
$this->Piwik->track($page_title);
```


```php
// Getting the piwik track image
echo $this->Piwik->track($page_title, 'image');
```

You can also use any method from the [PiwikTracker class](http://piwik.org/docs/tracking-api/#toc-two-tracking-methods-image-tracking-or-using-the-api):

```php
$this->Piwik->setCustomVariable(1, 'city', 'Barcelona');
$this->Piwik->setUrl($this->here);
$this->Piwik->doTrackPageView($page_title);
```

### Displaying widgets

The piwik helper has a method named "widget" that will return an iframe tag for the desired widget:

```php
echo $this->Piwik->widget('UserCountryMap', 'visitorMap');
echo $this->Piwik->widget('VisitsSummary', 'getEvolutionGraph');
```

You can add options to the widget request:

```php
$widget_options = array();
if (!empty($user['User']['piwik_token_auth']))
{
	$widget_options['token_auth'] = $user['User']['piwik_token_auth'];
}
echo $this->Piwik->widget('UserCountryMap', 'visitorMap', $widget_options);
```

And you can of course change the iframe attributes:

```php
echo $this->Piwik->widget('Actions', 'getPageTitles', null, array('width' => 300, 'height' => 300));
```


## License

	Copyright 2013 Òscar Casajuana (a.k.a. elboletaire)

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

	   http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	imitations under the License. 

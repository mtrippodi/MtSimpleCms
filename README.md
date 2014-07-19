MtSimpleCms
===========

This is a module for Zend Framework 2. It provides a flexible, lightweight content management system. It is based on the module "PhlySimplePage" by Matthew Weier O'Phinney. https://github.com/phly/PhlySimplePage

#### Features
* Content can be fetched from a database (or any other interface can be set up too)
* Full page cashing including layout (This is mainly why I based this code on "PhlySimplePage")
* HTML-Metatags and title-tag can be set for each page individually

## Usage
#### Enable the module
```php
<?php
//config/application.config.php
return array(
    'modules' => array(
        'Application',
        'MtSimpleCms',
    ),
);
```
#### Enable the page model
In any module you want you can insert the the two models `PagesTable.php` and `PagesMetaTable.php`. Sample tables can be found in the file `mysql-structure.sql`. All these files are in the data folder.

In your `Module.php`:
```php
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
            	'PagesModel' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new \MtSimpleCms\PagesTable($dbAdapter);
                    return $table;
                }
			)
		);
		
	}
```
Be sure to change namespaces to the actual namespace of your module.
#### Setting up routing
Example:
```php
<?php
'router' => array(
        'routes' => array(
        	
            'cms' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/foo[/:lv1[/:lv2]]',
                    'defaults' => array(
                        'controller' => 'MtSimpleCms\Controller\Page',
                        'page_model' => 'PagesModel',
                        'title' => 'Foo - %s',
                        'meta_names' => array(
							'keywords' => array('Foo, Bar, %s', true),
							'description' => 'Lorem Ipsum, %s'
						),
						'meta_props' => array(
							"og:title" => '%s, Foo'
						)
                    ),
                ),
            ),
      ))
```
### The 'route' key:
May contain up to three levels (lv). Thus a valid route may be something like /foo/bar/bat/baz.
### The 'defaults' key:
Has the following keys:

**'controller' (mandatory)**
Should always be `'MtSimpleCms\Controller\Page'`

**'page_model' (mandatory)* *
Specify the name of your page model which you set up in the service config of your module (see above).

**'title' (optional)**
Do you wish that the HTML title tag of your application always contains a certain prefix or suffix? Then use this key and be sure to include `%s` for the individual page title. For example with `'title' => 'Foo - %s'` the "About Us" page will get `<title>Foo - About Us</title>`.

**'meta_names' (optional)**
Similar to title. This array may contain the following keys:

**'keywords' (optional)**
Specify a pattern for the HTML keywords meta tag. 
`'keywords' => array('Foo, Bar, %s', false)` or just `'keywords' => 'Foo, Bar, %s'` will only add the pattern, if the requested page also has keywords specified. If the page has no keywords then MtSimpleCms will not render the keywords meta tag at all.
`'keywords' => array('Foo, Bar, %s', true)` will always render the keywords meta tag with just "Foo, Bar" as its contents if the given page has no keywords specified.

**'description (optional)'**
Specify a pattern for the HTML description meta tag.
Same behaviour as 'keywords'.

**'meta_props' (optional)**
An array of HTML meta property tags. For each property you may again set a template or default pattern. See 'keywords' for usage.

#### Your layout
Just insert the following into your `layout.phtml`:
```php

        <?php 
        	echo $this->headTitle($this->title) . PHP_EOL;
        	echo $this->headMeta();
        ?>
```
#### Enabling cashing
In your `module.config.php` insert something like:
```php
'phly-simple-page' => array(
	        'cache' => array(
	            'adapter' => array(
	                'name'   => 'filesystem',
	                'options' => array(
	                    'namespace'       => 'pages',
	                    'cache_dir'       => getcwd() . '/data/cache',
	                    'dir_permission'  => '0777',
	                    'file_permission' => '0666',
	                ),
	            ),
	        ),
	    ),
	    
  ....
  
  
    'service_manager' => array(
        'factories' => array(
           'MtSimpleCms\PageCache' => 'MtSimpleCms\PageCacheService',
        ),
		
    ),
```
For clearing cashe you may use a terminal. MtSimpleCms uses the API of PhlySimplePage. See https://github.com/phly/PhlySimplePage#selectively-disabling-caching-for-given-routes for usage.

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
							'description' => array('Lorem Ipsum, %s', true)
						),
						'meta_props' => array(
							"og:title" => '%s, OG, Glasreinigung'
						)
                    ),
                ),
            ),
      ))
```



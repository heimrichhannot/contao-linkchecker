<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Widgets
	'HeimrichHannot\LinkChecker\Widgets\LinkChecker' => 'system/modules/linkchecker/widgets/LinkChecker.php',

	// Classes
	'HeimrichHannot\LinkChecker\LinkChecker'         => 'system/modules/linkchecker/classes/LinkChecker.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_linkchecker_widget'      => 'system/modules/linkchecker/templates/backend',
	'be_linkchecker'             => 'system/modules/linkchecker/templates/backend',
	'linkchecker_result_default' => 'system/modules/linkchecker/templates/linkchecker',
));

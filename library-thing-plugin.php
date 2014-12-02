<?php
/*
Plugin Name: LibraryThing Widget
Description: A plugin to cache and display a LibraryThing Collection
Version: 20130412
Plugin URI: https://github.com/jackweinbender/library-thing-widget
Author: Jack Weinbender
Author URI: https://github.com/jackweinbender
*/

date_default_timezone_set(get_option('timezone_string'));

include 'inc/LibraryThingSettings.php';
include 'inc/LibraryThingCache.php';
include 'inc/LibraryThingWidget.php';

$settings = new LibraryThingSettings();

$cache = new LibraryThingCache($settings);

$widget = new LibraryThingWidget($settings);
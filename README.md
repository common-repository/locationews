# Locationews WordPress Plugin

Publish location based articles in [Locationews](https://www.locationews.com).

## Description

Locationews is a location based publishing channel that works both as a tool for journalists (as a plugin and template for the most widely used publishing platforms such as WordPress) and as an application that shows the content in a convenient map-based interface.

Go to locationews.com, register your free account and start publishing.

The plugin is made as simple as possible so that publishing on Locationews would be effortless for the publisher. Essentially you only need to install the plugin and enable it in one switch and you are ready to go.

Locationews plugin is WordPress multisite compatible.

This plugin is built on Cerado's [Structure](https://github.com/cedaro/structure).

## Installation

1. Upload the plugin files to the `/wp-content/plugins/locationews` directory or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Locationews screen to configure the plugin.

## Changelog

### 2.0.6 (2018-08-17)
* Bugfix:	Fixed metabox javascript.

### 2.0.5 (2018-08-15)
* Changed:	Validate and double check post meta values when setting or getting the values.

### 2.0.4 (2018-05-07)
* Bugfix:   Set testing mode off. Allow to post to API.

### 2.0.3 (2018-06-02)
* NEW:  Use possible Geotags (e.g. GEO:LAT=0.0, GEO:LON=0.0) for coordinates when map coordinates not set.
* NEW:  Added option to import plugin settings.
* Fixed:    Metabox behaviour when choosing article categories.
* Bugfix:   Couldn't read the required config files.

### 2.0.2 (2018-04-11)
* Changed:	Check return type in wp_remote_get and wp_remote_post.
* Bugfix: Remove frontend JS.

### 2.0.1 (2018-03-20)
* Changed:	Not to require any location to publish articles.

### 2.0.0 (2018-01-31)
* NEW:  Completely redesigned code. Read minimum requirements.
* Changed:  Doesn't automatically publish articles with default location.
* Changed:  Minimum requirements changed. From now on this plugin requires at least PHP 5.6 or newer. Stay with the 1.1.15 if you have an older PHP version.

## Upgrade Notice
Minimun requirements has changed in 2.0.0 release. Make sure you have at least PHP 5.6 installed or stay with 1.1.15.

## Requirements
* PHP >= 5.6 (Version 2.0.0 >) or PHP >= 5.3.29 (Version 1.1.15)
* WordPress >= 4.8 (Version 2.0.0 >) or WordPress >= 4.4 (Version 1.1.15)
* cURL support

## Frequently Asked Questions
None yet.

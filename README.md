# Ride: ORM Publish Behaviour

Publish behaviour of the ORM in a Ride application.

This will add a boolean  _isPublished_ field to set the state.
Optionally you can use the _datePublishedFrom_ and _datePublishedTill_ datetime fields to specify the publication.

A method ```isPublishedEntry($date = null)``` should be used to check if the entry is published.

To enable, add _behaviour.publish_ model option and set it to ```true```.

## models.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<models>
    <model name="Post">
        <field name="title" type="string">
            <validation name="required"/>
        </field>
        <field name="teaser" type="wysiwyg" localized="true">
            <validation name="required"/>
        </field>

        <format name="title">{title}</format>
        <format name="teaser">{teaser}</format>

        <option name="behaviour.publish" value="true" />
    </model>
</models>
```

## Related Modules 

- [ride/app](https://github.com/all-ride/ride-app)
- [ride/app-orm](https://github.com/all-ride/ride-app-orm)
- [ride/lib-generator](https://github.com/all-ride/ride-lib-generator)
- [ride/lib-orm](https://github.com/all-ride/ride-lib-orm)
- [ride/lib-reflection](https://github.com/all-ride/ride-lib-reflection)

## Installation

You can use [Composer](http://getcomposer.org) to install this application.

```
composer require ride/app-orm-behaviour-publish
```

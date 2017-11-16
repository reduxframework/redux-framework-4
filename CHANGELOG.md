# Redux Framework Changelog

## 4.0.0.2
* Modified: Sorter 'checkbox' now 'toggle' with UI redesign.  Full backward compatibility in place.
* Added:    Shim for redux localization JS object from 3.x where the optName is not appended.  This broke repeater.

## 4.0.0.1
* Rewrite:  Core.  Now modularized.
* Update:   Select2 v4.0.3
* Added:    Dimension and spacing fields now contain extra and new units.
* Modified: The field 'validate' argument now supports an array of values.
* Updated:  Removed 'color_rgba' validation.  'color' validation now supports and sanitizes all color fields.
* Added:    New global arg 'admin_theme'.  The Redux Pro UI now mimics the WordPress menu system in terms of theme colors and behaviour.  Set this arg to 'classic' to use the old Redux UI.
* Fixed:    Tracking opt-in and newsletters popups now appear due to malformed inline javascript.
* Added:    Redux::disable_demo to the Redux API to disable the demo mode.  No more actions hooks.
* Added:    Redux::instance($opt_name) to the Redux API to obtain an instance of Redux based on the opt_name argument.
* Added:    Redux::get_all_instances() to the Redux API to return an array of all available Redux instances with the opt_name as they key.
* Modified: All outputting variables fully escaped to comply with wp.org and themeforest standards.
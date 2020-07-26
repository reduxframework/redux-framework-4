# Redux Framework v4 Changelog

## 4.0.4.2
* Fixed:    PHP issue when Redux was called in legacy methods.
* Fixed:    CSS output not rendering properly.

## 4.0.4
* Fixed:    PHPCS, all.
* Added:    Redux Templates.
* Added:    Complete rewrite of the underlying code base is complete and complies with all WordPress coding standards. 

## 4.0.3
* Fixed:    PHPCS findings.
* Added:    New output_variables flags that dynamically add CSS variables to pages even on fields that do not support 
            dynamic CSS output. Thanks @andrejarh for the idea!

## 4.0.2
* Fixed:    PHP backwards compatibility for extensions. Still have to work on JS probably.

## 4.0.1.9
* Fixed:    #33 - Reset Section and Reset All not show appropriate message. Thanks @voldemortensen!
* Fixed:    #29 - Multi-Text class not saving properly per new field. Adding to parent container only instead.
* Fixed:    #48 - Color RGBa field alpha was not showing.
* Removed:  Deprecated notices for old Redux API is fine.
* Fixed:    Fixes for color and comma numeric validations.
* Fixed:    #30 - Initial load of typography always initiates a redux_change. Resolved, thanks @kprovance.
* Fixed:    #31 - Text field not show the correct type, thanks @adrix71!

## 4.0.1.8
* Fixed:    #30 - Typography field causing a "save" notice.
* Added:    Start of Redux Builder API for fields.
* Modified: Moved some methods to new classes.
* Fixed:    Fix underscore naming convention in in Redux_Field,
* Modified: Move two ajax saves routines to Redux_Functions_Ex for advanced customizer validation on save.

## 4.0.1.7
* Fixed:    #20 - variable mssing $ dev.
* Fixed:    Customizer saving.
* Fixed     Customizer 'required'.
* Fixed:    button_set field not saving or loading in multi mode.
* Fixed:    Section disable and section hidde in customizer.
* Fixed:    Some malformed field ids in sample-config, for some reason.
* Change:   #19 - `validate_msg` field arg replaces `msg` for validation schemes.  Shim in place for backward compatibility.

## 4.0.1.6
* Modified: Metabox lite loop not using correct extension key.
* Fixed:    Error when no theme is installed, which is possible, apparently.

## 4.0.1.5
* Fixed:    redux_post_meta returning null always.
* Added:    New Redux API get_post_meta to retrieve Metabox values.

## 4.0.1.4
* Fixed:      Metabox lite css/js not minifying on compile.
* New:        Redux APIs set_field, set_fields

## 4.0.1.3
* Improved:   Improvement record caller and warning fixes  Thanks @Torfadel.
* Fixed:      Errors on 'Get Support' page.

## 4.0.1.2
* Fixed:      #14 - Malformed enqueue URLs when embedding.

## 4.0.1.1
* Fixed:      Section field not hiding with required calls.
* Fixed:      Tour pointer not remembing closed state. 

## 4.0.1
* New:        Initial public beta release.

## 4.0.0.22
* Added:    `allow_empty_line_height` arg for the typography field to prevent font-size from overriding a blank line-height field.

## 4.0.0.21
* Fixed:    Editor field not saving.

## 4.0.0.20
* Modified: Continued work for compatibility with the forecoming Redux Pro.
* New:      Global arg `elusive_frontend` to enqueue the internal Elusive Font CSS on the front end.

## 4.0.0.19
* Added:    Metaboxes Lite.  See READ ME & sample config (sample-metabox-config.php).
* Added:    Removed "welcome" screen.  Replaced with 'What is this?' screen that no longer appears on first launch.
* Fixed:    Demo mode actiavtes in Netword Enabled mode.
* Modified: Additional WPCS work.
* Modified: Improved tracking.

## 4.0.0.18
* Added:    Field/section disabling.  See README.

## 4.0.0.17
* Fixed:    Data caching for WordPress data class.

## 4.0.0.16
* Added:    Optional AJAX loading for select2 fields.  See README.
* Disabled: WordPress Data caching.  It's broke.  See issue tracker.

## 4.0.0.15
* Added:    Field sanitzing added.  See README.
* Added:    Sanitizing examples added to sample config.
* Fixed:    Multi text not removing new added boxes until after save.

## 4.0.0.14
* Fixed:    Sections in customizer not rendering properly when customizer is set to false.  Thanks @onurdemir.
* Fixed:    Function in ajax save class bombing when v3 is embedded.  Thanks @danielbarenkamp.

## 4.0.0.13
* Nope.  I'm supersticous!

## 4.0.0.12
* Modified: Core to accept v3 based extensions with deprecation notice.
* Modified: @Torfindel's work on the extension/builder abstract.
* Finished: New Spinner UI, with extra args.

## 4.0.0.11
* Fixed:    Typo in redux.js caused panel to stall.  My bad.  :)
* Updated:  Gulp to version 4 to solve vulnerability issues.
* Modified: Linting of remaining JS files.

## 4.0.0.10
* Modified: redux.js opt_name logic to shim in older versions of metaboxes.
* Updated:  Spinner field mods.  New look.  No more jQuery depricated notices.

## 4.0.0.9
* Fixed:    Import/Export feature not importing.  Damn typesafe decs got me again!!!  Thanks, WPCS.  ;-)
* Modified: Replace wpFileSystemInit in sample-config.php with a more practical solution.  Thanks @Torfindel 

## 4.0.0.8
* Modified: Changed typography update localize handle.  Too generic.  Conflicted with something else.
* Fixed:    Template head structure cause tempalte notice to fail.  Thanks @anikitas.
* Fixed:    Google font update choked over incorrect protocol.
* Fixed:    Required logic was operating backward.  Damn those typesafe operators!
* Fixed:    Redux v3 templates no longer crash v4 panel.
* Modified: Sample config to default settings.  They got all wonky for testing various things.

## 4.0.0.7
* Added:    'sites' to the select field data argument to return blog urls.
* Fixed:    Old extensions that extend to the ReduxFramework class failed to save.
* Fixed:    Extraneous semicolon output in admin notices.
* Fixed:    Redux v4 plugin trips fatal error on activation when v3 is embedded in a project.
* Modified: Moved new functions in Redux_Helpers due to incompatibility with embedded v3.
* Fixed:    Section field malformed when two or more section use together with no indentation.
* Fixed:    CDN loading failed even on success due to typesafe comparison (whoops, my bad) - kp.

## 4.0.0.6 (Welcome Fundraiser participants)
* Fixed:    Admin notices were msflromed due to mis-escaped code.
* Added:    Abstract class for extensions.
* Modified: Last of the JavaScript mods from JSHint and JSCS.  Travis checks will no longer fail.

## 4.0.0.3
* Fixed:    Remove plugins_loaded hook to init plugin.  Broke backward compat with Redux 3.

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

[![Build Status](https://travis-ci.org/reduxframework/redux-framework-4.png?branch=master)](https://travis-ci.org/reduxframework/redux-framework-4) [![Slack](https://redux-slackin.herokuapp.com/badge.svg)](https://redux-slackin.herokuapp.com) 
## Welcome to the Redux 4.0

Here are several guidelines to consider when testing:

1. While we feel this version of Redux is fast approaching stable, we must caution you about using this code in production.  At this time - should you chose to do so - it's with no guarantees from us for any given feature or function.

2. Unlike the current v3 of Redux, the plugin slug for v4 is not yet 'redux-framework'.  It is HIGHLY recommended you do not install this version into the current v3 folder.  The slug for this private build is 'redux-framework-4' and is what the plugin folder is titled.  When testing, please disable the v3 plugin.  Remember, the active plugin of Redux will supersede any embedded version.  You are also free to use embedding.

3. As far as known issues go, there are potentially two we are aware of that will require my further investigating.  The first is a possible 'run in' with an embedded v3 when running v4 as a plugin.  The other is custom styling for the v4 option panel.  Some folks have restyled Redux to their own liking.  Because Redux v4 is now theme aware, current custom CSS may not render properly.  If that should happen, it is possible to turn off the 'theme aware' feature and return to the v3 styling which will not interfere with any custom styling.  To do this, all the following argument to your global arguments array: `'admin_theme' => 'classic'`

4. For the time being, please report any issues here on this repo's issue tracker.  Please do not use the current redux-framework issue tracker for v3.  If filing as issue, please be aware the the usual instructions for sending reports.  Specifically, the support hash/URL and the specific steps and circumstances in which the issue occured.  Basically, we'll need instructions to guide us through what you did to recreate the issue.  If need be, we may ask you for a copy of your project in the event we're unable to recreate the issue on our own.

5. Translations.  Due to the undertaking of rewriting the Redux core, more than a few translation strings were changed, removed, or added.  When Redux v4 goes 'gold' and is added to wp.org, we will be using their online translation services.  It will allow the community to add their own translations for the many languages out there.  Redux would then automatically download the one it needs on demand, versus packing them all in one project.  For now, a current .POT file is included in the redux-core/languages folder in the event you'd like to do some local translation.  If so, please feel free to submit them via pull request.  We will add them to wp.org when Redux v4 is released.  Eventually, language submissions will be made here:  https://translate.wordpress.org/projects/wp-plugins/redux-framework (No need to do so right now, as any work done will apply to v3 and NOT v4).

6. Lastly, we are not accepting pull requests at this time.  The reason for this is because this code is extremely complicated, especially in terms of backward compatibility with v3.  Please propose changes via the issue tracker so they may be evaluated for backward compatibility.

If you would like to interact with us directly, you can join our Slack workspace at [http://slack.redux.io](http://slack.redux.io).  Join us in the [#redux-beta](https://redux.slack.com/messages/CG9F75Y7L) channel.  Our handles are @Kev and @dovy.  Don't be afraid to say hi!

Please check back nightly for new code pushes.

## We still need your support!

It is difficult and time consuming to continue development and support for this free plugin without contributions from users like yourself.  If you enjoy using Redux Framework, find it useful, and if it's saved you hours upon hours of development time, please consider [donating and helping us hit our fundraising goal](https://www.gofundme.com/development-of-redux-framework-v4). Your donations will help encourage and support the plugin’s continued development and better user support.

## Changelog ##

See [Changelog.md](https://github.com/reduxframework/redux-framework-4/blob/master/CHANGELOG.md)

## What's new?

### Core Rewrite
Our code base has been rebuilt from the ground up.  With compartmentalized code,  autoloading, and class inheritance, Redux is now faster and more efficient than it’s ever been!

### Top of the Line Security!
Redux meets security standards laid out by WordPress Coding Standards, WordPress VIP Standards, and ThemeForest Guidelines.  Focus includes escaping, sanitizing, and nonces verification, and database query prep/caching.

### Automatic Google Font Updates.
This is one of the crown jewels of Redux v4!  Your users will have the ability to update Google Fonts as updates are available or automatic ‘behind the scenes’ updates.  No more waiting for updates and no API key required!

### Basic Metaboxes
Redux now contain a ‘lite’ version of Metaboxes to support basic fields such as Checkbox, Radio Button, Text, Textarea, Media, and Color.  It’s part of our expanse into the interface builder realm.

Post Format and Page Template features are also not available.  These features plus support for all fields will be available in the Advanced Metaboxes portion of Redux Pro.

Due to the complex nature in which the Metaboxes feature integrates with Redux and existing option panels, it is important that a strict load order be maintained.  The metabox config must be loaded in your option config via a specific action hook, otherwise the metaboxes config will not load properly.  The see `BEGIN METABOX CONFIG` section of the [sample-config.php](https://github.com/reduxframework/redux-framework-4/blob/master/sample/sample-config.php) file.

The current Metabox extension *is* supported and will override the lite version. 

### Field Sanitizing
Field sanitizing allows one to pass an array of function names as an argument to a field in which the return value will be the sanitizing string.  This feature will only work with text based fields including text, textarea, and multi_text (ACE Editor and WP Editor not included).

One may use any existing function including PHP functions, WordPress functions and custom written functions.  The return value of any used function must be that of a string.  Any other return value will be disregarded.

Please view the [sample-config.php](https://github.com/reduxframework/redux-framework-4/blob/master/sample/sample-config.php) file for specific examples.

### Select2 AJAX Loading
The AJAX loading routines for the select2 fields have been fixed/finished.  See the 'capabilities' field in the demo panel for an example.  

For the interim, this feature will only work when used in conjunction with the `data` argument (that is, the one that fetches WordPress data).  

To set AJAX loading, add the `'ajax' => true` argument to your select field.  The `min_input_length` argument may also be added to specify how many characters should be typed before results are shown.  Default is `1`.

### Field/Section Disabling
This feature has been request quite a few times over the years.  Fields and sections can now be disabled by adding the `'disabled' => true` argument to either a section or a field.  The section or field will then appear 'greyed out' and not respond to input.  This comes in handy in the event one may want to offer teasers for premium versions of their products.

Since those with a little CSS know-how could easily reactive disabled fields with a little CSS, we took the added precaution of having Redux remove any `name` attributes on disabled fields/sections.  This way, even if a clever user reactivates the field, it will never save.

### Updated Panel Interface
The option panel interface has been brought up to date with the current WordPress admin design.  It is also now ‘theme aware’.  Panel colors will now follow suit with the selected admin theme.

### Improved Field Validation
Due to the need for multiple field validations, Redux now supports an array of validations versus the previously limiting single argument.  Validation results now appear in real time after a save without the need for page refresh.

### Full v3 Backward Compatibility
We take backward compatibility very seriously here and strive to maintain it.  Redux v4 has been designed to act as a drop in replacement to offer new functionality without breaking existing functionality.

## A Note About Current Redux Extensions
Redux v4 has been tested with our current extension library.  Nothing serious has come up thus far.  However, if you are using extensions and find an issue, please report it on **THIS** issue tracker.  If warranted. updates to extensions will be released for compatibility purposes only.  In order to receive updates to extensions, your subscription **MUST** be current and active.  There are no exceptions.

Future development of our extension library for new features and updates for Redux v4 will come in the form of Redux Pro.  Announcements about this product will be made as they become available.

## Frequently Asked Questions
#### What happens to Redux v3 when v4 is finished?
Upon completion, Redux v4 will completely replace v3 in our primary repository and at wp.org.  At that time, we will no longer be offering v3 in any form nor will we be maintaining any code v3.  Any copies or forks out there will be considered deprecated and should be considered 'as is'. 

#### Is Redux v4 free?
Yes.  This is the 'core' and it remains free to use, fork, embed, etc.  However, what you see is what you get.  All new features, extensions, add-ons, etc will come in the form of Redux Pro, which will require a maintained subscription for licensed usage.  We do not yet have details to offer about availability or pricing at this time.  Please join our mailing list for updates on this and other Redux related news as it becomes available: [https://redux.io/subscribe-to-redux-framework](https://redux.io/subscribe-to-redux-framework)
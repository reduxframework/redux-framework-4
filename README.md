## Welcome to the Redux 4.0 Private Beta

Dear Fundraiser participants,

Thank for for donating to the development of Redux v4.  Here is a 'sneak preview' of the last build of Redux v4.  Please check back nightly for new code pushes.  Here are a couple of guidelines to consider when testing:

First and foremeost, this is NOT production level code.  Please do not use this code in live products yet.  If you have internal teams that test your products then please feel free to inclucde Redux v4 in those tests for feedback.

Second, unlike the current v3 of Redux, the plugin slug for v4 is not yet 'redux-framework'.  It is HIGHLY recommended you do not install this version into the current v3 folder.  The slug for this private build is 'redux-dev' and that the plugin folder is titled.  When testing, please disable the v3 plugin.  If you are embedding Redux in your products, it is suggested for testing purposes to NOT embed just yet.  Run Redux v4 as a plugin.  It will supercede the embedded version.  Once the code is judged stable then embedding should not be an issue.  That said, you are also free to use embedding.  You will need to rename the current v3 and use v4 in it's place.

Third, as far as known issues go, there are potentially two I am aware of that will require my further investigating.  The first is a possible 'run in' with an embedded v3 when running v4 as a plugin.  The other is custom styling for the option panl.  Some folks have restyled Redux to their own liking.  Because Redux v4 is now theme aware, current custom CSS may not render properly.  If that should happen, it is possible to turn off the 'theme aware' feature and return to the v3 styling which will not interfere with any custom styling.  TO do this, all the followng argument to your global arguments array:

```php
'admin_theme => 'classic'
```

Fourth, for the time being, please report any issues here, on this private repo using the issue tracker.  If filing as issue, please be aware the the usual instructions for sending reports.  Specifically, the support hash/URL and the specific steps and circumstances in which the issue occured.  Basically, I need you to give me instructions to guide me through what you did to recrate the issue.  If need be, I may ask you for a copy of your project in the event I am unable to recreate the issue on my own.

Fifth; Tranlations.  Due to the undertaking of rewriting the core, more than a few translation strings were changed, removed or added.  When Redux v4 goes 'gold' and is added to wp.org, we will be using their online translation services.  It will allow the community to add their own for the many languages out there.  Redux would then automatically download the one it needs on demand, versus packing them all in one project.  For now, a current .POT file is included in the ReduxCore/languages folder in the event you'd like to do some local translation.  If so, please feel free to submit them via pull request.  We will add them to wp.org when Redux v4 is released.  Eventually, lanuage submissions will be made here:  https://translate.wordpress.org/projects/wp-plugins/redux-framework (No need to do so right now, as any work done will apply to v3 and NOT v4).

Lastly, since this repo ios a fork of the main one, pull requests could become complicated.  If you have one, please note the following:

PLEASE DO NOT PUSH CODE DIRECTLY TO THIS REPO!  Please send pull requests only.  Due to the complexity of Redux Framework, we prefer to vet new code and changes for backward compatibility and other potential incompatibilities.  Code pushed directly to this repo will be undone.

PLEASE do not post code anywhere outside this repo.

PLEASE DO NOT CHANGE OUR FORMATTING!!!  While we recognize developers have their own style, we prefer ours.

Thank you for respecting this guidelines.  

If you want to interact with us directly, you can join our slack on http://slack.redux.io.  We have a private channel deticated for this beta.  My handle is @Kev.  Message me for access!

Thank you again for donating and contributing to Reduc v4!

## Changelog ##

See [Changelog.md](https://github.com/dovy/redux-dev/blob/master/CHANGELOG.md)

## Field Sanitizing ##

Field sanitizing allows one to pass an array of function names as an argument to a field in which the return value will be the sanitizing string.  This feature will only work with text based fields including text, textarea, and multi_text (ACE Editor and WP Editor not included).

One may use any existing function including PHP functions, WordPress functions and custom written functions.  The return value of any used function must be that of a string.  Any other return value will be disregarded.

Please view the [sample-config.php](https://github.com/kprovance/redux-dev/blob/master/sample/sample-config.php) file for specific examples.

## Select2 AJAX Loading ##

The AJAX loading routines for the select2 fields have been fixed/finished.  See the 'capabilities' field in the demo panel for an example.  

For the interim, this feature will only work when used in conjunction with the `data` argument (that is, the one that fetches WordPress data).  

To set AJAX loading, add the `'ajax' => true` argument to your select field.  The `min_input_length` argument may also be added to specify how many characters should be typed before results are shown.  Default is `1`.

## Field/Section Disabling ##

This feature has been request quite a few times over the years.  Fields and sections can now be disabled by adding the `'disabled' => true` argument to either a section or a field.  The section or field will then appear 'greyed out' and not respond to input.  This comes in handy in the event one may want to offer teasers for premium versions of their products.

Since those with a little CSS know-how could easily reactive disabled fields with a little CSS, I took the added precaution of having Redux remove any `name` atttibutes on disabled fields/sections.  This way, even if a clever user reactivates the field, it will never save.

## Metabox Lite ##

A lite version of Metaboxes is available with Redux v4.  Please see the [sample-metabox-config.php](https://github.com/kprovance/redux-dev/blob/master/sample/sample-metabox-config.php) in the `sample` folder for usage.

Metabox Lite supports the following fields only:  `text, textarea, checkbox, radio, color, media`.  Post Format and Page Template features are also not available.  These features plus support for all fields will be available in the Advanced Metaboxes portion of Redux Pro.

Due to the complex nature in which the Metaboxes feature integrates with Redux and existing option panels, it is important that a strict load order be maintained.  The metabox config must be loaded in your option config via a specific action hook, otherwise the metaboxes config will not load properly.  The see `BEGIN METABOX CONFIG` section of the [sample-config.php](https://github.com/kprovance/redux-dev/blob/master/sample/sample-config.php) file.

The current Metabox extension *is* supported and will override the lite version. 
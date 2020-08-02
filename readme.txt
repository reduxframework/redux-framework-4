=== Redux Framework ===
Contributors: dovyp, redux
Donate link: http://paypal.me/reduxframework
Tags: admin, admin interface, options, theme options, plugin options, options framework, settings, web fonts, google fonts
Requires at least: 4.0
Requires PHP: 5.3
Tested up to: 5.5
Stable tag: 4.1.9
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Redux is a simple, truly extensible and fully responsive options framework for WordPress themes and plugins.

== Description ==

Redux is a simple, truly extensible and fully responsive options framework for WordPress themes and plugins. Built on the WordPress Settings API, Redux supports a multitude of field types as well as: custom error handling, custom fields & validation types, and import/export functionality.

But what does Redux actually DO? We don't believe that theme and plugin
developers should have to reinvent the wheel every time they start work on a
project. Redux is designed to simplify the development cycle by providing a
streamlined, extensible framework for developers to build on. Through a
simple, well-documented config file, third-party developers can build out an
options panel limited only by their own imagination in a fraction of the time
it would take to build from the ground up!

= Online Demo =
Don't take our word for it, check out our online demo and try Redux without installing a thing!
[**http://demo.redux.io/**](http://demo.redux.io/)

= Use the Redux Builder to Get Started =
Want to use Redux, but not sure what to do? Use our [builder](http://build.redux.io/)! It will allow you to make
a custom theme based on [_s](http://underscores.me), [TGM](http://tgmpluginactivation.com), and [Redux](http://redux.io), and any Redux arguments you want to set.
Don't want to make your own theme? Then output a custom admin folder that you can place
in a theme or plugin. Oh and did we mention it's free? Try it today at:
[**http://build.redux.io/**](http://build.redux.io/)


= Docs & Support =
We have extremely extensive docs. Please visit [http://docs.redux.io/](http://docs.redux.io/) If that doesn’t solve your concern, you should search [the issue tracker on Github](https://github.com/reduxframework/redux-framework/issues). If you can't locate any topics that pertain to your particular issue, [post a new issue](https://github.com/reduxframework/redux-framework/issues/new) for it. Before you submit an issue, please read [our contributing requirements](https://github.com/redux-framework/redux-framework/blob/master/CONTRIBUTING.md). We build off of the dev version and push to WordPress.org when all is confirmed stable and ready for release.


= Redux Framework Needs Your Support =
It is hard to continue development and support for this free plugin without contributions from users like you. If you enjoy using Redux Framework, and find it useful, please consider [making a donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MMFMHWUPKHKPW). Your donation will help encourage and support the plugin's continued development and better user support.

= Fields Types =

* Background
* Border
* Button Set
* Checkbox / Multi-Check
* Color (WordPress Native)
* Color Gradient
* Color RGBA
* Date
* Dimensions (Height/Width)
* Divide (Divider)
* Editor (WordPress Native)
* Gallery (WordPress Native)
* Image Select (Patterns/Presets)
* Import/Export
* Info (Header/Notice)
* Link Color
* Media (WordPress Native)
* Multi-Text
* Password
* Radio (w/ WordPress Data)
* Raw (HTML/PHP/MarkDown)
* Section (Indent and Group Fields)
* Select (Select/Multi-Select w/ Select2 & WordPress Data)
* Select Image
* Slider (Drag a Handle)
* Slides (Multiple Images, Titles, and Descriptions)
* Sortable (Drag/Drop Checkbox/Input Fields)
* Sorter (Drag/Drop Manager - Works great for content blocks)
* Spacing (Margin/Padding/Absolute)
* Spinner
* Switch
* Text
* Textarea
* Typography
 * The most advanced typography module complete with preview, Google fonts, and auto-css output!

= Additional Features =

* Field Validation
* MANY translations. (See below)
* Full value escaping.
* Required - Link visibility from parent fields. Set this to affect the visibility of the field on the parent's value. Fully nested with multiple required parents possible.
* Output CSS Automatically - Redux generates CSS and the appropriate Google Fonts stylesheets for you on select fields. You need only specify the CSS selector to apply the CSS to (limited to certain fields).
* Compiler integration! A custom hook runs when any fields with the argument `compile => true` are changed.
* Oh, and did we mention a fully integrated Google Webfonts setup that will make you so happy you'll want to cry?

= Get Involved =
Redux is an ever-changing, living system. Want to stay up to date or
contribute? Subscribe to one of our mailing lists or join us on [Facebook](https://facebook.com/reduxframework) or [Twitter](https://twitter.com/reduxframework) or [Github](https://github.com/ReduxFramework/ReduxFramework)!

NOTE: Redux is not intended to be used on its own. It requires a config file
provided by a third-party theme or plugin developer to actual do anything
cool!

## Privacy Policy
Redux Framework uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.

Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users.

Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**

Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

== Installation ==

= For Complete Documentation and Examples =
Visit: [http://docs.redux.io/](http://docs.redux.io/)

== Frequently Asked Questions ==

= Why doesn't this plugin do anything? =

Redux is an options framework... in other words, it's not designed to do anything on its own! You can however activate a demo mode to see how it works.

= How can I learn more about Redux? =

Visit our website at [http://redux.io/](http://redux.io/)

= You don't have much content in this FAQ section =
That's because the real FAQ section is on our site! Please visit [http://docs.redux.io/faq/](http://docs.redux.io/faq/)

== Screenshots ==

1. This is the demo mode of Redux Framework. Activate it and you will find a fully-functional admin panel that you can play with. On the Plugins page, beneath the description and an activated Redux Framework, you will find a Demo Mode link. Click that link to activate or deactivate the sample-config file Redux ships with.  Don't take our word for it, check out our online demo and try Redux without installing a thing! [**http://demo.redux.io/wp-admin/**](http://demo.redux.io/wp-admin/)

== Changelog ==

= 4.1.9 =
* Fixed: Compatibility issue when developers made custom panel templates. The opt_name wasn't fetched and thus saving broke.
* Release date: Aug 1, 2020

= 4.1.8 =
* Fixed: Map files are now all present.
* Fixed: Path fix for how developers called the typography file directory.
* Release date: Aug 1, 2020

= 4.1.7 =
* Fixed: Issue with sortable in text mode not properly passing the name attribute and thus not saving properly.
* Fixed: Compatibility with old extension names to not crash other plugins.
* Release date: July 31, 2020

= 4.1.6 =
* Fixed: Issue with customizer double loading the PHP classes and causing an exception.
* Fixed: Chanced a class name as to not conflict with a 6+ year old version of Redux.
* Release date: July 30, 2020

= 4.1.5 =
* Fixed: Google fonts not working when old configs used string vs an array for output.
* Release date: July 30, 2020

= 4.1.4 =
* Fixed: Google fonts loading over non-secure breaks fonts. Forced all SSL for Google fonts.  :)
* Release date: July 30, 2020

= 4.1.3 =
* Fixed: Issue where theme devs tried to bypass the framework. Literally I made an empty file to fix their coding. :P
* Release date: July 29, 2020

= 4.1.2 =
* Fixed: Don't try to set empty defaults when none are present.
* Fixed: Issue where the WP Data argument was misused.
* Release date: July 29, 2020

= 4.1.1 =
* Fixed: CSS decode when esc_attr replaces the HTML characters and CSS outputs are set with >'s.
* Release date: July 29, 2020

= 4.1.0 =
* Fixed: Compatibility with certain themes using the deprecated $_is_plugin variable.
* Release date: July 29, 2020

= 4.0.9 =
* Fixed: Complete compatibility fix for older Redux extensions.
* Release date: July 28, 2020

= 4.0.8 =
* Fixed: Initial library load was failing on some server setups.
* Release date: July 28, 2020

= 4.0.7 =
* Fixed: Race condition for PHP include for Redux_Typography causing blank white screens.
* Release date: July 28, 2020

= 4.0.5 =
* Fixed: Issues where the site crashes because of varied ways Redux was called.
* Fixed: Varied implementations of opt_names resulting in option panels not working as expected.
* Release date: July 28, 2020

= 4.0.4 =
* Release date: July 24, 2020

== Attribution ==

Redux is was originally based off the following frameworks:

* [NHP](https://github.com/leemason/NHP-Theme-Options-Framework)
* [SMOF](https://github.com/syamilmj/Options-Framework "Slightly Modified Options Framework")

It has now a completely different code base. If you like what you see, realize this is a labor of love. Please [donate to the Redux Framework](http://paypal.me/reduxframework) if you are able.

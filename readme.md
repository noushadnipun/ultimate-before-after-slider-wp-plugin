===Ultimate Before After Wp Slider ===
Contributors: noushadnipun
Tags: elementor, before after, image comparison, slider, widget
Requires at least: 6.3
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, accessible before/after image comparison slider. Gutenberg block, Elementor widget and shortcode.

== Description ==

Adds a "Before After Slider" block for the block editor, a widget for Elementor, and a `[before_after_slider]` shortcode for posts, pages, widgets and theme templates.

**Features**

* Horizontal and vertical orientation
* Drag, touch, or hover to compare
* Keyboard accessible (arrow keys, Home, End)
* Custom starting position
* Optional Before/After labels with custom text
* Style controls: handle color, line width, knob size, labels, border radius
* No jQuery dependency for the slider, no external requests, no tracking
* Assets load only on pages that use the widget

The block and the shortcode work without Elementor. The widget requires the free Elementor plugin.

**Shortcode examples**

`[before_after_slider before="123" after="124"]`

`[before_after_slider before="https://example.com/a.jpg" after="https://example.com/b.jpg" orientation="vertical" offset="30" labels="no"]`

Attributes: before, after (attachment ID or URL), size (e.g. large or 800x600), orientation, offset, hover, labels, before_label, after_label, alt_before, alt_after, width (e.g. 600, 600px or 80%), align (left, center, right).

Defaults for the shortcode can be changed in Settings > Before After Slider.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install it from the Plugins screen.
2. Activate the plugin (Elementor must be active).
3. In the Elementor editor, search for "Before After Slider" and drag it onto your page.
4. In the block editor, add the "Before After Slider" block. Or use the shortcode anywhere. Adjust defaults in Settings > Before After Slider.

== Frequently Asked Questions ==

= Does it need Elementor? =
Only for the Elementor widget. The block and the shortcode work on any WordPress site.

= Does it need Elementor Pro? =
No.

= The two images look cropped. =
Use images with the same aspect ratio for the best result.

== Changelog ==

= 1.2.1 =
* The slider now displays at the selected image size instead of always filling its container.
* New width and alignment options in the Elementor widget, block and shortcode.

= 1.2.0 =
* Added Gutenberg block (Media category) with live preview and sidebar options.

= 1.1.1 =
* Custom image size in the Elementor widget and shortcode (size="800x600").

= 1.1.0 =
* Added [before_after_slider] shortcode.
* Added settings page for shortcode defaults.
* Fixed widget registration depending on plugin load order.

= 1.0.0 =
* Initial release.

Version 1.6.5

=== Global WooCommerce Reviews for Elementor ===
Contributors: custom
Tags: woocommerce, reviews, elementor, review carousel, testimonials
Requires at least: 6.4
Requires PHP: 7.4
Stable tag: 1.6.5

== 1.6.5 ==
Added three responsive left-margin sliders directly below the corresponding submit-button width controls.


== 1.6.5 ==
* Added separate Elementor button-width sliders for desktop, tablet, and mobile breakpoints.
* Button widths are entered in pixels and remain constrained to the available form width.

Adds an Elementor widget that displays approved WooCommerce reviews from all products in a single-review carousel. Reviews attached to product variations are attributed to the parent product.

== 1.5.6 ==
* Fixed the Subject column so it appears in WooCommerce Products > Reviews using WooCommerce's dedicated ReviewsListTable hooks.
* Added an editable Review Subject field to the admin comment edit screen.

== 1.3.0 ==
* Reworked the widget into a single-review carousel layout inspired by the supplied reference.
* Added previous/next pagination controls and a page indicator.
* Defaulted to one review per page, centered review content, gold five-star display, reviewer avatar, author, and date.
* Variation reviews remain merged under their parent product identity.
* Removed the previous Load More interaction in favor of pagination.

== Installation ==
1. Upload the plugin ZIP from WordPress > Plugins > Add New Plugin > Upload Plugin.
2. Activate the plugin.
3. Edit your Elementor Shop/Archive template.
4. Search for "Global Product Reviews" and insert the widget.
5. Keep "Reviews Per Page" at 1 for the supplied carousel look.


NEW IN 1.3.0
- Added the Global Write a Review Elementor widget.
- Customers can select a parent product from a dropdown and submit a 1–5 star WooCommerce review.
- Variation reviews remain grouped under their parent product in the Global Product Reviews widget.
- Reviews respect WordPress comment moderation and WooCommerce review status.

Version 1.3.0 adds a polished responsive review form, yellow/black outlined stars on transparent backgrounds, a confirmation checkbox, and mobile layout tuned for 480px screens.


Version 1.5.5
- Replaced the Write a Review product selector with a required Subject field.
- The review is associated automatically with the WooCommerce product page containing the widget.
- The submitted subject is saved with the review and displayed in the Reviews widget where the product name was previously shown.


Version 1.6.1
- Changed the Write a Review email placeholder to "Your Email".
- Added a responsive Elementor Star Border Weight slider to the Global Reviews widget.

* Button width and left-margin controls now support px, %, and vw units.

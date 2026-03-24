=== Sekura REST Bridge for ACF ===
Contributors: cwdekker
Tags: acf, rest-api, custom-fields, json, wp-api
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Expose Advanced Custom Fields in the WordPress REST API with proper access control.

== Description ==

Sekura REST Bridge for ACF securely exposes Advanced Custom Fields in the WordPress REST API. Based on [ACF to REST API](https://github.com/airesvsg/acf-to-rest-api) by Aires Goncalves, rebuilt with security as a first-class concern.

The original ACF to REST API plugin exposes all ACF field data to unauthenticated requests, including options pages (which often contain API keys and secrets), user profile fields, and fields on private/draft posts. This plugin fixes that with proper WordPress capability checks on every endpoint.

= Features =

* Proper permission checks on every endpoint
* Per-field Show in REST API / Edit in REST API toggles
* Works alongside ACF Pro's native REST support
* Drop-in replacement for ACF to REST API (same acf/v3 namespace)
* Filterable permissions for custom access control

= Endpoints =

* `GET /wp-json/acf/v3/{post_type}/{id}` - Get ACF fields for a post
* `GET /wp-json/acf/v3/{post_type}/{id}/{field}` - Get a specific field
* `PUT /wp-json/acf/v3/{post_type}/{id}` - Update ACF fields
* `GET /wp-json/acf/v3/options/{id}` - Get ACF options page fields
* `GET /wp-json/acf/v3/users/{id}` - Get ACF fields for a user
* `GET /wp-json/acf/v3/comments/{id}` - Get ACF fields for a comment

ACF data is also appended as an `acf` key on standard WP REST API responses.

== Installation ==

1. Upload the `sekura-rest-bridge-for-acf` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins menu in WordPress
3. Ensure Advanced Custom Fields is installed and active

== Frequently Asked Questions ==

= Does this work with ACF Pro? =

Yes. Sekura REST Bridge for ACF works alongside ACF Pro's native REST support and overrides it to provide consistent access control.

= Is this a drop-in replacement for ACF to REST API? =

Yes. It uses the same `acf/v3` namespace and `acf` response key. Deactivate the old plugin and activate this one.

== Changelog ==

= 1.0.0 =
* Initial release
* Secure permission checks on all endpoints
* Per-field REST API visibility toggles
* ACF Pro compatibility

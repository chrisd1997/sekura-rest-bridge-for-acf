# Sekura REST Bridge for ACF

Expose ACF fields in the WordPress REST API with proper access control.

Based on [ACF to REST API](https://github.com/airesvsg/acf-to-rest-api) by Aires Goncalves, rebuilt with security as a first-class concern.

## Why this plugin?

The original ACF to REST API plugin exposes all ACF field data to unauthenticated requests — including options pages (which often contain API keys and secrets), user profile fields, and fields on private/draft posts. This plugin fixes that with proper WordPress capability checks on every endpoint.

## Requirements

- WordPress 5.0+
- PHP 7.4+
- [Advanced Custom Fields](https://www.advancedcustomfields.com/) 5.x or 6.x

## Installation

1. Download or clone this repository into your `wp-content/plugins/` directory
2. Activate **Sekura REST Bridge for ACF** in the WordPress admin

## Endpoints

The plugin registers endpoints under the `acf/v3` namespace:

| Endpoint | Description |
|----------|-------------|
| `GET /wp-json/acf/v3/{post_type}/{id}` | Get ACF fields for a post |
| `GET /wp-json/acf/v3/{post_type}/{id}/{field}` | Get a specific field |
| `GET /wp-json/acf/v3/{post_type}` | List ACF fields for posts |
| `PUT /wp-json/acf/v3/{post_type}/{id}` | Update ACF fields |
| `GET /wp-json/acf/v3/options/{id}` | Get ACF options page fields |
| `GET /wp-json/acf/v3/users/{id}` | Get ACF fields for a user |
| `GET /wp-json/acf/v3/comments/{id}` | Get ACF fields for a comment |

ACF data is also appended as an `acf` key on standard WP REST API responses (e.g. `/wp-json/wp/v2/posts/{id}`).

## Access Control

Every endpoint enforces permission checks appropriate to the resource type:

| Resource | Read | Write |
|----------|------|-------|
| **Posts** | Published posts are public. Drafts/private require `read_post` capability. | Requires `edit_posts` |
| **Options** | Requires `manage_options` (admin only) | Requires `manage_options` |
| **Users** | Own profile is always accessible. Others require `list_users`. | Own profile or `edit_users` |
| **Comments** | Approved comments are public. Others require `moderate_comments`. | Requires `edit_posts` |
| **Terms** | Public taxonomies are open. Private taxonomies require `manage_terms`. | Requires `edit_posts` |
| **Attachments** | Same as posts (inherits status check) | Requires `edit_posts` |

All permission checks are filterable — see [Filters](#filters) below.

## Per-Field Visibility

Each ACF field has **Show in REST API?** and **Edit in REST API?** toggles in the field settings. Fields default to hidden until explicitly enabled, giving you granular control over what is exposed.

## Filters

| Filter | Description | Default |
|--------|-------------|---------|
| `sekura/item_permissions/get` | Override read permission for a single item | Varies by type |
| `sekura/items_permissions/get` | Override read permission for collections | Varies by type |
| `sekura/item_permissions/update` | Override write permission | `current_user_can(...)` |
| `sekura/key` | Change the request parameter key for field data | `'fields'` |
| `sekura/id` | Modify the ACF post ID used for field lookups | Varies |
| `sekura/{type}/get_fields` | Filter returned field data | — |
| `sekura/{type}/get_items` | Filter collection responses | — |
| `sekura/{type}/prepare_item` | Filter data before database write | — |

### Example: Make options readable by editors

```php
add_filter( 'sekura/item_permissions/get', function( $permitted, $request, $type ) {
    if ( 'option' === $type && current_user_can( 'edit_others_posts' ) ) {
        return true;
    }
    return $permitted;
}, 10, 3 );
```

## Updating fields

Send a PUT/PATCH/POST request with a `fields` parameter:

```bash
curl -X PUT \
  https://example.com/wp-json/acf/v3/posts/123 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"fields": {"my_field": "new value"}}'
```

## License

GPLv3 or later. See [LICENSE](LICENSE) for the full text.

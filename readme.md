# Better wp_die

This plugin adds a new wp_die handler which includes:

 - A permanent back button
 - A friendly message to help non-developers
 - Formatted output for arrays and objects
 - If WP_DEBUG is enabled, a stacktrace
 - A filter for adding a logo

## Usage

Adding a logo is as simple as:

```
add_filter( 'better-wp-die-image', function () {
	return 'http://placekitten.com.s3.amazonaws.com/homepage-samples/200/286.jpg';
});
```

The logo will appear to the right at the top of the wp_die output.
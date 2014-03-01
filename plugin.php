<?php
/*
Plugin Name: Better WP Die
Plugin URI: http://tomjn.com
Description: A much improved WP Die with a back button and debug data when WP_DEBUG is enabled
Author: Tom J Nowell
Version: 1.0
Author URI: http://www.tomjn.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


add_filter( 'wp_die_handler', 'tomjn_set_die_handler' );
function tomjn_set_die_handler() {
	return 'tomjn_wp_die_handler';
}

function tomjn_wp_die_handler( $message, $title = '', $args = array() ) {
	$defaults = array( 'response' => 500 );
	$r        = wp_parse_args( $args, $defaults );

	$have_gettext = function_exists( '__' );

	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
		if ( empty( $title ) ) {
			$error_data = $message->get_error_data();
			if ( is_array( $error_data ) && isset( $error_data['title'] ) ) {
				$title = $error_data['title'];
			}
		}
		$errors = $message->get_error_messages();
		switch ( count( $errors ) ) :
			case 0 :
				$message = '';
				break;
			case 1 :
				$message = "<p>{$errors[0]}</p>";
				break;
			default :
				$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
				break;
		endswitch;
	} elseif ( is_string( $message ) ) {
		$message = "<p>$message</p>";
	} elseif ( is_array( $message ) || is_object( $message ) ) {
		$message = '<pre>' . print_r( $message, true ) . '</pre>';
	}

	if ( isset( $r['back_link'] ) && $r['back_link'] ) {
		$back_text = $have_gettext ? __( '&laquo; Back' ) : '&laquo; Back';
		$message .= "\n<p><a href='javascript:history.back()'>$back_text</a></p>";
	}

	if ( ! did_action( 'admin_head' ) ) :
		if ( ! headers_sent() ) {
			status_header( $r['response'] );
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		}

		if ( empty( $title ) ) {
			$title = $have_gettext ? __( 'WordPress &rsaquo; Error' ) : 'WordPress &rsaquo; Error';
		}

		$text_direction = 'ltr';
		if ( isset( $r['text_direction'] ) && 'rtl' == $r['text_direction'] )
			$text_direction = 'rtl';
		elseif ( function_exists( 'is_rtl' ) && is_rtl() )
			$text_direction = 'rtl';
		?>
		<!DOCTYPE html>
		<!-- Ticket #11289, IE bug fix: always pad the error page with enough characters such that it is greater than 512 bytes, even after gzip compression abcdefghijklmnopqrstuvwxyz1234567890aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzz11223344556677889900abacbcbdcdcededfefegfgfhghgihihjijikjkjlklkmlmlnmnmononpopoqpqprqrqsrsrtstsubcbcdcdedefefgfabcadefbghicjkldmnoepqrfstugvwxhyz1i234j567k890laabmbccnddeoeffpgghqhiirjjksklltmmnunoovppqwqrrxsstytuuzvvw0wxx1yyz2z113223434455666777889890091abc2def3ghi4jkl5mno6pqr7stu8vwx9yz11aab2bcc3dd4ee5ff6gg7hh8ii9j0jk1kl2lmm3nnoo4p5pq6qrr7ss8tt9uuvv0wwx1x2yyzz13aba4cbcb5dcdc6dedfef8egf9gfh0ghg1ihi2hji3jik4jkj5lkl6kml7mln8mnm9ono
		-->
		<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists( 'language_attributes' ) && function_exists( 'is_rtl' ) ) language_attributes(); else echo "dir='$text_direction'"; ?>>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php echo $title ?></title>
			<style type="text/css">
				html {
					background: #eee;
				}

				body {
					background:         #fff;
					color:              #333;
					font-family:        "Open Sans", sans-serif;
					margin:             2em auto;
					padding:            1em 2em;
					max-width:          700px;
					-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
					box-shadow:         0 1px 3px rgba(0, 0, 0, 0.13);
				}

				h1 {
					border-bottom:  1px solid #dadada;
					clear:          both;
					color:          #666;
					font:           24px "Open Sans", sans-serif;
					margin:         30px 0 0 0;
					padding:        0;
					padding-bottom: 7px;
				}

				#error-page {
					margin-top: 50px;
				}

				#error-page p {
					font-size:   14px;
					line-height: 1.5;
					margin:      25px 0 20px;
				}

				#error-page code {
					font-family: Consolas, Monaco, monospace;
				}

				ul li {
					margin-bottom: 10px;
					font-size:     14px;
				}

				a {
					color:           #21759B;
					text-decoration: none;
				}

				a:hover {
					color: #D54E21;
				}

				.button {
					background:            #f7f7f7;
					border:                1px solid #cccccc;
					color:                 #555;
					display:               inline-block;
					text-decoration:       none;
					font-size:             13px;
					line-height:           26px;
					height:                28px;
					margin:                0;
					padding:               0 10px 1px;
					cursor:                pointer;
					-webkit-border-radius: 3px;
					-webkit-appearance:    none;
					border-radius:         3px;
					white-space:           nowrap;
					-webkit-box-sizing:    border-box;
					-moz-box-sizing:       border-box;
					box-sizing:            border-box;

					-webkit-box-shadow:    inset 0 1px 0 #fff, 0 1px 0 rgba(0, 0, 0, .08);
					box-shadow:            inset 0 1px 0 #fff, 0 1px 0 rgba(0, 0, 0, .08);
					vertical-align:        top;
				}

				.button.button-large {
					height:      29px;
					line-height: 28px;
					padding:     0 12px;
				}

				.button:hover,
				.button:focus {
					background:   #fafafa;
					border-color: #999;
					color:        #222;
				}

				.button:focus {
					-webkit-box-shadow: 1px 1px 1px rgba(0, 0, 0, .2);
					box-shadow:         1px 1px 1px rgba(0, 0, 0, .2);
				}

				.button:active {
					background:         #eee;
					border-color:       #999;
					color:              #333;
					-webkit-box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
					box-shadow:         inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
				}

				pre {
					overflow-x:  scroll;
					padding:     2em 1.5em;
					background:  #fdfdfd;
					border:      1px solid #f0f0f0;
					margin:      1em 0;
					line-height: 1.4em;
				}

				<?php if ( 'rtl' == $text_direction ) : ?>
				body {
					font-family: Tahoma, Arial;
				}

				<?php endif; ?>
			</style>
		</head>
		<body id="error-page">
		<?php

		$image = apply_filters( 'better-wp-die-image', '' );
		if ( !empty( $image ) ) {
			?>
			<img style="margin:1.5em 0 2em 10px; float:right;" src="<?php echo $image; ?>" />
			<?php
		}
		?>
		<h1 style="display:inline-block">Oh Dear</h1>
		<p>Something wrong happened and you ended up here. For more details see the message below.</p>
		<a href="javascript:history.back();" class="button">&laquo; Go Back</a>
		<?php
		$admin_email = get_option( 'admin_email' );
		if ( ! empty( $admin_email ) ) {
			?> <a href="mailto:<?php echo $admin_email; ?>" class="button">Contact Site Administrator</a><?php
		}
	endif; // ! did_action( 'admin_head' )
	?>
	<h1 style="clear:both;">Message</h1>
	<?php
	echo $message;
	if ( is_super_admin() || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
		?>
		<h1 style="clear:both;">Stack Trace</h1>
		<pre><?php debug_print_backtrace(); ?></pre>
	<?php
	}
	?>

</body>
	</html>
	<?php
	die();
}

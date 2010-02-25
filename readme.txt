=== Plugin Name ===
Contributors: geraint
Donate link: http://www.twothirdsdesign.co.uk/
Tags: files, protection, retsrict access,
Requires at least: 2.9.x
Tested up to: 2.9.2
Stable tag: 0.1

File Proxy is a simple WordPress plug that lest you protect / restrict access to a specific embedded file.

== Description ==

File Proxy is a simple WordPress plug that lest you protect / restrict access to a specific embedded file.  It lets you embed files from the upload directory into a post or page using a short code that restricts access to registered users.  guest users who click on the link are prompted to login before returning the file.

Key Features
* The true file URL is never revealed preventing hot linking.
* cherry pick which files you want to protect.
* simple shortcode interface.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `file-proxy` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `[file-proxy id='attachment_id']link text[/file-proxy]` or `[file-proxy id='attachment_id']` in your page or post

== Screenshots ==
1. Screen shots to follow

== Frequently Asked Questions ==

= How do i find the attachment id? =

1. Go to the Media Library and hover over the file you want to attach, at the end of the url in the status bar you will see `&attachment_id=xxx`
1. the number at the end is the number you want.

== Changelog ==

= 0.1 =
First Release

== Upgrade Notice ==

= 0.1 =
First Release
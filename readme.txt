=== File Proxy ===
Contributors: geraint
Tags: files, protection, retsrict access, user, protect, in, logged, download, proxy, files, private
Requires at least: 4.1.x
Tested up to: 4.1
Stable tag: 0.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

File Proxy lest you protect / restrict access to a specific embedded file making sure users are logged in before they can download any files.

== Description ==

File Proxy is a simple WordPress plug that lest you protect / restrict access to a specific embedded file.  
It lets you embed files from the upload directory into a post or page using a short code that restricts access to registered users.  
Guest users who click on the link are prompted to login before returning the file.

File Proxy acts as a Proxy protecting the specific file 

Key Features:

* The true file URL is never revealed preventing hot linking.
* Cherry pick which files you want to protect.
* Admin Settings Panel - allowing full customisation.
* Simple shortcode interface.
* File-Proxy link button in media manager.
* Display alt image for guest.
* No htaccess hacking, required.

**Contributing**

The development home for this plugin is on GitHub. This is where active development happens, along with issue tracking and associated discussions.

https://github.com/geraintp/file-proxy

**Support**

Support for this plugin will be provided in the form of _Product Support_. This means that I intend to fix any confirmed bugs and improve the user experience when enhancements are identified and can reasonably be accomodated. There is no _User Support_ provided for this plugin. If you are having trouble with this plugin in your particular installation of WordPress, I will not be able to help you troubleshoot the problem.

This plugin is provided under the terms of the GPL, including the following:

> BECAUSE THE PROGRAM IS LICENSED FREE OF CHARGE, THERE IS NO WARRANTY
> FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW.  EXCEPT WHEN
> OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES
> PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED
> OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
> MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.  THE ENTIRE RISK AS
> TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU.  SHOULD THE
> PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING,
> REPAIR OR CORRECTION.

== Installation ==

1. Install through the WordPress admin.

_or_

1. Upload `file-proxy` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `[file-proxy id='attachment_id']link text[/file-proxy]` or `[file-proxy id='attachment_id']` in your page or post

== Screenshots ==
none

== Frequently Asked Questions ==

= How do I find the attachment id? =

1. Go to the Media Library and hover over the file you want to attach, at the end of the url in the status bar you will see `&attachment_id=xxx`
1. the number at the end is the number you want.

== Changelog ==

= 0.7 =
* Added admin options panel
* Added file proxy link button to media uploader.

= 0.6 =
* This version requires WordPress 4.1 or later
* Update Compatibility with WordPress 4.1
* Plugin Reboot.
* Moved to http://github.com/geraintp/file-proxy 

= 0.5 = 
changed a variable name to, obscure it to avoid conflicts.

= 0.4 =
minor bug fix

= 0.3 =
Uses filename when link text is not specified. i.e. `[file-proxy id='attachment_id']`

= 0.2 =
Adds some Variable sanitation

== Upgrade Notice ==

= 0.6 =
New features, Admin options, link button, direct in media manager.

= 0.5 = 
changed a variable name to, obscure it to avoid conflicts.

= 0.4 =
minor bug fix

= 0.3 =
Default link text replaced by filename.

= 0.2 =
Adds some Variable sanitation, and sql prep to harden plugin security
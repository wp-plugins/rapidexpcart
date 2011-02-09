=== RapidExpCart ===
Contributors: rapidexp
Donate link: http://cart.rapidexp.com/resume/
Tags: shopping,cart,EC
Requires at least: 2.0.2
Tested up to: 3.0.4
Stable tag: 1.0

RapidExpCart is a shopping cart system composed of two programs that consist of a WordPress plugin and a main program.

== Description ==

RapidExpCart is a shopping cart system composed of two programs that consist of a WordPress plugin and a main program.
So, you should download the main proguram from [rapidexp.com](http://cart.rapidexp.com/resume/downloads/) besides this plugin.

The main proguram needs the following system requirements.

* php5 + mysqli (module mode is recommended)
* MySql5 (InnoDB is recommended)
* mod_write

And then, ... it is only Japanese at present :-(


== Installation ==

1. Upload `rapidexpcart.php` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Set the installed path of the main proguram to the 'Optons > RapidExpCart' menu in WordPress.
4. Memorize the permalink of the '\_RAPIDEXPCART_TEMPLATE_TITME\_' in the 'Pages' menu in WordPress.
5. Upload the main proguram to the directory specified at 3.
6. Permit writing directory `data`, `logs` and `session`.
7. Create `config.inc.php` referring to `config.sample.inc.php` in the `/php/share/` directory of the main proguram.
8. Copy the memorized permalink to `config.inc.php`.
9. Execute the `/php/admin/install.php` of the main proguram in the address column of a browser.

RapidExpCart has a product list and product pages independent of the blog.
If you wish to display product informations in the blog content, you may describe the following markups.

* [rapidexpcart button=_sku_] replaces the cart button specified by SKU.

== Frequently Asked Questions ==

= Why is the main program besides the plugin necessary? =

RapidExpCart was made based on real EC system of the custom order.
An independent program is more convenient to customize after the system is installed.

= How should I do to obtain "Professional version"? =

"Professional version" is scheduled to be sold for a fee when it become a stability version.
If you wish to obtain it even a little early, please contact me.
I wait for contact from several supporters or contributors.

== Screenshots ==

1. The shopping cart in WordPress.

== Changelog ==

= 1.0 =
* Add a meta tag of robots for the template page.
* Add markups of the event page and the download page.
* Don not display invalid markup.

= 0.10 =
* The first version in WordPress.org

== Upgrade Notice ==

= 1.0 =
This plugin version 1.0 is for the main program version 1.0 later. But you may use a combination of different versions of each other.


=== HikaShop for Beaver Builder ===
Contributors: hikarisoftware
Tags: hikashop, beaver builder, ecommerce, shop, product
Requires at least: 6.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Design your HikaShop store with Beaver Builder: product listings, product page parts, cart, filters, and product pages built from a layout.

== Description ==

HikaShop for Beaver Builder adds a HikaShop group to the Beaver Builder module panel. It works with the free Beaver Builder Lite as well as the paid editions.

= Listings =

* **HikaShop Products**: the products of a category, products chosen by hand (with a search of your catalog, in the order you choose), products on sale, the products the visitor viewed recently, or the products related to the product being viewed. Choose the order, the number of products, the columns, the product layout, and whether to show the price, the add to cart button, the quantity field, the wishlist button, the rating and the badges.
* **HikaShop Categories**: the subcategories of a category, with the number of products in each, empty categories hidden if you want.

= Product page =

Create a layout in Settings > HikaShop for Beaver Builder, design it with the product modules, and pick it as the product page layout: every HikaShop product page is then shown with it. The product modules show the product being viewed:

* Product title, images, price, add to cart (with the quantity field), description
* Variants (the characteristic selectors), options, custom fields
* Specifications, dimensions and weight, bundled products, files, rating, sharing buttons

In the builder, the latest product of your shop stands in so you can see the result while you design. The page title, meta description, canonical URL and structured data of the product still come from HikaShop.

= Cart and filters =

* **HikaShop Cart** and **HikaShop Wishlist**: the cart and wishlist summaries, with the full set of HikaShop module settings.
* **HikaShop Currency Switcher**
* **HikaShop Filters**: the filters of HikaShop for your product listings.

Prices, taxes, discounts, stock, access rights and the cart all follow your HikaShop configuration: the modules render with HikaShop's own layouts, so your theme overrides of HikaShop layouts keep working.

= Requirements =

This plugin needs HikaShop 6.8.2 or newer on WordPress (a package downloaded since 12 November 2026: reinstall it over an older 6.8.2), and Beaver Builder 2.9 or newer (Beaver Builder Lite is enough). HikaShop is not hosted on WordPress.org: its free Starter edition and the Essential and Business editions are available on [hikashop.com](https://www.hikashop.com/download).

== Installation ==

1. Install and activate HikaShop and Beaver Builder (or Beaver Builder Lite).
2. Install and activate HikaShop for Beaver Builder from Plugins > Add New, or upload the zip file.
3. Open a page with Beaver Builder: the modules are in the HikaShop group of the module panel.
4. To design the product pages, go to Settings > HikaShop for Beaver Builder, add a layout, open it with Beaver Builder and place the product modules in it, publish it, then choose it as the product page layout.

== Frequently Asked Questions ==

= Does it need the paid Beaver Builder? =

No. Every module and the product page layout work with the free Beaver Builder Lite. With the paid Beaver Builder, its saved layouts can also be chosen as the product page layout.

= Why is a product missing from a listing? =

The listings follow the HikaShop configuration: an unpublished product, a product the visitor's user group cannot see, or a product out of stock while "Display out of stock products" is off in HikaShop are left out, as in your shop pages.

= Where are the settings of the cart module? =

Open the module settings and click "Edit the HikaShop settings": the HikaShop cart module settings open in a new window, and the preview refreshes when you close it.

= Can visitors open a layout on its own? =

No. A layout's own address is only open to the people who can edit it, and search engines are told not to index it.

= Can I change the HTML of the modules? =

Yes, with HikaShop's layout overrides: copy the HikaShop layout file into the hikashop folder of your theme, as you would for the HikaShop pages.

== Screenshots ==

1. A product listing and its settings in Beaver Builder.
2. Products chosen by hand, with a search of the HikaShop catalog.
3. A product page layout designed with the product modules of the HikaShop group.
4. A HikaShop product page shown with that layout.
5. A shop page built with the category and product listing modules.
6. The product page layout setting.

== Changelog ==

= 1.0.0 =
* First release: product and category listings, the product page parts, the product page layout, the cart, wishlist, currency and filter modules.

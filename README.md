=== RSVP Manager ===
Contributors: codeverse93
Donate link: https://www.paypal.com/donate/?hosted_button_id=AQ578DHK9YAZN
Tags: rsvp, wedding, attendees, guests, event-manager
Requires at least: 4.7.19
Tested up to: 6.6.1
Version: 1.1
Stable tag: 1.1
Requires PHP: 7.4.19
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Enhance your event management with RSVP tracking, attendee relationships and customizable labels. Perfect for managing guest lists seamlessly.

== Description ==

A simple and lightweight plugin for managing private event RSVPs effortlessly. Ideal for events with a user-friendly interface and minimal setup.

In the Wordpress admin console you have the following features:

* View the event you want the attendees to rsvp.
* Edit the details of the event, including name, start & end date time for rsvp.
* View the list of attendees.

For attendees you have the following options:

* View the details of the attendee.
* View the RSVP status, RSVP date and custom message.
* Add a new attendee.
* Add related attendees for an attendee.
* Reciprocal attendee associations.
* Update an attendee.
* Delete an attendee.
* Bulk delete attendees.

For the UI, you have options to customize the following labels:

* The message displayed when the RSVP is not open.
* The message displayed when the RSVP is already closed.
* The message displayed above the search form.
* "Firt name" label.
* "Last name" label.
* Search button leabel.
* Error displayed when no first and/or last name is provided.
* RSVP button label.
* The message displayed when the user already did the RSVP.
* Positive answer label for already RSVP.
* Negative answer label for already RSVP.
* Welcome label in the RSVP form.
* RSVP question.
* RSVP positive answer.
* RSVP nevative answer.
* Custom message label.
* Related attendees info message.
* Related attendees RSVP question.
* Confirmation button label in the RSVP form.
* RSVP positive confirmation message.
* RSVP negative confirmation message. 

== Installation ==

1. Upload the `rsvp-manager` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin from the 'Plugins' menu in WordPress admin console.
1. Edit the event, add your attendees and configure it as you wish. 
1. To display the rsvp form in your website, add the short code `[event_rsvp]` in the page.

== Frequently Asked Questions ==

= How do I configure the RSVP settings for my event? =

To configure RSVP settings, go to the RSVP menu in the Wordpress admin consle. There you can edit the event details, define the RSVP open and close dates, customize labels, and manage the attendees.

= Can I integrate this plugin with Elementor? =

Yes, you can integrate the plugin with Elementor by using the shortcode provided in the plugin settings. Simply add the shortcode to an Elementor widget, and the RSVP form will be displayed on your page.

== Screenshots ==

1. The event short code integrated in a sample website created with Elementor.
2. RSVP search functionality which allows the user to find themselves and start the RSVP process.
3. RSVP form allowing the user to submit the answer.
4. RSVP confirmation.
5. Event page in Wordpress admin console.
6. Page to manage the attendee list of the event.
7. The event details page which can be edited.
8. Page which provides the posibility to customize the texts displayed in the short code. 

== Changelog ==

= Version 1.1 =

Release Date: 17.10.2024

#### New Features
- **Reciprocal Attendee Associations**:
    - Added a new option to enable reciprocal (mutual) associations between attendees. When attendee X is associated with attendee Y, Y is automatically associated with X.
    - Introduced the "Reciprocal Association" checkbox in the attendee creation/edit screen to control this behavior.

= Version 1.0 =

Release Date: 07.10.2024

* Initial release of the plugin with core functionality for managing RSVPs for a private event.

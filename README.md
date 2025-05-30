# My Event Manager Plugin

## Overview

The **My Event Manager** plugin is a user-friendly WordPress solution designed to streamline the creation, management, and display of events on any WordPress website. Built with modern WordPress development best practices and Object-Oriented Programming (OOP) principles.

From detailed event information to seamless frontend display via a shortcode, and an improved administrative experience with custom columns and sorting, this plugin demonstrates a solid understanding of WordPress core functionalities and extension.

## Features

This plugin delivers the following key functionalities:

* **Custom Post Type (CPT) for Events:**
    * Registers a dedicated "Event" post type, distinct from standard posts or pages, to manage event-specific content.
    * Supports standard WordPress features like title and rich content editor for each event.

* **Custom Meta Boxes & Event Details:**
    * Adds a custom meta box on the Event editing screen to capture essential event information.
    * Fields include:
        * **Event Date:** With an intuitive jQuery UI date picker for easy selection.
        * **Event Time:** For precise scheduling.
        * **Location:** Specify the physical venue or "Online".
        * **Speaker(s):** List key speakers or presenters.
        * **Short Description:** A concise summary for event listings.

* **Custom Taxonomy for Event Categorization:**
    * Registers an "Event Category" taxonomy (hierarchical, like standard categories) to organize events into relevant groups (e.g., "Webinars," "Conferences," "Workshops").

* **Frontend Event Listing Shortcode:**
    * Provides a versatile `[my_events]` shortcode to display a list of upcoming events on any WordPress page, post, or widget area.
    * **Filtering by Category:** Use `[my_events category="your-category-slug"]` to display events from a specific category.
    * **Limiting Events:** Control the number of events shown with `[my_events count="5"]`.
    * **Combinable Attributes:** e.g., `[my_events category="online-webinars" count="3"]`.
    * Displays event title, date, time, location, speakers, short description, and featured image (if available).
    * Automatically filters to show only events from the current date onwards.

* **Custom Single Event Page Template:**
    * Includes a default `single-event.php` template located in the `templates` folder.
    * This ensures a consistent and dedicated display for individual event pages, which can be easily overridden by the active theme for seamless integration.

* **Enhanced WordPress Admin Experience:**
    * **Custom List Table Columns:** Adds informative columns to the "All Events" admin screen for "Date," "Time," "Location," "Speaker(s)," and "Category," providing quick overviews.
    * **Sortable Columns:** Enables sorting of the "All Events" list by "Event Date" for easy chronological management.

* **Internationalization (i18n) Ready:**
    * All user-facing strings are wrapped with WordPress's translation functions, making the plugin ready for translation into multiple languages.

* **Robust Development Practices:**
    * Implemented with Object-Oriented Programming (OOP) for modularity, maintainability, and scalability.
    * Utilizes WordPress activation/deactivation hooks for proper setup and cleanup.
    * Includes nonce security checks and capability checks for secure data handling.
    * Follows WordPress coding standards.

## Installation

1.  **Download:** Clone this repository or download the plugin ZIP file.
2.  **Upload:**
    * If you downloaded the ZIP, go to `Plugins > Add New > Upload Plugin` in your WordPress admin, choose the ZIP file, and click "Install Now."
    * If you cloned/downloaded the folder, upload the `my-event-manager` folder to your WordPress installation's `wp-content/plugins/` directory.
3.  **Activate:** Navigate to `Plugins` in your WordPress admin, locate "My Event Manager," and click "Activate."
4.  **Flush Rewrite Rules:** After activation, it's crucial to go to `Settings > Permalinks` in your WordPress admin and simply click "Save Changes" (you don't need to alter any settings). This ensures that the custom post type URLs (permalinks) for your events work correctly.

## Usage

### Creating and Managing Events

1.  From your WordPress admin dashboard, navigate to **Events > Add New**.
2.  Enter the **Event Title** and a comprehensive description in the main content editor.
3.  Fill in the **Event Details** using the custom fields provided in the meta box below the editor:
    * **Event Date:** Select the date using the integrated date picker.
    * **Event Time:** Enter the time (e.g., `10:00 AM`, `14:30`).
    * **Location:** Provide the event's location (e.g., "Online via Zoom," "Main Auditorium, City Convention Center").
    * **Speaker(s):** List the names of speakers (e.g., "John Doe, Jane Smith").
    * **Short Description:** A brief, compelling summary of the event.
4.  Optionally, set a **Featured Image** for the event.
5.  Assign appropriate **Event Categories** from the "Event Categories" meta box on the right sidebar.
6.  Click **Publish** (or "Update") to save your event.

### Displaying Events on Your Website

Use the `[my_events]` shortcode on any WordPress page, post, or even in a text widget to display a dynamic list of your upcoming events.

**Examples:**

* **Display all upcoming events:**
    ```
    [my_events]
    ```
* **Show only events from the 'conference' category:**
    ```
    [my_events category="conference"]
    ```
* **Display the next 5 upcoming workshops:**
    ```
    [my_events category="workshop" count="5"]
    ```

## Technologies Used & Skills Demonstrated

This project showcases proficiency in a range of web development technologies and WordPress-specific skills:

* **PHP:** The core programming language for plugin development, logic, and WordPress API interaction.
* **Object-Oriented Programming (OOP):** Structured plugin architecture for maintainability, reusability, and professional development.
* **WordPress Core APIs:** Extensive use of `register_post_type()`, `register_taxonomy()`, `add_meta_box()`, `add_shortcode()`, `WP_Query`, `wp_enqueue_script()`, `wp_enqueue_style()`, and various filters/actions for deep integration.
* **Git & GitHub:** Version control management for collaborative development and project showcasing.
* **HTML & CSS:** Structuring plugin output and basic styling for both frontend and admin areas.
* **JavaScript (jQuery UI Datepicker):** Enhancing user experience in the WordPress admin with interactive elements.
* **Internationalization (i18n):** Adherence to best practices for making plugins translatable into different languages.
* **Clean Code & Documentation:** Emphasis on readable code, meaningful comments, and comprehensive README documentation.
* **Security Best Practices:** Implementation of nonces and capability checks to secure data processing.

## Contribution

Contributions are welcome! If you find a bug, have an idea for a new feature, or want to improve the code, please feel free to:

1.  Fork the repository.
2.  Create your feature branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

## License

This plugin is open-source software licensed under the GPL2.

---

Feel free to customize any section to better reflect your personal style or any additional nuances of your plugin!

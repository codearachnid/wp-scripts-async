WordPress Scripts Async
==================

### NOTE: THIS PLUGIN IS NOT FUNCTIONAL YET ###

Please use at your own risk and provide pull requests for any improvements.

### Would you like to contribute?!

I do not have a lot of time to devote to this project and would love your help! Use pull request or contact me @codearachnid and I will add you as a contributor.

### Purpose ###

Extending wp_enqueue_script to use RequireJS for asynchronous loading. Use this plugin to dynamically load your JavaScripts through the UI without hardcoding script references.

It creates a site configuration for all your JavaScript assets and

This project is an idea for supporting wp_enqueue_script to the frontend sparked By Aaron Jorbin @aaronjorbin at #wcmia 2014.

Note currently when active this plugin will create the file `/wp-content/require.config.js` when any page is hit in the admin. In a future commit an admin process will be constructed to manually request and generate the configuration files required.

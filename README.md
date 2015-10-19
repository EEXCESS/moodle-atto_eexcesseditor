copyright:   bit media e-solutions GmbH, gerhard.doppler@bitmedia.cc  
license:     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

Use this link to download the installable plugin .zip file: https://github.com/EEXCESS/MoodleAttoEditorPlugin/raw/master/docs/MoodleAttoEditorPlugin.zip

# Moodle EEXCESS plugin
## Short description
It is a tool that provides you recommendations from cultural and scientific databases in a Moodle environment (http://www.moodle.org) .
Technically it consists of two plugins:

* Moodle Server plugin  https://purl.org/eexcess/components/moodle-server-plugin
* Moodle Atto Editor plugin https://purl.org/eexcess/components/moodle-atto-editor-plugin

As it mainly makes sense to install both plugins, this documentation contains installation and usage instructions for both.

## Installation instruction
### Moodle Server plugin
Necessary steps:

1. Download plugin Repository with plugin.
2. Go to Site administration/Plugins/Install plugins. Click the button Chose a file and chose folder with plugin. After, click the button install plugin from the ZIP file.
3. Click the button install plugin.
4. Click the button Upgrade Moodle database now.
5. Click continue.
6. Change the settings if it is necessary and click the button Save changes.
7. Installing EEXCESS plugin finished.

After the installation, an EEXCESS button ![button_eexcess](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/button_eexcess.png) (for displaying the recommendations) will appear on the top in the center. 
User settings for changing the preferred citation style will be added to the navigation block.

### Moodle Atto Editor plugin
You also need to install the excess atto editor plugin, for inserting citations in atto text editor.

Necessary steps:

1. Download plugin Repository with plugin.
2. Go to Site administration/Plugins/Install plugins. Click the button Chose a file and chose folder with plugin. After, click the button install plugin from the ZIP file.
3. Click the button install plugin.
4. Click the button Upgrade Moodle database now.
5. Click continue.
6. Installing EEXCESS plugin finished.

After installation, button EEXCESS (for displaying the recommendations) will appear in toolbar atto.

![atto_toolbar_menu](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/atto_toolbar_menu.png)

## Usage instruction
### Content consumption
For search: you can select any text on any page in the Moodle site (except inside a text editor). The EEXESS button gets animated while the system is looking for recommendations. As soon as recommendations are found the animation stops and the number of recommendations is displayed.![button_eexcess_with_results](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/button_eexcess_with_results.png) Clicking the button opens the recommendation display.
### Content creation
When you are writing a text (e.g. a forum post) the system gets active as soon as you finish a paragraph (by pressiong Return). The EEXCESS button gets animated while the system is looking for recommendations. As soon as recommendations are found the animation stops and the number of recommendations is displayed.
Clicking the EEXCESS button in the Atto Editor toolbar or the EEXCESS button on the top of the page opens the recommendation display. 
Now the recommendation display contains 3 elements to enrich your text in the editor:

![dashboard_screenshot](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/dashboard_screen.png)

These elements serve to:

* Embed an image 
 
  ![button_embed_image](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/embed_image.png)
* Embed a citation (according to the selected preferred citation style) (image Embed a citation)

  ![button_embed_citation](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/embed_citation.png)
* Embed a screenshot of the visualization (not available for all visualization types)
 
  ![button_screenshot](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/screenshot.png)

## Settings
### Administrator settings:
Go to Site administration/Plugins/Local plugins/EEXCESS settings has 2 options:

* Change EEXCESS citation.
* Change base url for recommendations.

![admin_settings](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/EEXCESS_admin_settings.png)

### User settings:
In navigation block go to EEXCESS settings/Citation settings to change the citation style.

![user_settings](https://raw.githubusercontent.com/EEXCESS/MoodleAttoEditorPlugin/master/docs/EEXCESS_user_settings.png)

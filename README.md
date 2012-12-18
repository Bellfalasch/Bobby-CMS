Bobby CMS
================

A Superlight Admin
----------------

Bobby CMS is a development name, since more fitting names such as Skeleton CMS, Barebone CMS, Skinny CMS, where all taken. This CMS is intended only for PHP-developers so that they fast and easy can set up an admin on top of their MySQL-database.

This is meant to be used on top of my other PHP-templates. It's super easy and small. You just set up your own SQL's for the admin in _database.php and then you're good to go. The footer and header's are used on each file. The header detects which file you're browsing and updates the menu according to that.

Each php-file is it's own admin function. Basically they each display an empty form to the left with information, and to the right a list of every data that already are in the database. Click a link there to load this data in the form and perform a database update, or just fill in an empty form to perform a database insert. This is built to be easy up and go but with the ability for everything to be easily tailormade, hence sparingly with automation of things.

Check the included examples for best practice of how to set your admin files up.

Just drop the folder `_admin` in your project and extract these files in it. Of course you can edit the folder name after your hearts content, but at the moment you have to manually update all the files to the new path.


Updates:
----------------

### 0.9.2.1
Just cleaned up the code a lot. Translated some comments, moved a few things around, re-organized a few functions, explained them better, and small things like that. Nothing that really makes any difference. Only "big" thing is that error and debug-handling now uses variables instead of sessions. No need for them to be stored in sessions really.

### 0.9.2
You should now be able to rename the "_admin"-folder AS WELL as the root-folder, and move the files between live and localhost without any code change! I use a couple of new variables - SYS_root and SYS_folder - for this, where SYS_folder is known from before. Added section to this guide about our variables.

### 0.9.1
Introducing the file `_global.php` full of functions and setup up things needed. Things now work a bit different with PAGE_form in that we explicitly call the function `validateForm()` to validate the form. Do this in the ISPOST-check before extracting and working with the posted data heading for your database.

### 0.9
A major clean-up of files and their code. The most prominent new things are the admin functions for file upload and image upload (as usual as files and not functions), new easier menu-handling, and support for form-posting without the whole PAGE_form. So you can set up your own forms, just drop the code, and get it to work by the side of the smarter PAGE_form to get up and running faster. The menu-update is explained in more detail futher down.

### 0.8.6
Easier add fields to your form with a smoother syntax of setting them all up. The output of the fields are also now just a single function-call. And a bit of other clean ups in the code and the comments.

### 0.8.5
Field Type "folder" (also supports setting size) introduced! Uses the all new settings-variable which in a CSS-like manner easily configures more advanced fields. Supports settings for which folder to list (in the select/options-box), select file types (if set, exclude every file format not listed), and set text for "select no file" if that is to be used (added in the end of the folderlist. The folder-type automatically hides subfolders.

### 0.8
This update makes for an complete overhaul of how forms are set up and validated. You now define your settings in an associative array for each form field you wanna have on each page (se example1.php). This will then generate the COMPLETE form, AND it's validation. Nothing short of epic! The UPDATE/INSERT to the database is still handled manually ... automation for this set to come for 1.0.

### 0.5
Changed the file-structure a bit and included default files needed to run this admin - `../inc/*` - and took the admin to it's own repository on GitHub. The admin can now be "installed" and runned without any of my other templates in place beforehand.

### 0.3
I have set it up on my GitHub for "Super-Simple-Web-Templates", extremly basic set up, just the files needed and kind of bad/error proune examples.

### 0.1
On the sixth day, Bobby created this admin to ease the burden of setting up a full Wordpress for clients who only need super easy administration.


The future:
----------------
* JS-validation onsubmit via jQuery
* Automatic INSERT/UPDATE-generation for MySQL
* More field types, like checkbox, radio, hidden, map, dropdown
* A page_post setting for label append, submit button title, admin level, etc
* Combine min-max to one field (something like 'length:2-255')


Dependencies:
----------------
You must use this admin on a project based on one of my other simple PHP templates, at minimum these files:
**../inc/database.php** - Needed for accessing the database (passwords etc), and for processing all the SQL's in the admin's own "_database.php".
**../inc/functions.php** - The admin uses some basic functions added in this file, also used by my other "PHP-templates".

This admin is based on **Bootstrap** by Twitter (included) and **TinyMCE** (included).

It should be fairly easy to strip away all of these dependencies, if you're bold enough =P


Basic structure:
----------------

### _header.php:
The power file =) This file contains the two functions that will 1. Generate your form HTML, and 2. Ensure PHP-validation of your form when a user submits it. They get this from the functions called generateField, and validateField. Both these functions takes this array as input and from that, depending on each function, generates the correct html and validation based on what you have written.

### File structure:
* _database.php - Just SQL, this file uses the main folders database-file and all of it's functions.
* _header.php - See above.
* _footer.php - The last few bits of html etc for the admin.
* assets/admin.css - The menu is taken from Bootstrap web, also other styles that is needed for the admin is added in this file.
* assets/bootstrap.min.css - The projects uses Bootstrap 2.0, check their site for more info: http://twitter.github.com/bootstrap/scaffolding.html

### Files:
* index.php - frontpage, validates your login, and also handles log out
* users.php - create, delete, and edit users

### Examples:
Example files are added just so you can see how I have set up different files in my live projects. You can duplicate them, modify them, or just delete them. They are completly independable:
* examples.php - The intro/start page for our examples.
* examples-example1.php - This example use the 0.8-new style of setting up a form with validation.
* examples-upload.php - Easily upload any file to the admin for later use in any form. No database connection!
* examples-images.php - Easily upload an file and scale it to many different versions for use in any form. No database connection!


Our menu system
----------------
Our menu system is far from done. You do need to edit the `_header.php` and add the `li a` for each link. But I have at this moment at least eased the burden a bit by writing the `active`-class on the specific page you're actually visiting. It depends on two functions - at the moment placed just before the menu.

**isActiveOn** - This checks if the current file is the same as the menu you're printing, or if it is the child of it (using the `parent.php` + `parent-child.php` naming convention). This function is the main function for the menu. The submenu use this to show the correct submenu depending on the selected item in the main menu.

**flagAsActiveOn** - This just outputs the active-class if the previous function returns true.

In the future I will try and make the generation of the menu even easier.


Setting up your form fields:
----------------
So, firstly you should set up and define each field to be used (at the top of each page, yes) and then in validation
call the validation-function, and at the output-stage call the generate-function. Easy as pie =)

First example-field, the Title for a post:

    addField( array(
        "label" => "Title:",    // The label displayed to the user infront of the field
        "id" => "Title",        // id for the field itself (for JS-hooks), always prepended by "input". This will also be used for the "name" attribute.
        "type" => "text(3)",    // Type of field to generate, currently only "text,area,wysiwyg" is supported.
        
        // A description of what this field is for and how to fill it in, keywords MIN, MAX, and LABEL can be used to extract numbers from the validation setup of this field.
        "description" => "Write a good descriptive title for this post in between [MIN] and [MAX] characters.",
        
        "min" => "2",       // Minimum chars. If empty then this field is allow to not be set.
        "max" => "45",      // Maximum chars. If empty you can write as much text as you'd want. On text-fields the maxlength-attribute is set.
        "null" => false,    // If true this field be transformed to null if it's empty, instead of being an empty string. This is for later database-saving.
    
        // The errors-array, this array controls validation. If one type of validation is set the code WILL validate for this when you try and save, and it WILL stop you from saving the form.
        "errors" => array(
            "min" => "Please keep number of character's on at least [MIN].", // If this string is NOT set but you set the "min"-setting, we will not validate. If this is set to 1, we treat this field as a subject for "not empty"-validation.
            "max" => "Please keep number of character's to [MAX] at most.",
            "exact" => "Please keep number of character's to exactly [MIN].", // If text is in this validation-form, only the MIN-validation will be used for validation even if the MAX-value is set.
            "numeric" => "This field can only contain numeric values."
        )
    ) );

To get validation of an error, you must write an error message in the "errors"-array, AND in some times also the "min" and/or "max" setting of the array, see example above for more details.

As you might have noticed in the example, we use a few keywords here and there. `[MIN]`, `[MAX]`, and `[LABEL]` will get replace to the equivalent setting of this field. However, this only work on description and the error messages.

After setting this array up, you have to - at the moment - append this to the array `$PAGE_form` for the whole thing to work.

Just so that you get the hang of it I'm gonna define another field for this site with a bit different settings (and hardly any comments).
Basically I want a field, that you don't have to fill in, at most 45 characters, to represent an alternative title.

    addField( array(
        "label" => "Alternative title:",
        "id" => "Alternative",
        "type" => "area(5*5)",
        "description" => "Teh LOL ...",
        "max" => "100",
        "null" => true,
        "errors" => array(
                        "max" => "Please keep number of character's to [MAX] at most.",
                    )
    ) );

As you can see in this example we don't have to assign each item in the array, this is especially clear in the "errors"-array. Just completly delete a setting to not take it into consideration for validation/generation and it will work anyway.

Example to generate a wysiwyg-textarea (powered by TinyMCE).

    addField( array(
        "label" => "Wysiwyg:",
        "id" => "Wysiwyg",
        "type" => "wysiwyg(5*5)",   // The size here does not do anything exept sizing the textarea IF javascript is not active. All wysiwyg-fields are at this time all at a fixed size.
        "description" => "Write a novell!",
        "min" => "1",
        "max" => "10240",
        "null" => true,
        "errors" => array(
                        "min" => "Please write at least something here ='(",
                        "max" => "Please keep number of character's to [MAX] at most."
                    )
    ) );

Frequently used - example of an e-mail field =)

    addField( array(
        "label" => "Mail:",
        "id" => "Mail",
        "type" => "text(5)",
        "min" => "1",
        "max" => "255",
        "errors" => array(
                        "min" => "Please submit your e-mail address (we hate spam too and will not flood your mailbox).",
                        "max" => "Please keep number of character's to [MAX] at most.",
                        "mail" => "Please use a valid e-mail, [CONTENT] is not valid."
                    )
    ) );

Example of absolute minimal amount of setup for a field. These are the only settings needed to get your form jumpstarted!

    addField( array(
        "label" => "Minimal:",
        "type" => "text"
    ) );

### New in 0.8.6: easier setup and output!

Instead of the former way of adding each field to the array manually for output we now when defining for instance $fieldMail send it to `addField()` which will populate the `PAGE_form` array.

Old way:

    $fieldMinimal = array(
        "label" => "Minimal:",
        "type" => "text"
    );

    $PAGE_form = array(
                    $fieldTitle,
                    $fieldAlternative,
                    $fieldWysiwyg,
                    $fieldMail,
                    $fieldMinimal
                );

New way:

    addField( array(
        "label" => "Minimal:",
        "type" => "text"
    ) );

    // Done! Adding the new field to $PAGE_form is done automatically by the addField-call around your field settings ^^

After this you can call the generateField-function for each array in the variable `$PAGE_form`. This will be made better in the future.
The validation is automatically generated when you post the page so that you do not have to worry about =)

Old way:

    foreach ($PAGE_form as $field) {
        generateField($field);
    }

New way (the entire `$PAGE_form` array will be output):

    outputFormFields();

### New in 0.8.5: type "folder"

This is how you can use the all new folder-type.

    addField( array(
        "label" => "Image:",
        "type" => "folder(3)",
        "settings" => "formats:jpg,jpeg,png,gif; unselectable:Use no image; folder:uploads/;"
    ) );

As you can see, you can set the size of the field as usual. The big new thing is the now implemented "settings" value. As of now only the folder-type use this, and the only supported settings are "formats", "unselectable", and "folder". If formats is left empty, or not included, we will list every file from selected folder (but never subfolders). Unselectable is used to in the end of the select-box add an empty unselectable box tightly followed by another box with the text you enter here. Value on this field will be "" (empty string), so you cannot validate "min" => 1 on that box.

And lastly we have the folder-setting, which needs to be set for the rendering to work. Always end this with /, and this will start in the "_admin"-folder.

All settings are listed in CSS-style so that you have "setting:value;".


Our variables:
----------------
Names not preceding by the `$` are declared as such (constants). These are all set up in `_global.php` so they should be used without problem in any of your own template files. All these are set up automatically, except `$PAGE_name` and `$PAGE_title` that are set by yourself on top of each template file.

* `ISPOST`  - Detects if the page is being posted or not.
* `DEV_ENV` - Show the debugger-menu in the footer, if true (default).

* `$SYS_domain`   - Get the domainname, like "localhost" or "nxt.no".
* `$SYS_root`     - The root folder which the main project (not the admin) resides in.
* `$SYS_folder`   - The folder which the admin itself is placed in (you can thus rename it to whatever, whenever!).
* `$SYS_script`   - The name of current php-file without the file ending.
* `$SYS_incroot`  - Just used to get the correct includes from the main project (used twice).
* `$SYS_adminlvl` - Get current admins level, defaults to 0.

* `$PAGE_form`  - Our main array for smart form-handling, see previous point.
* `$PAGE_name`  - A name for the page/function, used here and there.
* `$PAGE_title` - The title to be used in the html title tag for this page.
* `$PAGE_dbid`  - If the QueryString "id" is present, this variable will store it. If not, it will store "-1". It's used for easier handling of fetching selected data from the database, and later saving it back on post.
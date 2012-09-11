Superlight Admin
================

This is meant to be used on top of my other PHP-templates. It's super easy and small. You just set up your own SQL's for the admin in
_database.php and then you're good to go. The footer and header's are used on each file. The header detects which file you're surfing
and updates the menu according to that.

Each php-file is it's own admin function. Basically they display an empty form to the left with information, and to the right a list of
every data that already are in the database. Click a link there to load this data in the form and perform a database update, or just fill
in an empty form to perform a database insert. This is built to be easy up and go but with the ability for everything to be easily
tailormade, hence no automation of things.

Check the included examples for best practice of how to set your admin files up.

Just drop the folder `_admin` in your project and extract these files in it. Of course you can edit the folder name after your hearts
content, but at the moment you have to manually update all the files to the new path.


Updates:
----------------

### 0.8
This update makes for an complete overhaul of how forms are set up and validated. You now define your settings in an associative array for each
form field you wanna have on each page (se example1.php). This will then generate the COMPLETE form, AND it's validation. Nothing short of epic!
The UPDATE/INSERT to the database is still handled manually ... automation set to come for 1.0.

### 0.5
Changed the file-structure a bit and included default files needed to run this admin - `../inc/*` - and took the admin to it's own repository on
GitHub. The admin can now be "installed" and runned without any of my other templates in place beforehand.

### 0.3
I have set it up on my GitHub for "Super-Simple-Web-Templates", extremly basic set up, just the files needed and kind of bad/error proune examples.

### 0.1
On the sixth day, Bobby created this admin to ease the burden of setting up a full Wordpress for clients who only need super easy administration.


The future:
----------------
* JS-validation onsubmit via jQuery
* Automatic INSET/UPDATE-generation for MySQL
* More field types, like checkbox, radio, hidden, map, dropdown
* A page_post setting for label append, submit button title, admin level, etc
* Autogenerate the $PAGE_form array (at the moment this is done manually ...)
* Combine min-max to one field (something like length:2-255)


Dependencies:
----------------
You must use this admin on a project based on one of my other simple PHP templates, at minimum these files:

* **../inc/database.php** - Needed for accessing the database (password etc), and processing SQL's in the admin's own "_database.php".
* **../inc/functions.php** - The admin uses some basic functions added in this file, used by my other "PHP-templates".

This admin is based on **Bootstrap** by Twitter (included) and **TinyMCE** (included).


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
* index.php - validates your login, and also handles log out
* users.php - create, delete, and edit users

### Examples:
Example files just so you can see how I have set up different files in my live projects:
* example1.php - THE ONLY EXAMPLE WITH THE NEW 0.8-STYLE OF SETTING UP FORMS!!!
* campaign.php - OLD SETUP!!!
* datespan.php - OLD SETUP!!!
* discounts.php - OLD SETUP!!!
* overview.php - OLD SETUP!!!


Setting up your form fields:
----------------
So, firstly you should set up and define each field to be used (at the top of each page, yes) and then in validation
call the validation-function, and at the output-stage call the generate-function. Easy as pie =)

First example-field, the Title for a post:

    $fieldTitle = array(
    	"label" => "Title:",	// The label displayed to the user infront of the field
    	"id" => "Title",		// id for the field itself (for JS-hooks), always prepended by "input". This will also be used for the "name" attribute.
    	"type" => "text(3)",	// Type of field to generate, currently only "text,area,wysiwyg" is supported.
    	
    	// A description of what this field is for and how to fill it in, keywords MIN, MAX, and LABEL can be used to extract numbers from the validation setup of this field.
    	"description" => "Write a good descriptive title for this post in between [MIN] and [MAX] characters.",
    	
    	"min" => "2",		// Minimum chars. If empty then this field is allow to not be set.
    	"max" => "45",		// Maximum chars. If empty you can write as much text as you'd want. On text-fields the maxlength-attribute is set.
    	"null" => false,	// If true this field be transformed to null if it's empty, instead of being an empty string. This is for later database-saving.
    
    	// The errors-array, this array controls validation. If one type of validation is set the code WILL validate for this when you try and save, and it WILL stop you from saving the form.
    	"errors" => array(
    		"min" => "Please keep number of character's on at least [MIN].", // Use keyword MIN to extract this value from the setup. If this string is NOT set but you set the "min"-setting, we will not validate.
    		"max" => "Please keep number of character's to [MAX] at most.",
    		"exact" => "Please keep number of character's to exactly [MIN].", // If text is in this validation-form, only the MIN-validation will be used for validation even if the MAX-value is set.
    		"empty" => "Please write something in this field [LABEL].",
    		"numeric" => "This field can only contain numeric values."
    	)
    );

After setting this array up you at the moment need to append this to the array `$PAGE_form` for the whole thing to work.

As you might have noticed in the example, we use a few keywords here and there. `[MIN]`, `[MAX]`, and `[LABEL]` will get replace to the equivalent setting of this field. However, this only work on description and all of the error messages.

To get validation of an error, you must write an error message in the "errors"-array, AND in some times also the "min" and/or "max" setting of the array, see example above for more details.

Just so that you get the hang of it I'm gonna define another field for this site with a bit different settings (and hardly any comments).
Basically I want a field, that you don't have to fill in, at most 45 characters, to represent an alternative title.

    $fieldAlternative = array(
    	"label" => "Alternative title:",
    	"id" => "Alternative",
    	"type" => "area(5*5)",
    	"description" => "Teh LOL ...",
    	"max" => "100",
    	"null" => true,
    	"errors" => array(
    					"max" => "Please keep number of character's to [MAX] at most.",
    				)
    );

As you can see in this example we don't have to assign each item in the array, especially clear in the "errors"-array. Just completly delete a setting to not take it into consideration and it will work anyway.

Example to generate a wysiwyg-textarea (supported by TinyMCE).

    $fieldWysiwyg = array(
    	"label" => "Wysiwyg:",
    	"id" => "Wysiwyg",
    	"type" => "wysiwyg(5*5)",	// The size here does not do anything exept sizing the textarea IF javascript is not active. All wysiwyg-fields are at this time all at a fixed size.
    	"description" => "Write a novell!",
    	"min" => "1",
    	"max" => "10240",
    	"null" => true,
    	"errors" => array(
    					"min" => "Please write at least something here ='(",
    					"max" => "Please keep number of character's to [MAX] at most."
    				)
    );

Frequently used - example of an e-mail field =)

    $fieldMail = array(
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
    );

Example of absolute minimal amount of setup for a field. These are the only fields needed to get your form jumpstarted!

    $fieldMinimal = array(
    	"label" => "Minimal:",
    	"type" => "text"
    );

And finally, prep the `$PAGE_form` array:

This form-var is used to fill it with all the fields you will want on your form.
This var is used for looping out all the fields at a given place, filling the forms with posted form-data, and of course validation.

    $PAGE_form = array(
    				$fieldTitle,
    				$fieldAlternative,
    				$fieldWysiwyg,
    				$fieldMail,
    				$fieldMinimal
    			);

After this you can call the generateField-function for each array in the variable `$PAGE_form`. This will be made better in the future.
The validation is automatically generated when you post the page =)

    foreach ($PAGE_form as $field) {
    	generateField($field);
    }
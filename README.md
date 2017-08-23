# MVP Starter Theme and Module Loader v1.5
Basically the most stripped down starting theme

## Guidelines so it doesn't break
1. Place all "modules" in the modules/ folder
2. Modules require the following files: `settings.json`, a template PHP file, and ACF fields
3. Styles/Scripts can be placed in the <module>/assets folder

## Sample `settings.json`
```
{
	// Module status
	"enabled" : true,

	// The name of the module- this should be the name of the ACF field group
	"name" : "Hero",

	// The main template file
	"file" : "hero.php",

	// The path to the file containing the ACF fields
	"fields" : "/fields/group.json",

	// The path to the module style
	"styles": "/assets/Hero.scss"
}
```

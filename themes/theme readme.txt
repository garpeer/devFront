Themes consist of the following files:

default.php
===========
default page template

available variables:
$this->title
$this->theme_dir
$this->locale
$this->content
$this->notices


settings.php
===========
settings page template

available variables:
$this->theme_dir
$this->locale
$this->locales
$this->themes
$this->c_locale (current locale)
$this->c_theme (current theme)
$this->projects
$this->folders
$ŧhis->request

projects.php
===========
project lister template

available variables:
$this->theme_dir
$this->locale
$this->projects

folders.php
===========
folder lister template

available variables:
$this->theme_dir
$this->locale
$this->folders


If a template file is not found for the current them ,the system falls back to 
the one in the default theme.
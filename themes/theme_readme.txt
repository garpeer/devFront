Themes consist of the following files:

default.php
===========
default page template

available variables:
$this->home
$this->title
$this->theme_dir
$ŧhis->request
$this->locale
$this->content
$this->notices


settings.php
===========
settings page template

available variables:
$this->theme_dir
$ŧhis->request
$this->locale
$this->locales
$this->themes
$this->c_locale (current locale)
$this->c_theme (current theme)
$this->projects
$this->folders

projects.php
===========
project lister template

available variables:
$this->theme_dir
$ŧhis->request
$this->locale
$this->projects

folders.php
===========
folder lister template

available variables:
$this->theme_dir
$ŧhis->request
$this->locale
$this->folders


If a template file is not found for the current theme, the system falls back to 
the one in the default theme.
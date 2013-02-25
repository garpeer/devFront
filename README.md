devFront
========

This package can be used to manage development projects and folders in a development environment.

Settings
--------

Settings page is only available from the address 127.0.0.1 (localhost).

###Projects###
- Name: project name to display
- URL: project url (eg. `%HOST%/foo/bar.php` , `/foo/` , `http://foo.bar/`)
    - `%HOST%` is substituted with the hostname of the server.
- Image: Project image filename 
    - lists images in the `project_images` folder.

###Folders###
- Name
- Path: folder path (preferably absolute path)
- Pattern: Pattern to create link from folder name
    - `%HOST%` is substituted with the hostname of the server.
    - `%FOLDER%` is substituted with the folder name.
- Exclude: list of folders to exclude (separated by ','). Eg.: `tmp, bak`
    
####Examples####

    path = /var/www/foo/bar
    pattern = %HOST%/foo/bar/%FOLDER%/

If you want to list documentations in `/var/www/doc/*/html/`:

    path = /var/www/doc/
    pattern = /doc/%FOLDER%/html/

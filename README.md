# What is this?
Scrabble-Words is an application that remembers a list of words and their definitions for you.  You can enter new words and it will fetch their definitions.

# How do I install it?
 * Clone the source code using `git` (try `git clone git://github.com/nylen/scrabble-words/`).  Place the resulting files into a directory reachable from your web server.
 * If you are using Apache, you may want to set up a .htaccess file for authentication.  If you are not using Apache, you should deny access to the `private/` folder using the appropriate method.
 * Create a MySQL table with the schema defined in `private/tabledef.sql`.
 * Copy the file `private/config.example.php` to `private/config.php` and change the settings as needed.

# How do I use it?
 Type one or more words (separated by spaces) in the text box to the left of the "Add word(s)" button.  Click the "Add word(s)" button, and the program will add the requested words to the list, along with their definitions.

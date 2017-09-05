# xmltools


Sometimes you need to process Filemaker pro XML exports and you realize you are wasting hours (ok days for me) processing badly formatted XML :

- bad tags, containing spaces (not good)
- bad tags, containing apostrophs
- bad tags, containing parenthesis (for god's sake, no !)

This simple (yet harsch to bring alive) PHP script cleanups the mess for you.

## Prerequisite

Do an export of all your records (Records > Show all records) ; File > Export Records.
Use the FMPDSORESULT grammar.

## Syntax

php cleanup.php file.xml output.xml  
 
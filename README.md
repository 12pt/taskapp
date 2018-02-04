Taskapp
=======

Created in 1 day's worth of work spread over 2 days due to pre-existing family commitments.

Notes
-----

No frameworks other than `phpunit` and `normalize.css` were used; this was to demonstrate an understanding of the underlying languages rather than knowledge of frameworks, which in the case of JavaScript change yearly.

Of course, this increased development time by quite a bit if one would compare PHP's REST friendliness sans frameworks compared to, say, NodeJS's.

I have however used my own libraries where sensible, such as `./static/ajax.js`.

The browser will mention theres a deprecated Synchronous XMLHttpRequest on the main thread, this is fine as it is the ajax library allowing for use cases
where the user wants synchronous AJAX calls. They shouldn't be in place in this app though.

Assumptions
-----------

* 1 day = "the period of time in which it is still the current date", as opposed to a 12 hours day, a working day, or daylight hours.
* The interface only needs to be "usable", as it is to be done in a day, and I've given the stipulation of not using frameworks (such as Polymer, which essentially gives you material design for free).
* Purely to demonstrate the code rather than be an actual production piece and so no URLs are being re-written.

Usage
-----

Useful scripts

```sh
# run all tests
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/

# run a php server from this dir
php -S localhost:8000
```

Make sure there's an SQL server running, details such as login info are in `config.php`.

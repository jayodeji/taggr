TAGGR
============================

This app does the following:

* Fetch the html source of a webpage given by user
* Summarizes the document by listing which tags are present in the HTML and the amount of tags present
* Highlights tags that are clicked by the user.


### Coding Assumptions And Decisions

* All tags present in the html source code are highlighted. **This includes tags that might be present in javascript within script tags or any other tags that are actually not part of the page dom structure**

* The highlighting applies to the attributes within the tags, so for instance in a tag like that below:
```html
<div class='random'></div>
```
The entire tag including its attributes will be highlighted.

* A regex was used to parse the tags for various reasons, while it is not advised to use a regex for parsing html, if I treat this exercise like a basic search for tags existing in a page, a regex should be sufficient. A regex would not be sufficient if decisions had to be made based on the data on the page however, so the that is the caveat in using a regex.

* I decided to do the highlighting purely on the client side in order to be more responsive and give instant feedback to the user. In addition, a regex was used on the client side to be quick and to be consistent with what was given by the regex from the server side.

* Both open and close tags are matched and counted and highlighted.

See an example of a hosted taggr app [Here](https://calm-bayou-4983.herokuapp.com/)

### Running Locally And Local Development

* Make sure **php5.6** is installed on whatever system this code should be run in.

* Install **composer**. If it is not present. The following command run on the terminal should install composer in your system if not present.
```bash
curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
```

* Install composer dependencies.
```bash
composer install
```

* Install [node.js](https://nodejs.org/en/download/). Alternately, you can use nvm to setup management of node.js versions.
```bash
touch ~/.bash_profile && curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.32.1/install.sh | bash
```

* Install all npm packages
```bash
npm install
```

* To run local tests, run the following command in the root folder
```bash
vendor/bin/phpunit tests/
```

* If doing local development, start compiling js assets.
```bash
./node_modules/.bin/gulp
```

* Start running development server. Make sure you are inside the root directory
```bash
php -S localhost:8000 -t public/
```

* Browse over to the [localhost](http://localhost:8000/) to see the page.

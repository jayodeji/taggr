TAGGR
============================

This app does the following:

* Fetch the html source of a webpage given by user
* Summarizes the document by listing which tags are present in the HTML and the amount of tags present
* Highlights tags that are clicked by the user.


### Coding Assumptions And Decisions

1. All tags present in the html source code are highlighted. **This includes tags that might be present in javascript within script tags or any other tags that are actually not part of the page dom structure**

2. The highlighting applies to the attributes within the tags, so for instance in a tag like that below:
```html
<div class='random'></div>
```
The entire tag including its attributes will be highlighted.

3. A regex was used to parse the tags for various reasons, while it is not advised to use a regex for parsing html, if I treat this exercise like a basic search for tags existing in a page, a regex should be sufficient. A regex would not be sufficient if decisions had to be made based on the data on the page however, so the that is the caveat in using a regex.

4. I decided to do the highlighting purely on the client side in order to be more responsive and give instant feedback to the user. In addition, a regex was used on the client side to be quick and to be consistent with what was given by the regex from the server side.

5. Both open and close tags are matched and counted and highlighted.

See an example of a hosted taggr app [Here](https://calm-bayou-4983.herokuapp.com/)

<?php
class PageTagsTest extends \PHPUnit_Framework_TestCase {

  protected $pageTags;

  protected function setUp() {
    $this->pageTags = new \Jayodeji\Taggr\PageTags();
  }

  /**
   * @dataProvider providerTestTagGeneration
   */
  public function testTagGeneration($params) {
    $page_html = $params['page_html'];
    $expected_tags = $params['expected_tags'];
    $tag_list = $this->pageTags->generateTags($page_html);
    //convert the tag list to an assoc array
    //primarily cause We can then sort by key and compare that way
    //and a lot of tests were written the other way first :(
    $actual = array();
    foreach ($tag_list as $tag) {
      $actual[$tag['title']] = $tag['count'];
    }
    $this->assertEquals($expected_tags, $actual);
  }

  public function providerTestTagGeneration() {
    return array(
      array(array('page_html' => '', 'expected_tags' => array())),
      array(array('page_html' => null, 'expected_tags' => array())),
      array(array('page_html' => '<html></html>', 'expected_tags' => array('html' => 2))),
      array($this->getBasicPageTag()),
      array($this->getSlightlyMoreAdvancedTag()),
      array($this->getHtmlWithTagsWithWeirdEndingsAndHtmlLookalikeTags()),
      array($this->getHtmlWithDifferentTagOpeningAndClosing())
    );
  }

  public function getBasicPageTag() {
    $page_html = '
    <html>
    <head></head>
    <body></body>
    </html>
    ';
    $expected = array(
      'html' => 2,
      'head' => 2,
      'body' => 2
    );
    return array('page_html' => $page_html, 'expected_tags' => $expected);
  }

  public function getSlightlyMoreAdvancedTag() {
    $page_html = '
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Taggr</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="dist/css/user.css">
        <!-- <link rel="stylesheet" href="assets/bootstrap/fonts/font-awesome.min.css"> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    </head>

    <script type="text/javascript">
      $(document).ready(function(){
        var controller = new Taggr.PageController()
        controller.initPage();
      });
    </script>

    <body>
        <nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button><a class="navbar-brand navbar-link" href="#">TAGGR </a></div>
                <div class="collapse navbar-collapse" id="navcol-1"></div>
            </div>
        </nav>
        <div id="landing-page">
          <div class="jumbotron hero">
              <div class="row">
                  <div class="col-md-12 col-md-offset-2">
                      <div class="row">
                          <div class="col-md-8">
                              <input id="url-input" type="url">
                          </div>
                          <div class="col-md-4">
                              <button id="load-button" class="btn btn-default" type="button">Button</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <section class="testimonials">
              <h2 class="text-center">People Love It!</h2>
              <blockquote>
                  <p>Great app for quickly viewing the source code of any page</p>
                  <footer>Joshua Adeyemi</footer>
              </blockquote>
          </section>
        </div>

        <div id="page-source-view" style="display:none;">
          <section>
              <div id="raw-page-source">
              </div>
          </section>
          <section>
              <p id="tag-list"></p>
          </section>
        </div>

        <footer class="site-footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <h5>Taggr © 2015</h5></div>
                    <div class="col-sm-6 social-icons"><a href="#"><span class="fa fa-facebook"></span></a><a href="#"><span class="fa fa-twitter"></span></a><a href="#"><span class="fa fa-instagram"></span></a></div>
                </div>
            </div>
            <a class="this is weord but should be caught">
            <div></div>
        </footer>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.2.3/backbone-min.js"></script>
        <script src="dist/js/tag-view.js"></script>
        <script src="dist/js/tag-list-view.js"></script>
        <script src="dist/js/landing-view.js"></script>
        <script src="dist/js/page-source-view.js"></script>
        <script src="dist/js/page-source-model.js"></script>
        <script src="dist/js/page-controller.js"></script>
    </body>

    </html>
    ';

    $expected_tag_list = array(
      'html' => 2,
      'head' => 2,
      'meta' => 2,
      'title' => 2,
      'link' => 3,
      'script' => 22,
      'body' => 2,
      'nav' => 2,
      'div' => 34,
      'button' => 4,
      'span' => 14,
      'a' => 9,
      'input' => 1,
      'section' => 6,
      'h2' => 2,
      'blockquote' => 2,
      'p' => 4,
      'footer' => 4,
      'h5' => 2,
    );
    return array('page_html' => $page_html, 'expected_tags' => $expected_tag_list);
  }

  public function getHtmlWithTagsWithWeirdEndingsAndHtmlLookalikeTags() {
    $page_html = '
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Taggr</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="dist/css/user.css">
        <!-- <link rel="stylesheet" href="assets/bootstrap/fonts/font-awesome.min.css"> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    </head>

    <script type="text/javascript">
      $(document).ready(function(){
        var controller = new Taggr.PageController()
        controller.initPage();
        var test_html_str = "
          <html><head></head><body></body></html>
        ";
        var do_not_pic = "<dea;"
        console.log(test_html_str);
      });
    </script>

    <body>
        <div>
          <p>Lorem Ipsum </p>
          <br />
          <br
          <img src="smiley.gif" alt="Smiley face" height="42" width="42">
          <ul>
            <li>First</li>
            <li>Second</li>
          </ul>
        </div>
    </body>

    </html>
    ';
    $expected_tag_list = array(
      'html' => 4,
      'head' => 4,
      'meta' => 2,
      'title' => 2,
      'link' => 3,
      'script' => 4,
      'body' => 4,
      'div' => 2,
      'p' => 2,
      'br' => 1,
      'ul' => 2,
      'li' => 4,
      'img' => 1
    );
    return array('page_html' => $page_html, 'expected_tags' => $expected_tag_list);
  }

  public function getHtmlWithDifferentTagOpeningAndClosing() {
    $page_html = '
    <img src="//smiley.gif" alt="Smiley face" height="42" width="42" >
<img src="//smiley.gif" alt="Smiley face" height="42" width="42">
<img src="//smiley.gif" alt="Smiley face" height="42" width="42" />
<img src="//smiley.gif" alt="Smiley face" height="42" width="42"/>
<img src="//smiley.gif" alt="Smiley face" height="42" width="42" / >
<img src="//smiley/me.gif" alt="Smiley face" height="42" width="42" ></img>
<img src="//smiley/me.gif" alt="Smiley face" height="42" width="42" ></ img>
<img src="//smiley/me.gif" alt="Smiley face" height="42" width="42" ></ img >
<img src="//smiley/me.gif" alt="Smiley face" height="42" width="42" >< / img >
<img src="//smiley/me.gif" alt="Smiley face" height="42" width="42" ></img >
<img src="//smiley.gif" alt="Smiley face" height-data="42" width="42"/>
<img src="//smiley.gif" alt="Smiley face" height-data="42" width="42">
    ';
    $expected_tag_list = array(
      'img' => 17
    );
    return array('page_html' => $page_html, 'expected_tags' => $expected_tag_list);
  }
}

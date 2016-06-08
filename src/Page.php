<?php
namespace Jayodeji\Taggr;

/**
 * This class will be given a web page when instantiated.
 * The goal of this class will be get grab the raw source code
 * of the web page. It needs to then process the tags of the page
 * and should be able to return a list of all the tags that exist in the
 * page in additon to the actual raw page itself.
 * @author Joshua Adeyemi
 */
class Page {

  /**
   * This could easily be cached so we do not have to do this
   * generation over and over again.
   * Perhaps make the request to get the page source, use the page url to get the
   * cache record (if exists compare the md5 of existing cache record to that of
   * the retrieved page, if its the same, we know the page has not changed and we
   * can just returned the cached processed page)
   * @param  string $url url to retrieve
   * @return array      assoc array of tag list and page soruce
   */
  public function processWebPage($url) {
    $this->validateUrl($url);
    $raw_html = trim($this->fetchRawHtml($url));
    return array(
      'id' => base64_encode($url),
      'raw_page_source' => $this->formatHtmlOutput($raw_html),
      'tag_list' => $this->getPageTags($raw_html)
    );
  }

  protected function validateUrl($url) {
    $res = filter_var($url, FILTER_VALIDATE_URL);
    if ($res === false) {
      throw new Exception\InvalidUrl('url_invalid');
    }
  }

  protected function getPageTags($raw_html) {
    $page_tags = new PageTags();
    return $page_tags->generateTags($raw_html);
  }

  protected function formatHtmlOutput($raw_html) {
    return $raw_html;
  }

  protected function fetchRawHtml($url) {
    $page = @file_get_contents(strtolower($url));
    if ($page === false) {
      throw new Exception\PageCannotBeLoaded('Page cannot be loaded.');
    }
    return $page;
  }
}

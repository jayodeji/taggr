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
    return utf8_encode($raw_html);
  }

  protected function fetchRawHtml($url) {
    $page = $this->getPage($url);
    if ($page === false) {
      throw new Exception\PageCannotBeLoaded('Page cannot be loaded.');
    }
    return $page;
  }

  protected function getPage($url) {
    $ch = curl_init();
    $timeout = 5;
    //some sites block spiders
    $user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
    $options = array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_CONNECTTIMEOUT => $timeout,
      CURLOPT_USERAGENT => $user_agent
    );
    curl_setopt_array($ch, $options);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

}

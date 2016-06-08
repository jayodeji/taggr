<?php
namespace Jayodeji\Taggr;

/**
 * Given a page html, generate a list of tags
 * @author Joshua Adeyemi
 */
class PageTags {

  protected $tagList;

  public function generateTags($page_html) {
    $this->tagList = array();
    $matched_tags = $this->getMatchedTags($page_html);
    foreach ($matched_tags as $match_tag) {
      $tag = explode(' ', $match_tag);
      $tag_name = str_replace(array('<','>'), '', strtolower($tag[0]));
      if ($this->tagExists($tag_name) === false) {
        $this->addToTagList($tag_name, $page_html);
      }
    }
    return $this->transformTags();
  }

  protected function tagExists($tag_name) {
    return isset($this->tagList[$tag_name]);
  }

  /**
   * To deal with some false positives, for every tag, lets try to use regex to match
   * it in the document. If the tag is valid, it will match the following format.
   * Note that we have already matched for tags that were valid in terms of having < and >,
   * so this match we are doing here is going off of the assumption that we have removed the negatives
   * and now we are just trying to remove false positives.
   * <tag>, <\tag>, <tag , <tag/>
   * @param [type] $tag       [description]
   * @param [type] $page_html [description]
   */
  protected function addToTagList($tag, $page_html) {
    $test_regex = "/(<\s*{$tag}((\s+\w+(\s*=\s*(\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)?\s*?\/*?\s*?>|<\s*\/{0,1}\s*{$tag}\s*>)|<\s*{$tag}\s*\/\s*>/i";
    $matches = array();
    preg_match_all($test_regex, $page_html, $matches);
    $num_tags = count($matches[0]);
    if ($num_tags > 0) {
      $this->tagList[$tag] = $num_tags;
    }
  }

  /**
   * Using regex in this case because it is a relatively efficient and easy way to extract out the tags.
   * extract all matching tags.
   * Using the provided php DOMDocument or Tidy has some other side effects that we do not necessarily want.
   * Such, as they actually try to validate and cleanup the html structure and it will be better if we do nothing
   * that mutates the html structure at all.
   */
  protected function getMatchedTags($page_html) {
    $regex = '/<?\w+((\s+\w+(\s*=\s*(\".*?"|\'.*?\'|[^\'\">\s]+))?)+\s*|\s*|\s*\/)?\s*?\/*?\s*?>/';
    $matches = array();
    preg_match_all($regex, $page_html, $matches);
    return $matches[0];
  }

  protected function transformTags() {
    $transformed_tags = array();
    foreach ($this->tagList as $name => $count) {
      $transformed_tags[] = array('title' => $name, 'count' => $count);
    }
    return $transformed_tags;
  }

}

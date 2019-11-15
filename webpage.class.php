<?php

/**
 * This class provides methods in order to ease 
 * the production of a HTML5 Web page
 *
 * @author Jérôme Cutrona (jerome.cutrona@univ-reims.fr)
 * @author Olivier Nocent (olivier.nocent@univ-reims.fr)
 * @version 1.1
 */
class WebPage {
  /**
   * @var string Language in which the content is written
   */
  protected $lang = null;

  /**
   * @var string Text nested in the <title> element (mandatory)
   */
  protected $title = null;

  /**
   * @var string Content nested in the <head> element (mainly CSS code)
   */
  protected $head  = null;

  /**
   * @var string Content nested in the <body> element
   */
  protected $body  = null;

  /**
   * @var string Scripts inserted after the content nested in the <body> element,
   *             for performance purposes
   */
  protected $js  = null;

  /**
   * Constructor
   * @param string $title Page title (mandatory)
   *
   * @return void
   */
  public function __construct($title, $lang="fr") {
    $this->setTitle($title);
    $this->setLanguage($lang);
  }

  /**
   * Escape special characters not allowed in the content of a Web page (for example <, >, &).
   * @see http://php.net/manual/en/function.htmlentities.php
   * @param string $content Content to be escaped
   *
   * @return string Escaped content
   */
  public static function escapeString($content) {
    return htmlentities($content, ENT_QUOTES|ENT_HTML5, "utf-8");
  }

  /**
   * Set the language in which the content is written
   * @param string $lang Language codename (en, fr, pt, ...)
   *
   * @return void
   */
  public function setLanguage($lang) {
    $this->lang = $lang;
  }

  /**
   * Set the title of the Web page
   * @param string $title Title
   *
   * @return void
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * Set the author of the Web page
   * @param string $authorName Author's name
   *
   * @return void
   */
  public function setAuthor($authorName) {
    $this->appendToHead(<<<HTML
    <meta name="author" content="$authorName">

HTML
                       );
  }

  /**
   * Attach a list of keywords to the Web page
   * @param string $keywordList Keyword list
   *
   * @return void
   */
  public function setKeywords($keywordList) {
    $this->appendToHead(<<<HTML
    <meta name="keywords" content="$keywordList">

HTML
                       );
  }

  /**
   * Add a short description to the Web page
   * @param string $descriptionText Short description text
   *
   * @return void
   */
  public function setDescription($descriptionText) {
    $this->appendToHead(<<<HTML
    <meta name="description" content="$descriptionText">

HTML
                       );
  }

  /**
   * Add content nested in the <head> element
   * @param string $content Content to be added
   *
   * @return void
   */
  public function appendToHead($content) {
    $this->head .= $content;
  }

  /**
   * Add CSS code nested in the <head> element
   * @param string $css CSS code to be added
   *
   * @return void
   */
  public function appendCss($css) {
    $this->appendToHead(<<<HTML
    <style type="text/css">
      {$css}
    </style>

HTML
                       );
  }

  /**
   * Add the URL of an external CSS file
   * @param string $url CSS file URL
   *
   * @return void
   */
  public function appendCssUrl($url) {
    $this->appendToHead(<<<HTML
    <link rel="stylesheet" type="text/css" href="{$url}">

HTML
                       );
  }

  /**
   * Add raw text to $js attribute
   * @param string $rawText raw text to be added
   *
   * @return void
   */
  public function appendRawTextToJs($rawText) {
    $this->js .= $rawText;
  }

  /**
   * Add JavaScript code
   * @param string $js JavaScript code to be added
   *
   * @return void
   */
  public function appendJs($js) {
    $this->appendRawTextToJs(<<<HTML
    <script>
    {$js}
    </script>

HTML
                            );
  }

  /**
   * Add the URL of an external JavaScript file
   * @param string $url JavaScript file URL
   *
   * @return void
   */
  public function appendJsUrl($url) {
    $this->appendRawTextToJs(<<<HTML
    <script src='{$url}'></script>

HTML
                            );
  }

  /**
   * Add content nested in the <body> element
   * @param string $content Content to be added
   *
   * @return void
   */
  public function appendContent($content) {
    $this->body .= $content;
  }

  /**
   * Generate the HTML5 code of the Web page as a string
   *
   * @return string HTML5 code
   */
  public function toHTML() {
    return <<<HTML
<!doctype html>
<html lang="{$this->lang}">
  <head>
    <meta charset="utf-8">
    <title>{$this->title}</title>
{$this->head}
  </head>
  <body>
{$this->body}
{$this->js}
  </body>
</html>
HTML;
  }
}

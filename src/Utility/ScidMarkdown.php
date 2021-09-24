<?php
/**
 * Created by PhpStorm.
 * User: sdevore
 * Date: 3/19/18
 * Time: 12:39 PM
 */

namespace Scid\Utility;

use cebe\markdown\Markdown;
use cebe\markdown\MarkdownExtra;
use \cebe\markdown\GithubMarkdown;

class ScidMarkdown
{

    private static $_parser = NULL;

    /**
     * @param $text
     *
     * @return string
     */
    public static function parse($text) {
        /**
         * Parses the given text considering the full language.
         *
         * This includes parsing block elements as well as inline elements.
         *
         * @param string $text the text to parse
         *
         * @return string parsed markup
         */
        if (empty(self::$_parser)) {
            self::$_parser = new \cebe\markdown\MarkdownExtra();
        }
        return self::$_parser->parse($text);
    }

}

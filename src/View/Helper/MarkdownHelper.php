<?php

    namespace Scid\View\Helper;

    use Cake\Cache\Cache;
    use Cake\Core\Configure;
    use Cake\Filesystem\File;
    use Cake\View\Helper;
    use Cake\View\View;
    use cebe\markdown\Markdown;
    use cebe\markdown\MarkdownExtra;
    use cebe\markdown\GithubMarkdown;
    use DebugKit\DebugTimer;

    /**
     * Markdown helper
     */
    class MarkdownHelper extends Helper
    {

        /**
         * Default configuration.
         *
         * @var array
         */
        protected $_defaultConfig = [];

        /**
         * Default markdown parser is null.
         *
         * @var \cebe\markdown\Parser
         */
        protected $_parser = NULL;

        /**
         * HtmlHelper constructor.
         *
         * @param \Cake\View\View $View
         * @param array           $config
         */
        public function __construct(\Cake\View\View $View, array $config = []) {
            $parserName = Configure::read('Scid.MarkdownHelper.parser');
            if (!empty($parserName)) {
                $this->setParser($parserName);
            }
            else {
                $this->setParser();
            }
            parent::__construct($View, $config);
        }

        public function setParser($parserName = 'default') {
            switch ($parserName) {
                case 'markdown-extra':
                    $this->_parser = new \cebe\markdown\MarkdownExtra();
                    break;
                case 'github':
                    $this->_parser = new \cebe\markdown\GithubMarkdown();
                    break;
                case 'default':
                default:
                    $this->_parser = new \cebe\markdown\Markdown();
            }
        }

        /**
         * Parses the given text considering the full language.
         *
         * This includes parsing block elements as well as inline elements.
         *
         * @param string $text the text to parse
         *
         * @return string parsed markup
         */
        public function parse($text) {
            return $this->_parser->parse($text);
        }

        /**
         * Parses a paragraph without block elements (block elements are ignored).
         *
         * @param string $text the text to parse
         *
         * @return string parsed markup
         */
        public function parseParagraph($text) {
            return $this->_parser->parseParagraph($text);
        }

        /**
         * Fetch the content for a block and render it with the markdown parser. If a block is
         * empty or undefined '' will be returned.
         *
         * @param string $name    Name of the block
         * @param string $default Default text
         *
         * @return string The block content or $default if the block does not exist.
         * @see \Cake\View\View::fetch()
         */
        public function fetch($blockName = 'markdown', $default = '') {
            $block = $this->_View->fetch($blockName, $default);
            if ($block) {
                $md5 = md5($block);
                $parsed = Cache::read($md5);
                if (!$parsed) {
                    $parsed = $this->parse($block);
                    Cache::write($md5, $parsed);
                }

                return $parsed;
            }
            else {
                return '';
            }
        }

        public function element($path, $options) {
            if (isset($options['cache'])) {
                if (is_bool($options['cache'])) {
                    $options['cache'] = ['config' => NULL, 'key' => NULL];
                }
                if (empty($options['cache']['config'])) {
                    $options['cache']['config'] = 'default';
                }
                if (empty($options['cache']['key'])) {
                    $options['cache']['key'] = 'markdown_' . $path;
                }
                $cache = Cache::read($options['cache']['key'], $options['cache']['config']);
                if (!empty($cache)) {
                    return $cache;
                }
            }
            $pathInfo = pathinfo($path);
            if (empty($pathInfo['extension'])) {
                $pathInfo['extension'] = 'md';
            }
            $markdownFolderName = 'Markdown';
            if (empty($pathInfo['dirname']) || '.' == $pathInfo['dirname']) {
                $pathInfo['dirname'] = $markdownFolderName;
            }
            else {
                $pathInfo['dirname'] = $markdownFolderName . DS . $pathInfo['dirname'];
            }
            if (!empty($pathInfo['basename'])) {
                $filePath =
                    APP . 'Template' . DS . 'Element' . DS . $pathInfo['dirname'] . DS . $pathInfo['basename'] . '.' . $pathInfo['extension'];
                $file = new File($filePath);
                $text = $file->read();
                if ($text) {
                    $parsed = $this->_parser->parse($text);
                    if (!empty($options['cache']['key'])) {
                        Cache::write($options['cache']['key'], $parsed, $options['cache']['config']);
                    }

                    return $parsed;
                }
            }

            return '';
        }

    }

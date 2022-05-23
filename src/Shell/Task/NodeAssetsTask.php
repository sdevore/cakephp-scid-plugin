<?php

    namespace Scid\Shell\Task;

    use Cake\Console\ConsoleIo;
    use Cake\Console\Shell;
    use Cake\Core\Configure;
    use Cake\Core\Plugin;
    use Cake\Filesystem\File;
    use Cake\Filesystem\Folder;
    use Cake\Utility\Text;

    /**
     * Task for installing Bootstrap assets and layouts to app.
     */
    class NodeAssetsTask extends Shell
    {

        protected $_assetDir;
        protected $_nodeDir;
        protected $_cssDir;
        protected $_jsDir;
        private $_webfontsDir;
        private $_spritesDir;
        private $_svgsDir;
        private $_nodePreserveDir;

        public function __construct() {
            parent::__construct();
            $this->_assetDir = new Folder(Plugin::path('Scid') . 'webroot', TRUE);
            $this->_nodeDir = new Folder(Plugin::path('Scid') . 'node_modules', TRUE);
            $this->_nodePreserveDir = new Folder(Plugin::path('Scid') . 'node_modules_preserve', TRUE);

            $this->_cssDir = new Folder($this->_assetDir->path . DS . 'css', TRUE);
            $this->_jsDir = new Folder($this->_assetDir->path . DS . 'js', TRUE);
            $this->_webfontsDir = new Folder($this->_assetDir->path . DS . 'webfonts', TRUE);
            $this->_spritesDir = new Folder($this->_assetDir->path . DS . 'sprites', TRUE);
            $this->_svgsDir = new Folder($this->_assetDir->path . DS . 'svgs', TRUE);
        }

        /**
         * Installs Bootstrap assets using npm
         *
         * @return void
         */
        public function installAssets() {
            $this->info('Checking npm...');
            $npm = 'npm';
            if (!`which npm`) {
                if (file_exists('/snap/bin/npm')) {
                    $npm = '/snap/bin/npm';
                }
                else if (file_exists('/usr/local/bin/npm')) {
                    $npm = '/usr/local/bin/npm';
                }
                else {
                    $this->abort('NPM (https://www.npmjs.com/) is required, but not installed. Aborting.');
                }
            }

            $this->_createNpmrc();
            $this->cleanAssets();

            exec($npm . ' install --verbose', $output, $return);
            $this->out($output);
            if ($return === 0) {

                $this->success('Scid assets installed successfully.');
            }
            else {
                $this->abort('SCid assets could not be installed.');
            }
        }

        public
        function copyFontAwesomePro() {
            $files = [];
            $folder = new Folder($this->_nodeDir->path . DS . '@fortawesome/fontawesome-pro');
            foreach ($folder->findRecursive() as $file) {
                $files[] = new File($file);
            }
            $sprites = Folder::addPathElement($folder->path, 'sprites');
            $webfonts = Folder::addPathElement($folder->path, 'webfonts');
            $svgs = Folder::addPathElement($folder->path, 'svgs');
            $scss = Folder::addPathElement($folder->path, 'scss');
            foreach ($files as $file) {
                $dir = NULL;
                /** @var \Cake\Filesystem\Folder $parent */
                $parent = $file->folder();
                try {
                if (!$parent->inPath($scss)) {
                    if ($parent->inPath($sprites)) {
                        $dir = $this->_spritesDir;
                    }
                        elseif ($parent->inPath($webfonts)) {
                        $dir = $this->_webfontsDir;
                    }
                        elseif ($parent->inPath($svgs)) {
                        $pInfo = pathinfo($parent->path);
                        $dir = new Folder(Folder::addPathElement($this->_svgsDir->path, $pInfo['basename']));
                    }
                        elseif (preg_match('/.css/', $file->name)) {
                        $dir = $this->_cssDir;
                    }
                        elseif (preg_match('/.js|.min.map/', $file->name)) {
                        $dir = $this->_jsDir;
                    }
                }
                try {
                    if (!empty($dir) && !file_exists($dir->path . DS . $file->name) && @$file->copy($dir->path . DS . $file->name)) {
                        $this->success($file->name . ' successfully copied.', 1, ConsoleIo::VERBOSE);
                    }
                    else {
                        $this->info($file->name . ' will not be copied.', 1, ConsoleIo::VERBOSE);
                    }
                } catch (\Exception $exception) {
                    $this->info($file->name . ' will not be copied. Exception: ' . $exception->getMessage(), 1, ConsoleIo::VERBOSE);
                }
            }
        }

        /**
         * Copy assets from node_modules folder to plugin's webroot
         * If in production mode, just copies min. required and minified assets
         *
         * @return void
         */
        public
        function copyAssets() {
            $this->info('Clearing webroot and copying assets...');
//        if ($this->_clear($this->_assetDir, '^(?!cover)(?!dashboard)(?!signin)(?!baked-with-cakephp.svg).*$')) {
//            $this->success('All files cleared...');
//        }

            $files = [];
            $folders = [];
            $folders[] = new Folder($this->_nodeDir->path . DS . 'bootstrap/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'bootstrap-duration-picker/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'bootstrap-markdown-editor/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'bootstrap-toggle');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'checkboxes.js/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'datepair.js/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'flatpickr/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'jquery/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'jquery-awesome-cursor/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'jquery-mask-plugin/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'markitup/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'popper.js/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'select2/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'vue/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'signature_pad/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'sticky-sidebar/dist');
            $folders[] = new Folder($this->_nodeDir->path . DS . 'timepicker/dist');

            foreach ($folders as $folder) {
                foreach ($folder->findRecursive() as $file) {
                    $files[] = new File($file);
                }
            }
            try {
            $this->_copy($files);
            } catch (\Exception $exception) {
                $this->info('Some did not copy. Exception: ' . $exception->getMessage(), 1, ConsoleIo::VERBOSE);
            }

        }

        /**
         * Copy sample layouts to app's layout dir
         *
         * @param string $target The destination path
         *
         * @return void
         */
        public
        function copyLayouts($target = NULL) {
            $this->info('Copying sample layouts...');
            $layoutDir =
                new Folder(Plugin::path('BootstrapUI') . 'src' . DS . 'Template' . DS . 'Layout' . DS . 'examples');

            if ($target == NULL) {
                $target = APP . 'Template' . DS . 'Layout' . DS . 'TwitterBootstrap';
            }

            if (!$layoutDir->copy($target)) {
                $this->abort('Sample layouts could not be copied.');
            }
            $this->success('Sample layouts copied successfully.');
        }

        /**
         * Copy files to assetdir's css/js path
         *
         * @param array $files Assetfiles to copy
         *
         * @return void
         */
        protected
        function _copy(array $files) {
            foreach ($files as $file) {
                $dir = NULL;
                if (preg_match('/.css/', $file->name)) {
                    $dir = $this->_cssDir;
                }
                else if (preg_match('/.js|.min.map/', $file->name)) {
                    $dir = $this->_jsDir;
                }
                try {
                    if (!empty($dir) && $file->copy($dir->path . DS . $file->name)) {
                        $this->verbose($file->name . ' successfully copied.');
                    }
                    else {
                        $this->warn($file->name . ' could not be copied.');
                    }
                } catch (\Exception $e) {
                    $this->warn($dir->path . DS . $file->name . ' could not be copied due to exception: ' . $e->getMessage());
                }
            }
        }

        public
        function perserveNodeModules($dirs = []) {
            $this->info(__('Preserve {0} npm_modules that we don\'t want to clear...', [Text::toList($dirs)]));
            foreach ($dirs as $dir) {
                $path = $this->_nodePreserveDir->path;
                $path = Folder::addPathElement($path, $dir);
                $destination = new Folder($path, TRUE);
                $destination->delete();
                $source = new Folder($this->_nodeDir->path . DS . $dir);
                $source->copy($destination->path);
            }
            $this->success(__('{0} Directories moved to nmp_modules_preserve', [Text::toList($dirs)]));
        }

        /**
         * Clear folder of assets
         *
         * @param \Cake\Filesystem\Folder $folder Folder to clear
         * @param string                  $except Files to skip
         * @return bool
         */
        protected
        function _clear(Folder $folder, $except) {
            $files = $folder->findRecursive($except);
            foreach ($files as $file) {
                $file = new File($file);
                if (!$file->delete()) {
                    return FALSE;
                }
            }

            return TRUE;
        }

        /**
         * @return void
         */
        protected
        function _createNpmrc(): void {
            $npmrc = new File(Plugin::path('Scid') . DS . '.npmrc');
            if ($npmrc->exists()) {
                $npmrc->delete();
            }
            $token = Configure::read('Scid.font-awesome-token');
            if (!empty($token)) {
                $npmrc->create();
                $npmrc->write(__("@fortawesome:registry=https://npm.fontawesome.com/
//npm.fontawesome.com/:_authToken={0}", [$token]));
            }
        }

        /**
         * @return void
         */
        public
        function cleanAssets(): void {
            chdir(Plugin::path('Scid'));

            $node_mod = new Folder('node_modules');
            if ($node_mod->delete()) {
                $this->success('Cleared node_modules...');
            }
        }
    }

<?php

namespace Scid\Shell;

use Cake\Console\Shell;
use Cake\Filesystem\File;

/**
 * @property \Scid\Shell\Task\NodeAssetsTask $NodeAssets
 * @property \Cake\Shell\Task\AssetsTask            $Assets
 */
class ScidShell extends Shell
{
    /**
     * Tasks used by this shell.
     *
     * @var array
     */
    public $tasks = ['Scid.NodeAssets', 'Assets'];

    /**
     * Installs assets via npm and symlinks them wo app's webroot
     *
     * @return void
     */
    public function install()
    {
        $this->NodeAssets->installAssets();
        $this->NodeAssets->copyFontAwesomePro();
        $this->NodeAssets->copyAssets();
        $this->NodeAssets->perserveNodeModules(['bootstrap']);
        $this->NodeAssets->cleanAssets();
        $this->Assets->remove('Scid');
        $this->Assets->symlink('Scid');
    }





    /**
     * Get the option parser.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser->setDescription([
            'SCid Shell',
            '',
            ''
        ])->addSubcommand('install', [
            'help' => 'Installs Bootstrap assets and links them to app\'s webroot.'
        ]);
    }
}

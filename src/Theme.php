<?php
namespace Hilabs\Theme;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Collection;

class Theme
{
    /**
     * @var string
     */
    protected $active;
    /**
     * @var Repository
     */
    protected $config;
    /**
     * @var Filesystem
     */
    protected $files;
    /**
     * @var string
     */
    protected $path;
    /**
     * @var View
     */
    protected $viewFactory;

    /**
     * Constructor method.
     *
     * @param Filesystem  $files
     * @param Repository  $config
     * @param ViewFactory $viewFactory
     */
    public function __construct(Filesystem $files, Repository $config, ViewFactory $viewFactory)
    {
        $this->config      = $config;
        $this->files       = $files;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Register custom namespaces for all themes.
     *
     * @return null
     */
    public function register()
    {
        foreach ($this->all() as $theme) {
            $this->registerNamespace($theme);
        }
    }

    /**
     * Register custom namespaces for specified theme.
     *
     * @param string $theme
     * @return null
     */
    public function registerNamespace($theme)
    {
        $this->viewFactory->addNamespace('theme', $this->getThemePath($theme));
    }

    /**
     * Get all themes.
     *
     * @return Collection
     */
    public function all()
    {
        $themes = [];
        if ($this->files->exists($this->getPath())) {
            $scannedThemes = $this->files->directories($this->getPath());
            foreach ($scannedThemes as $theme) {
                $themes[] = basename($theme);
            }
        }
        return new Collection($themes);
    }

    /**
     * Check if given theme exists.
     *
     * @param  string $theme
     * @return bool
     */
    public function exists($theme)
    {
        return in_array($theme, $this->all()->toArray());
    }

    /**
     * Gets themes path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?: $this->config->get('theme.paths.absolute');
    }

    /**
     * Sets themes path.
     *
     * @param string $path
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Gets active theme.
     *
     * @return string
     */
    public function getActive()
    {
        return $this->active ?: $this->config->get('theme.active');
    }

    /**
     * Sets active theme.
     *
     * @return Themes
     */
    public function setActive($theme)
    {
        $this->active = $theme;
        return $this;
    }

    /**
     * Gets the specified themes path.
     *
     * @param string $theme
     * @return string
     */
    public function getThemePath($theme)
    {
        return $this->getPath()."/{$theme}/";
    }

    /**
     * Generate a HTML link to the given asset using HTTP for the
     * currently active theme.
     *
     * @return string
     */
    public function asset($asset, $secure = false)
    {
        $basePath = $this->config->get('theme.paths.base');
        $assetPath = $this->config->get('theme.paths.assets');
        $fullPath = $basePath.'/'.$this->getActive().'/'.$assetPath.'/'.$asset;

        if($secure){
            return secure_asset($fullPath);
        }
        return asset($fullPath);
    }
}

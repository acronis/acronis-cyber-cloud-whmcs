<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\View;

use AcronisCloud\Service\BuildInfo\BuildInfoAwareTrait;
use AcronisCloud\Util\Str;
use InvalidArgumentException;

class ViewLoader
{
    use BuildInfoAwareTrait;

    /** @var string */
    private $templateDir;

    /** @var \Smarty */
    private $template;

    /** @var string */
    private $assetsLocalDir;

    /** @var string */
    private $assetsPath;

    /** @var array */
    private $assets;

    private static $assetsVar = 'assetLinks';

    public function __construct($templateDir, $assetsLocalDir)
    {
        $this->setTemplateDir($templateDir);
        $this->setAssetsDir($assetsLocalDir);
        $this->template = new \Smarty();
    }

    /**
     * @param $templateDir
     * @return $this
     */
    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $templateDir;

        return $this;
    }

    /**
     * @param $assetsDir
     * @return $this
     */
    public function setAssetsDir($assetsDir)
    {
        $this->assetsLocalDir = $assetsDir;
        $this->buildAssetsPath();

        return $this;
    }

    /**
     * @param $variable
     * @param $value
     * @return ViewLoader
     * @throws InvalidArgumentException
     */
    public function assign($variable, $value)
    {
        if ($variable === static::$assetsVar) {
            throw new InvalidArgumentException(Str::format(
                'Variable name "%s" is reserved for template functionality.',
                static::$assetsVar
            ));
        }
        $this->template->assign($variable, $value);

        return $this;
    }

    /**
     * @param $asset
     * @param bool $path
     * @return ViewLoader
     */
    public function addAsset($asset, $path = false)
    {
        $version = $this->getBuildInfo()->getPackageVersion();
        $ext = pathinfo($asset,PATHINFO_EXTENSION);
        $this->assets[$ext] = isset($this->assets[$ext]) ? $this->assets[$ext] : [];
        $this->assets[$ext][] = ($path ?: $this->assetsPath) . '/' . $asset . '?v=' . $version;

        return $this;
    }

    /**
     * @param $templateName
     * @return string
     * @throws \SmartyException
     */
    public function fetch($templateName)
    {
        $this->initTemplate();
        $this->template->assign(static::$assetsVar, $this->assets);

        return $this->template->fetch($templateName);
    }

    /**
     * @return void
     */
    protected function initTemplate()
    {
        $this->template->setCompileDir($this->getTemplateCompileDir());
        $this->template->setTemplateDir($this->templateDir);
    }

    /**
     * @return string
     */
    protected function getTemplateCompileDir()
    {
        global $templates_compiledir;

        return $templates_compiledir;
    }

    /**
     * @return void
     */
    protected function buildAssetsPath()
    {
        $this->assetsPath = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', realpath($this->assetsLocalDir));
    }
}
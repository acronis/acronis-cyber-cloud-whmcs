<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Localization;

use Illuminate\Translation\ArrayLoader as IlluminateArrayLoader;
use Illuminate\Translation\Translator as IlluminateTranslator;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\Translator as SymfonyTranslator;

class Translator extends IlluminateTranslator
{
    /**
     * @var SymfonyTranslator
     */
    private $internalTranslator;

    public function __construct($locale)
    {
        $loader = new IlluminateArrayLoader();
        parent::__construct($loader, $locale);
        $this->internalTranslator = new SymfonyTranslator($locale);
    }

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->internalTranslator->trans($id, $parameters, $domain, $locale);
    }


    /**
     * Adds a Loader.
     *
     * @param $fileFormat
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function addLoader($fileFormat, LoaderInterface $loader)
    {
        $this->internalTranslator->addLoader($fileFormat, $loader);
    }

    /**
     * Adds a Resource.
     *
     * @param $fileFormat
     * @param mixed $file The resource name
     * @param string $locale The locale
     *
     */
    public function addResource($fileFormat, $file, $locale)
    {
        $this->internalTranslator->addResource($fileFormat, $file, $locale);
    }
}
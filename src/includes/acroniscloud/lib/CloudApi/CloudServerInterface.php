<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\CloudApi;

interface CloudServerInterface
{
    const SCHEMA_HTTPS = 'https';
    const SCHEMA_HTTP = 'http';
    const MINIMAL_SUPPORTED_CLOUD_MAJOR_VERSION = 8;

    /**
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isSecure();

    /**
     * @return string
     */
    public function getHostname();

    /**
     * @return int | null
     */
    public function getPort();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @return string
     */
    public function getAccessHash();
}
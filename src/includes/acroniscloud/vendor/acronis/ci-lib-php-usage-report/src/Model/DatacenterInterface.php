<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Model;

interface DatacenterInterface
{
    public function getId();

    public function getName();

    public function getHostname();

    public function getUsername();

    public function getPassword();
}
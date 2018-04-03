<?php

namespace Deployer\Discord;

/**
 * Interface MessagingInterface
 *
 * @package Deployer\Discord
 */
interface MessagingInterface
{
    /**
     * @return array
     */
    public function notify();

    /**
     * @return array
     */
    public function success();

    /**
     * @return array
     */
    public function failure();
}

<?php

namespace DataSift\TestBundle\Server\Listener;

/**
 *
 * @author jcabantous
 */
interface ServerListenerInterface
{
    public function onDataReceived($data);
}

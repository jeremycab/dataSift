<?php

namespace DataSift\TestBundle\Socket\Server\Listener;

/**
 *
 * @author jcabantous
 */
interface ServerListenerInterface
{
    public function onDataReceived($data);
}

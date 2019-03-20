<?php

namespace Ehann\RedisRaw;

use Predis\Client;

/**
 * Class PredisAdapter
 * @package Ehann\RedisRaw
 *
 * This class wraps the NRK client: https://github.com/nrk/predis
 */
class PredisAdapter extends AbstractRedisRawClient
{
    /** @var Client */
    public $redis;

    public function connect($hostnames = '127.0.0.1', int $port = 6379, int $db = 0, string $password = null, array $options = null): RedisRawClientInterface
    {
        if (is_array($hostnames)) {
            $this->redis = new Client($hostnames, $options);
        } else {
            $this->redis = new Client([
                'scheme' => 'tcp',
                'host' => $hostnames,
                'port' => $port,
                'database' => $db,
                'password' => $password,
            ]);
        }
        $this->redis->connect();
        return $this;
    }

    public function multi(bool $usePipeline = false)
    {
        return $this->redis->pipeline();
    }

    public function rawCommand(string $command, array $arguments)
    {
        $preparedArguments = $this->prepareRawCommandArguments($command, $arguments);
        $rawResult = $this->redis->executeRaw($preparedArguments);
        $this->validateRawCommandResults($rawResult);
        return $this->normalizeRawCommandResult($rawResult);
    }

}

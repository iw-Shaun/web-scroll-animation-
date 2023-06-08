<?php

namespace App\LineUtils;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;


class LINEBotExt extends LINEBot
{
    const DEFAULT_ENDPOINT_BASE = 'https://api.line.me';

    /** @var string */
    private $channelSecret;
    /** @var string */
    private $endpointBase;
    /** @var HTTPClient */
    private $httpClient;

    /**
     * LINEBot constructor.
     *
     * @param HTTPClient $httpClient HTTP client instance to use API calling.
     * @param array $args Configurations.
     */
    public function __construct(HTTPClient $httpClient, array $args)
    {
        // Initiate the parent class.
        parent::__construct($httpClient, $args);

        // Copy the things from parent.
        $this->httpClient = $httpClient;
        $this->channelSecret = $args['channelSecret'];

        $this->endpointBase = LINEBot::DEFAULT_ENDPOINT_BASE;
        if (!empty($args['endpointBase'])) {
            $this->endpointBase = $args['endpointBase'];
        }
    }

    /**
     * Get all LIFF apps.
     *
     * @return Response
     */
    public function getLiffApps()
    {
        return $this->httpClient->get($this->endpointBase . '/liff/v1/apps');
    }

    /**
     * Add a new LIFF app.
     *
     * @param string $type compact|tall|full
     * @param string $url
     * @param string $description
     * @return Response
     */
    public function addLiffApp($type, $url, $description)
    {
        return $this->httpClient->post($this->endpointBase . '/liff/v1/apps', [
            'view' => [
                'type' => $type,
                'url' => $url
            ],
            'description' => $description
        ]);
    }

    /**
     * Update LIFF app by ID.
     *
     * @param string $liffId xxxxx-ooooo
     * @param string $type compact|tall|full
     * @param string $url
     * @param string $description
     * @return Response
     */
    public function updateLiffApp($liffId, $type, $url, $description)
    {
        return $this->httpClient->put($this->endpointBase . "/liff/v1/apps/{$liffId}", [
            'view' => [
                'type' => $type,
                'url' => $url
            ],
            'description' => $description
        ]);
    }

    /**
     * Delete LIFF app by ID.
     *
     * @param string $liffId xxxxx-ooooo
     * @return Response
     */
    public function deleteLiffApp($liffId)
    {
        return $this->httpClient->delete($this->endpointBase . "/liff/v1/apps/{$liffId}");
    }
}

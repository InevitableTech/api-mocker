<?php

use Behat\Behat\Context\Context;
use Genesis\Api\Mocker\Service\Curl;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @BeforeScenario
     */
    public function purgeMocks()
    {
        Curl::sendRequest('post', 'http://localhost:8989/?purge=true');
    }
}

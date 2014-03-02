<?php

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\MinkExtension\Context\MinkDictionary;

require_once('PHPUnit/Util/Filesystem.php');
require_once('PHPUnit/Autoload.php');
require_once('PHPUnit/Framework/Assert/Functions.php');

class FeatureContext extends BehatContext
{
    use MinkDictionary;

    /**
     * @Then /^status code should be (\d+)$/
     */
    public function statusCodeShouldBe($code)
    {
        assertEquals($code, $this->getSession()->getStatusCode());
    }
}

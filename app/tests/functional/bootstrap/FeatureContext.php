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



    private function elementHasClass($selector, $class)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);

        return $element->hasAttribute('class') && preg_match('/'.$class.'/', $element->getAttribute('class'));
    }

    /**
     * @Then /^"([^"]*)" element should have class "([^"]*)"$/
     */
    public function elementShouldHaveClass($selector, $class)
    {
        assertTrue($this->elementHasClass($selector, $class));
    }

    /**
     * @Then /^"([^"]*)" element should not have class "([^"]*)"$/
     */
    public function elementShouldNotHaveClass($selector, $class)
    {
        assertFalse($this->elementHasClass($selector, $class));
    }
}

<?php

use Behat\Behat\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;

require_once('PHPUnit/Util/Filesystem.php');
require_once('PHPUnit/Autoload.php');
require_once('PHPUnit/Framework/Assert/Functions.php');

class FeatureContext extends MinkContext
{
    /**
     * Checks the response's status code
     *
     * @Then /^status code should be (\d+)$/
     *
     * @return void
     */
    public function statusCodeShouldBe($code)
    {
        assertEquals($code, $this->getSession()->getStatusCode());
    }



    /**
     * Determines if element has class
     *
     * @return void
     */
    private function elementHasClass($selector, $class)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);

        var_dump($element->getAttribute('class'));
        return $element->hasAttribute('class') && preg_match('/'.$class.'/', $element->getAttribute('class'));
    }



    /**
     * Checks if element has class
     *
     * @Then /^"([^"]*)" element should have class "([^"]*)"$/
     *
     * @return void
     */
    public function elementShouldHaveClass($selector, $class)
    {
        assertTrue($this->elementHasClass($selector, $class));
    }



    /**
     * Checks if element doesn't have class
     *
     * @Then /^"([^"]*)" element should not have class "([^"]*)"$/
     *
     * @return void
     */
    public function elementShouldNotHaveClass($selector, $class)
    {
        assertFalse($this->elementHasClass($selector, $class));
    }
}

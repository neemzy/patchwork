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
     * Checks an element has a class
     *
     * @Then /^"([^"]*)" element should have class "([^"]*)"$/
     *
     * @return void
     */
    public function elementShouldHaveClass($selector, $class)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);

        assertTrue($element && $element->hasClass($class));
    }



    /**
     * Checks an element doesn't have a class
     *
     * @Then /^"([^"]*)" element should not have class "([^"]*)"$/
     *
     * @return void
     */
    public function elementShouldNotHaveClass($selector, $class)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);

        assertFalse($element && $element->hasClass($class));
    }



    /**
     * Checks an element is visible
     *
     * @Then /^"([^"]*)" element should be visible$/
     *
     * @return void
     */
    public function elementShouldBeVisible($selector)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);

        assertTrue($element && $element->isVisible());
    }



    /**
     * Checks an element is hidden
     *
     * @Then /^"([^"]*)" element should be visible$/
     *
     * @return void
     */
    public function elementShouldBeHidden($selector)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);

        assertFalse($element && $element->isVisible());
    }
}

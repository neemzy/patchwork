<?php

use Behat\Behat\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ElementNotFoundException;

require_once('PHPUnit/Util/Filesystem.php');
require_once('PHPUnit/Autoload.php');
require_once('PHPUnit/Framework/Assert/Functions.php');

class FeatureContext extends MinkContext
{
    /**
     * Waits for n seconds
     *
     * @Then /^I wait (\d+) seconds?$/
     *
     * @return void
     */
    public function iWaitSeconds($seconds)
    {
        $this->getSession()->getDriver()->wait($seconds * 1000, null);
    }



    /**
     * Takes a screenshot
     *
     * @Then /^take a screenshot$/
     *
     * @return void
     */
    public function takeAScreenshot()
    {
        file_put_contents(__DIR__.'/../../../../'.uniqid().'.png', $this->getSession()->getDriver()->getScreenshot());
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
        $session = $this->getSession();
        $page = $session->getPage();
        $element = $page->find('css', $selector);

        if (!$element) {
            throw new ElementNotFoundException($session, 'Element "'.$selector.'"');
        }

        assertTrue($element->isVisible());
    }



    /**
     * Checks an element is hidden
     *
     * @Then /^"([^"]*)" element should be hidden$/
     *
     * @return void
     */
    public function elementShouldBeHidden($selector)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $element = $page->find('css', $selector);

        if (!$element) {
            throw new ElementNotFoundException($session, 'Element "'.$selector.'"');
        }

        assertFalse($element->isVisible());
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
        $session = $this->getSession();
        $page = $session->getPage();
        $element = $page->find('css', $selector);

        if (!$element) {
            throw new ElementNotFoundException($session, 'Element "'.$selector.'"');
        }

        assertTrue($element->hasClass($class));
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
        $session = $this->getSession();
        $page = $session->getPage();
        $element = $page->find('css', $selector);

        if (!$element) {
            throw new ElementNotFoundException($session, 'Element "'.$selector.'"');
        }

        assertFalse($element->hasClass($class));
    }
}

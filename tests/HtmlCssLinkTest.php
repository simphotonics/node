<?php

namespace Simphotonics\Dom\Tests;

use PHPUnit\Framework\TestCase;

use Simphotonics\Dom\HtmlCssLink;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlCssLink using URI's with
 * different format.
 */
class HtmlCssLinkTest extends TestCase
{
    /**
     * Simulate home uri
     * @var string
     */
    private $emptyUri    = "/";

    /**
     * Simulate home uri with *.php ending.
     * @var string
     */
    private $homeUri     = "/Index.php";

    /**
     * Standard uri
     * @var string
     */
    private $standardUri = "https:://www.simphotonics.com/user/LoginForm.php";

    /**
     * Laravel style uri
     * @var string
     */
    private $laravelUri  = "/company/profile/about-us";

    /**
     * Uri containig query
     * @var string
     */
    private $queryUri    = "https:://simphotonics.com/post?'.
    'url=http://google.com/&amp;message=This%20is%20my%20post";

    public static function setUpBeforeClass(): void
    {
        HtmlCssLink::registerElements(['link' => 'inline']);
    }

    public function testEmptyUri()
    {
        $this->checkCssLink($this->emptyUri, '/style/Index.css');
    }

    public function testHomeUri()
    {
        $this->checkCssLink($this->homeUri, '/style/Index.css');
    }

    public function testStandardUri()
    {
        $this->checkCssLink($this->standardUri, '/style/LoginForm.css');
    }

    public function testLaravelUri()
    {
        $this->checkCssLink($this->laravelUri, '/style/about-us.css');
    }

    public function testQueryUri()
    {
        $this->checkCssLink($this->queryUri, '/style/post.css');
    }

    public function testSetCssFolder()
    {
        $cssLink = new HtmlCssLink('about-us');
        $cssLink->setCssFolder('cssFolder');
        $this->assertEquals("<link rel=\"stylesheet\" type=\"text/css\"".
        " href=\"cssFolder/about-us.css\" media=\"all\"/>", "$cssLink");
    }

    /**
     * Tests title string.
     * @param  string $uri Static variable
     * @return void
     */
    private function checkCssLink($uri, $expectedPath)
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $cssLink = new HtmlCssLink();
        $this->assertEquals("<link rel=\"stylesheet\" type=\"text/css\"".
        " href=\"$expectedPath\" media=\"all\"/>", "$cssLink");
    }
}

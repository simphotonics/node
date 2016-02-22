<?php

namespace Simphotonics\Dom\Tests;

use Simphotonics\Dom\HtmlTitle;

/**
 * @author D Reschner <d.reschner@simphotonics.com>
 * @copyright 2016 Simphotonics
 * Description: Tests Simphotonics\HtmlTitle using URI's with
 * different format.
 */
class HtmlTitleTest extends \PHPUnit_Framework_TestCase
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
    private $standard_uri = "https:://www.simphotonics.com/user/LoginForm.php";

    /**
     * Laravel style uri
     * @var string
     */
    private $laravelUri  = "/company/profile/about-us";

    /**
     * Uri containig query
     * @var string
     */
    private $queryUri    = "https:://simphotonics.com/post?url=http://google.com/&amp;message=This%20is%20my%20post";

    public function __construct()
    {
        HtmlTitle::registerElements(['title' => 'empty']);
    }

    public function testEmptyUri()
    {
        $this->template($this->emptyUri);
    }

    public function testHomeUri()
    {
        $this->template($this->homeUri);
    }

    public function testStandardUri()
    {
        $this->template($this->standarUri);
    }

    public function testLaravelUri()
    {
        $this->template($this->laravelUri);
    }

    public function testQueryUri()
    {
        $this->template($this->queryUri);
    }

    /**
     * Tests title string.
     * @param  string $uri Static variable
     * @return void
     */
    private function template($uri)
    {
        $_SERVER[REQUEST_URI] = $uri;
        $title = new HtmlTitle('SIMPHOTONICS');
        $this->assertEquals("<title />", "$title");
    }
}

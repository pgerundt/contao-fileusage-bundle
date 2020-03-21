<?php
    namespace Efficient\ContaoFileUsageBundle\Tests;

    use Efficient\ContaoFileUsageBundle\ContaoFileUsageBundle;
    use PHPUnit\Framework\TestCase;

    class ContaoFileUsageBundleTest extends TestCase
    {
        public function testCanBeInstantiated()
        {
            $bundle = new ContaoFileUsageBundle();
            $this->assertInstanceOf('Efficient\ContaoFileUsageBundle\ContaoFileUsageBundle', $bundle);
        }
    }

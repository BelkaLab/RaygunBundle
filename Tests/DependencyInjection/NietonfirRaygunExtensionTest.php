<?php

/*
 * This file is part of the Raygunbundle package.
 *
 * (c) nietonfir <nietonfir@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nietonfir\RaygunBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Nietonfir\RaygunBundle\DependencyInjection\NietonfirRaygunExtension;
use Symfony\Component\Yaml\Parser;

class NietonfirRaygunExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    public function testDefaults()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new NietonfirRaygunExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);

        $this->assertAlias('nietonfir_raygun.client', 'raygun.client');
        $this->assertAlias('nietonfir_raygun.monolog_handler', 'raygun.handler');
        $this->assertParameter('1234567', 'nietonfir_raygun.api_key');
        $this->assertParameter(true, 'nietonfir_raygun.async');
        $this->assertParameter(false, 'nietonfir_raygun.debug_mode');
        $this->assertHasDefinition('nietonfir_raygun.monolog_handler');
        $this->assertHasDefinition('nietonfir_raygun.twig_extension');
    }

    public function testCustomSettings()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new NietonfirRaygunExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);

        $this->assertAlias('nietonfir_raygun.client', 'raygun.client');
        $this->assertAlias('nietonfir_raygun.monolog_handler', 'raygun.handler');
        $this->assertParameter('987655', 'nietonfir_raygun.api_key');
        $this->assertParameter(false, 'nietonfir_raygun.async');
        $this->assertParameter(true, 'nietonfir_raygun.debug_mode');
        $this->assertHasDefinition('nietonfir_raygun.monolog_handler');
        $this->assertHasDefinition('nietonfir_raygun.twig_extension');
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
api_key: 1234567
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getFullConfig()
    {
        $yaml = <<<EOF
api_key: 987655
async: false
debug_mode: true
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    /**
     * @param string $value
     * @param string $key
     */
    private function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    /**
     * @param mixed  $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }
}

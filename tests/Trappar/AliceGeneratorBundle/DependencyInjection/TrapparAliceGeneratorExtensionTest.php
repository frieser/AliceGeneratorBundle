<?php

namespace Trappar\AliceGeneratorBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Trappar\AliceGenerator\Exception\RuntimeException;
use Trappar\AliceGeneratorBundle\DependencyInjection\TrapparAliceGeneratorExtension;

class TrapparAliceGeneratorExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return array(
            new TrapparAliceGeneratorExtension()
        );
    }

    public function testAutoDetectionDisabled()
    {
        $this->setBundles();
        $this->load([
            'metadata' => [
                'auto_detection' => false
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'trappar_alice_generator.metadata.file_locator',
            0,
            []
        );
    }

    public function testWithCustomDirectory()
    {
        $this->setBundles();
        $this->load([
            'metadata' => [
                'auto_detection' => false,
                'directories' => [
                    'myname' => [
                        'namespace_prefix' => 'some_prefix',
                        'path'             => '@TestBundle'
                    ]
                ]
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'trappar_alice_generator.metadata.file_locator',
            0,
            ['some_prefix' => realpath(__DIR__ . '/../SymfonyApp/TestBundle')]
        );
    }

    public function testAutoDetectionEnabled()
    {
        $this->setBundles();
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'trappar_alice_generator.metadata.file_locator',
            0
        );
    }

    public function testWithInvalidBundle()
    {
        $this->expectException(RuntimeException::class);

        $this->setBundles();
        $this->load([
            'metadata' => [
                'directories' => [
                    'myname' => [
                        'namespace_prefix' => 'some_prefix',
                        'path'             => '@Blah'
                    ]
                ]
            ]
        ]);
    }

    public function testWithCustomYamlOptions()
    {
        $this->setBundles();
        $this->load([
            'yaml' => [
                'indent' => 1
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'trappar_alice_generator.yaml_writer',
            0,
            3
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'trappar_alice_generator.yaml_writer',
            1,
            1
        );
    }

    private function setBundles()
    {
        $this->setParameter('kernel.bundles', [
            'TestBundle' => 'Trappar\AliceGeneratorBundle\Tests\SymfonyApp\TestBundle\TestBundle'
        ]);
    }
}

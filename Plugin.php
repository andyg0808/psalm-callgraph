<?php

namespace Andyg0808\PsalmCallgraph;

use Psalm\Plugin\EventHandler\AfterFunctionCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionCallAnalysisEvent;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\Context;
use SimpleXMLElement;

class Plugin implements PluginEntryPointInterface, AfterFunctionCallAnalysisInterface, AfterMethodCallAnalysisInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null): void
    {
        $file = fopen("./callers.csv", "w");
        fclose($file);
        // This is plugin entry point. You can initialize things you need here,
        // and hook them into psalm using RegistrationInterface
        //
        // Here's some examples:
        // 1. Add a stub file
        // ```php
        // $psalm->addStubFile(__DIR__ . '/stubs/YourStub.php');
        // ```
        foreach ($this->getStubFiles() as $file) {
            $psalm->addStubFile($file);
        }

        // Psalm allows arbitrary content to be stored under you plugin entry in
        // its config file, psalm.xml, so your plugin users can put some configuration
        // values there. They will be provided to your plugin entry point in $config
        // parameter, as a SimpleXmlElement object. If there's no configuration present,
        // null will be passed instead.
    }

    /** @return list<string> */
    private function getStubFiles(): array
    {
        return glob(__DIR__ . '/stubs/*.phpstub') ?: [];
    }

    public static function afterFunctionCallAnalysis(AfterFunctionCallAnalysisEvent $event): void {
        $file = fopen("./callers.csv", "a");
        $call = $event->getFunctionId();
        $madeBy = self::getCaller($event->getContext());
        fputcsv($file, [$madeBy, $call]);
        fclose($file);
    }

    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void {
        $file = fopen("./callers.csv", "a");
        $call = $event->getMethodId();
        $madeBy = self::getCaller($event->getContext());
        fputcsv($file, [$madeBy, $call]);
        fclose($file);
    }

    private static function getCaller(Context $context): string {
        return $context->calling_method_id ?: $context->calling_function_id ?: "";
    }
}

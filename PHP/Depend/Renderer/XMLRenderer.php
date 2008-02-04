<?php
require_once 'PHP/Depend/Renderer.php';

class PHP_Depend_Renderer_XMLRenderer implements PHP_Depend_Renderer
{
    public function render(Iterator $metrics)
    {
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        $root = $dom->appendChild($dom->createElement('PHPDepend'));
        $pkgs = $root->appendChild($dom->createElement('Packages'));
        
        foreach ($metrics as $metric) {
            if ($metric->getName() === PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE) {
                continue;
            }
            
            $pkg = $pkgs->appendChild($dom->createElement('Package'));
            $pkg->setAttribute('name', $metric->getName());
            
            $stats = $pkg->appendChild($dom->createElement('Stats'));
            
            $stats->appendChild($dom->createElement('TotalClasses'))
                  ->appendChild($dom->createTextNode($metric->getTC()));
            $stats->appendChild($dom->createElement('ConcreteClasses'))
                  ->appendChild($dom->createTextNode($metric->getCC()));
            $stats->appendChild($dom->createElement('AbstractClasses'))
                  ->appendChild($dom->createTextNode($metric->getAC()));
            $stats->appendChild($dom->createElement('Ca'))
                  ->appendChild($dom->createTextNode($metric->getCA()));
            $stats->appendChild($dom->createElement('Ce'))
                  ->appendChild($dom->createTextNode($metric->getCE()));
            $stats->appendChild($dom->createElement('A'))
                  ->appendChild($dom->createTextNode($metric->getA()));
            $stats->appendChild($dom->createElement('I'))
                  ->appendChild($dom->createTextNode($metric->getI()));
            $stats->appendChild($dom->createElement('D'))
                  ->appendChild($dom->createTextNode($metric->getD()));
            // TODO: V
            
            $cc = $pkg->appendChild($dom->createElement('ConcreteClasses'));
            foreach ($metric->getConcreteClasses() as $class) {
                $c = $cc->appendChild($dom->createElement('Class'));
                // TODO: Source file
                $c->appendChild($dom->createTextNode($class->getName()));
            }
            
            $ac = $pkg->appendChild($dom->createElement('AbstractClasses'));
            foreach ($metric->getAbstractClasses() as $class) {
                $c = $cc->appendChild($dom->createElement('Class'));
                // TODO: Source file
                $c->appendChild($dom->createTextNode($class->getName()));
            }
            
            $ce = $pkg->appendChild($dom->createElement('DependsUpon'));
            foreach ($metric->getEfferentCouplings() as $dep) {
                $p = $ce->appendChild($dom->createElement('Package'));
                $p->appendChild($dom->createTextNode($dep->getName()));
            }
            
            $ce = $pkg->appendChild($dom->createElement('UserBy'));
            foreach ($metric->getAfferentCouplings() as $dep) {
                $p = $ce->appendChild($dom->createElement('Package'));
                $p->appendChild($dom->createTextNode($dep->getName()));
            }
        }
        
        echo $dom->saveXML();
    }
}
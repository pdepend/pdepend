<?php

use Gregwar\RST\Directive;
use Gregwar\RST\Environment;
use Gregwar\RST\HTML\Nodes\ParagraphNode;
use Gregwar\RST\Parser;

class ParagraphClassNode extends ParagraphNode
{
    /**
     * @var string
     */
    protected $class;

    public function __construct($value = null, $class = null)
    {
        parent::__construct($value);
        $this->class = $class;
    }

    public function render()
    {
        $text = $this->value;
        $class = $this->class;

        if (trim($text)) {
            return '<p'.($class ? ' class="'.$class.'"' : '').'>'.$text.'</p>';
        } else {
            return '';
        }
    }
}

class ClassDirective extends Directive
{
    /**
     * Get the directive name
     */
    public function getName()
    {
        return 'class';
    }

    /**
     * @param Parser $parser
     * @param ParagraphNode $node
     * @param mixed $variable
     * @param mixed $data
     * @param array $options
     */
    public function process(Parser $parser, $node, $variable, $data, array $options)
    {
        $node = new ParagraphClassNode($node->getValue(), $data);

        parent::process($parser, $node, $variable, $data, $options);
    }
}

class PhpMdEnvironment extends Environment
{
    public static $letters = array('=', '-', '`', '~', '*', '^', '"');

    /**
     * @var string
     */
    protected $baseHref;

    public function getBaseHref()
    {
        return $this->baseHref;
    }

    public function reset()
    {
        parent::reset();

        $this->baseHref = ltrim(getenv('BASE_HREF') ?: '', ':');
        $this->titleLetters = array(
            2 => '=',
            3 => '-',
            4 => '`',
            5 => '~',
            6 => '*',
            7 => '^',
            8 => '"',
        );
    }

    public function relativeUrl($url)
    {
        $root = substr($url, 0, 1) === '/';

        return ($root ? $this->getBaseHref().'/' : '').parent::relativeUrl($url);
    }
}

$changelogContent = file_get_contents(__DIR__.'/../../CHANGELOG.md');
$env = new PhpMdEnvironment;
$parser = new Parser($env);
$parser->registerDirective(new ClassDirective());

return array(
    'index'            => 'documentation/getting-started.html',
    'baseHref'         => $env->getBaseHref(),
    'cname'            => getenv('CNAME'),
    'websiteDirectory' => __DIR__.'/../../dist/website',
    'sourceDirectory'  => __DIR__.'/rst',
    'assetsDirectory'  => __DIR__.'/resources/web',
    'layout'           => __DIR__.'/resources/layout.php',
    'extensions'       => array(
        'rst' => function ($file) use ($parser, $changelogContent) {
            $content = file_get_contents($file);
            $content = str_replace(
                '.. include:: ../release/parts/latest.rst',
                $changelogContent,
                $content
            );
            $content = $parser->parse($content);
            // Rewrite links anchors
            $content = preg_replace_callback('/(<a id="[^"]+"><\/a>)\s*<h(?<level>[1-6])([^>]*>)(?<content>[\s\S]*)<\/h\\g<level>>/U', function ($match) {
                $level = $match['level'];
                $content = $match['content'];
                // Use content as anchor
                $hash = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($match['content'])));

                return "<a id=\"$hash\"></a>\n<h$level>$content</h$level>";
            }, $content);
            $content = preg_replace(
                '/phpmd-(\d+\.\S+)/',
                '<a href="https://github.com/phpmd/phpmd/releases/tag/$1" title="$0 release">$0</a>',
                $content
            );

            return $content;
        },
    ),
);

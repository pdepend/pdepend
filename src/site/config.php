<?php

use Gregwar\RST\Directive;
use Gregwar\RST\Environment;
use Gregwar\RST\HTML\Nodes\ParagraphNode;
use Gregwar\RST\Parser;

class ParagraphClassNode extends ParagraphNode
{
    private ?string $class;

    public function __construct(mixed $value = null, ?string $class = null)
    {
        parent::__construct($value);
        $this->class = $class;
    }

    public function render(): string
    {
        $text = trim($this->value);
        $class = $this->class;

        if ($text === '') {
            return '';
        }

        return '<p' . ($class ? ' class="' . $class . '"' : '') . '>' . $text . '</p>';
    }
}

class ClassDirective extends Directive
{
    /**
     * Get the directive name
     */
    public function getName(): string
    {
        return 'class';
    }

    /**
     * @param ParagraphNode $node
     * @param ?string $data
     * @param array<mixed> $options
     */
    public function process(Parser $parser, $node, mixed $variable, mixed $data, array $options): void
    {
        $node = new ParagraphClassNode($node->getValue(), $data);

        parent::process($parser, $node, $variable, $data, $options);
    }
}

class PHPDependEnvironment extends Environment
{
    /** @var list<string> */
    public static $letters = ['=', '-', '`', '~', '*', '^', '"'];

    protected string $baseHref;

    public function getBaseHref(): string
    {
        return $this->baseHref;
    }

    public function reset(): void
    {
        parent::reset();

        $this->baseHref = ltrim(getenv('BASE_HREF') ?: '', ':');
        $this->titleLetters = [
            2 => '=',
            3 => '-',
            4 => '`',
            5 => '~',
            6 => '*',
            7 => '^',
            8 => '"',
        ];
    }

    /**
     * @param string $url
     */
    public function relativeUrl($url): string
    {
        $root = substr($url, 0, 1) === '/';

        return ($root ? $this->getBaseHref() . '/' : '') . parent::relativeUrl($url);
    }
}

$env = new PHPDependEnvironment();
$parser = new Parser($env);
$parser->registerDirective(new ClassDirective());

return [
    'index' => 'news.html',
    'baseHref' => $env->getBaseHref(),
    'cname' => getenv('CNAME'),
    'websiteDirectory' => __DIR__ . '/../../dist/website',
    'sourceDirectory' => __DIR__ . '/rst',
    'assetsDirectory' => __DIR__ . '/resources/web',
    'layout' => __DIR__ . '/resources/layout.php',
    'publishPhar' => 'pdepend/pdepend',
    'extensions' => [
        'rst' => function ($file) use ($parser) {
            $parser->getEnvironment()->setCurrentDirectory(dirname($file));
            $content = $parser->parseFile($file);
            // Rewrite links anchors
            $content = preg_replace_callback('/(<a id="[^"]+"><\/a>)\s*<h(?<level>[1-6])([^>]*>)(?<content>[\s\S]*)<\/h\\g<level>>/U', function ($match) {
                $level = $match['level'];
                $content = $match['content'];
                // Use content as anchor
                $hash = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($match['content'])));

                return "<a id=\"$hash\"></a>\n<h$level>$content</h$level>";
            }, $content) ?: $content;
            $content = preg_replace(
                '/pdepend-(\d+\.\S+)/',
                '<a href="https://github.com/pdepend/pdepend/releases/tag/$1" title="$0 release">$0</a>',
                $content
            ) ?: $content;

            return preg_replace('/^\s*<hr\s*\/?>/', '', $content);
        },
    ],
];

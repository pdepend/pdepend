<?php

class PHPFilterIterator extends FilterIterator
{
    public function accept() 
    {
        return ( substr($this->getInnerIterator()->current(), -4, 4) === '.php' );
    }
}

if ( isset( $argv[1] ) )
{
    $dir = $argv[1];
}
else
{
    $dir = dirname( __FILE__ ) . '/data/code-5.2.x';
}

$it = new PHPFilterIterator( 
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator( $dir )
    )
);

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Code/DefaultBuilder.php';
require_once 'PHP/Depend/Code/NodeVisitor.php';
require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';
require_once 'PHP/Depend/Metrics/PackageMetricsVisitor.php';


$classes  = array();
$packages = array();

$builder = new PHP_Depend_Code_DefaultBuilder();

foreach ( $it as $file ) 
{
    $parser = new PHP_Depend_Parser(
        new PHP_Depend_Code_Tokenizer_InternalTokenizer($file), $builder
    );
    $parser->parse();
}

$visitor = new PHP_Depend_Metrics_PackageMetricsVisitor();

foreach ($builder as $pkg) {
    $pkg->accept($visitor);
}

$packages = $visitor->getMetrics();

print_r($packages);

$size = 40;

$im = imagecreatetruecolor( 13 * $size, 13 * $size );

$red    = imagecolorallocate( $im, 67, 118, 16 );
$orange = imagecolorallocate( $im, 252, 175, 62 );
$green  = imagecolorallocate( $im, 139, 226, 52 );
$white  = imagecolorallocate( $im, 255, 255, 255 );
$lgray  = imagecolorallocate( $im, 186, 189, 182 );
$gray   = imagecolorallocate( $im, 85, 87, 83 );
$dgray  = imagecolorallocate( $im, 46, 52, 54 );

$bias = 0.1;

imagefill( $im, 0, 0, $white );
imagerectangle( $im, $size, $size, 12 * $size, 12 * $size, $gray );

for ( $n = 0.0, $i = ( $size + ( $size / 2 ) ); $i < ( 12 * $size ); $n += 0.1, $i += $size )
{
    imageline( $im, $size, $i, ( 12 * $size ), $i, $gray );
    imageline( $im, $i, $size, $i, ( 12 * $size ), $gray );
    
    imageline( $im, ( $size - 2 ), $i, ( $size + 2 ), $i, $dgray );
    imageline( $im, $i, ( ( 12 * $size ) - 2 ), $i, ( ( 12 * $size ) + 2 ), $dgray );
    
    $text = sprintf( '%.1f', $n ); 
    
    imagestring( $im, 1, floor( $size / 2 ), ( 13 * $size ) - $i - 4, $text, $dgray );
    imagestring( $im, 1, $i - 4, ceil( $size * 12.25 ), $text, $dgray );
}

imagestring( $im, 2, 6 * $size, floor( 12.5 * $size ), "Abstraction", $dgray );
imagestringup( $im, 2, floor( $size / 10 ), floor( 7.5 * $size ), "Instability", $dgray );

imageline( $im, $size, $size, ( 12 * $size ), ( 12 * $size ), $red );

//$step = ( maxPackage( $packages ) / 100 );

foreach ( $packages as $package )
{
    $sum      = ( $package->getCC() + $package->getAC() );
    $diameter = ( sqrt( $sum ) * $size ) / sqrt( $size );
    
    $A = $package->getA();
    $I = $package->getI();
    
    $offsetX = $size + ceil( $A * ( 10 * $size ) ) + ( $size / 2 );
    $offsetY = $size + ceil( 10.5 * $size ) + ( $I * ( -10 * $size ) );
    
    if ( abs( ( $A + $I ) - 1 ) < $bias )
    {
        $color = $green;
    }
    else
    {
        $color = $orange;
    }

    imagefilledarc( $im, $offsetX, $offsetY, $diameter, $diameter, 0, 0, $color, IMG_ARC_PIE );
    imagearc( $im, $offsetX, $offsetY, $diameter, $diameter, 0, 0, $dgray );
    
    imagestring($im, 1, $offsetX, $offsetY - round($diameter / 2), $package->getName(), $dgray);
}

imagepng( $im, '/home/manu/foo.png' );
imagedestroy( $im );

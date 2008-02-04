<?php
require_once 'PHP/Depend/Renderer.php';
require_once 'PHP/Depend/Metrics/PackageMetricsVisitor.php';

class PHP_Depend_Renderer_GdChartRenderer implements PHP_Depend_Renderer
{
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function render(Iterator $metrics)
    {
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

        foreach ( $metrics as $metric )
        {
            if ($metric->getName() === PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE) {
                continue;
            }
            
            $sum      = ( $metric->getCC() + $metric->getAC() );
            $diameter = ( sqrt( $sum ) * $size ) / sqrt( $size );

            $A = $metric->getA();
            $I = $metric->getI();

            $offsetX = $size + ceil( $A * ( 10 * $size ) ) + ( $size / 2 );
            $offsetY = $size + ceil( 10.5 * $size ) + ( $I * ( -10 * $size ) );

            if ( $metric->getD() < $bias )
            {
                $color = $green;
            }
            else
            {
                $color = $orange;
            }

            imagefilledarc( $im, $offsetX, $offsetY, $diameter, $diameter, 0, 0, $color, IMG_ARC_PIE );
            imagearc( $im, $offsetX, $offsetY, $diameter, $diameter, 0, 0, $dgray );

            imagestring($im, 2, $offsetX + ceil($diameter / 2), $offsetY - $diameter, $metric->getName(), $dgray);
        }

        imagepng( $im, $this->file );
        imagedestroy( $im );
    }
}
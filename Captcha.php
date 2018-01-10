<?php
/**
 * Class Captcha
 * Author : 李聪leo
 * Site : www.lcgod.com
 */
class Captcha {
    //画布资源
    private $img;
    //验证码文字
    private $code = '';
    //验证码字体文件
    public $font;
    //图片宽度
    public $width = 400;
    //图片高度
    public $height = 300;
    //字体大小
    public $size = 50;
    //背景颜色
    public $bgColor = '#ffffff';
    //字体颜色
    public $fontColor;
    //验证码位数
    public $length = 4;
    //验证码种子
    public $seed = '0123456789abcdefghijklmnopqrstuvwxzyABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * 输出验证码图片
     */
    public function show() {
        //验证GD库是否开启
        if( !$this->checkGD() ) {
            return FALSE;
        }
        //设置字体文件路径
        if( empty($this->font) ) {
            $this->font = __DIR__ . '/font.ttf';
        }
        //制作背景与验证码文字
        $this->createCode();
        //添加图片干扰
        $this->createInterference();
        //发送头标明文件类型为image
        header( 'Content-type:image/png' );
        //输出png格式的验证码图片
        imagepng( $this->img );
        //销毁图片资源
        imagedestroy( $this->img );
        exit;
    }

    /**
     * 制作背景与验证码文字
     */
    private function createCode() {
        //创建真彩画板
        $this->img = imagecreatetruecolor( $this->width, $this->height );
        //填充背景颜色
        imagefill( $this->img, 0, 0, hexdec( $this->bgColor ) );
        //生成验证码文字
        for( $i = 0; $i < $this->length; $i++ ) {
            $this->code .= $this->seed [ mt_rand( 0, strlen( $this->seed ) - 1 ) ];
        }
        //将验证码存入session中
        $_SESSION['code'] = $this->code;
        //判断用户是否指定字体颜色
        if( !empty( $this->fontColor ) ) {
            //调色
            $this->fontColor = imagecolorallocate( $this->img, hexdec( substr( $this->fontColor, 1,2 ) ), hexdec( substr( $this->fontColor, 3,2 ) ), hexdec( substr( $this->fontColor, 5,2 ) ) );
            for( $i = 0; $i < $this->length; $i++ ) {
                //逐个输出文字
                imagettftext( $this->img, $this->size, mt_rand( -30, 30 ), $this->width / $this->length * $i + mt_rand( $this->size / 3, $this->size ), ( $this->height + $this->size ) / 2, $this->fontColor, $this->font, $this->code[$i] );
            }
        }else{
            for( $i = 0; $i < $this->length; $i++ ) {
                //随机颜色
                $color = imagecolorallocate( $this->img, mt_rand( 50, 200 ), mt_rand( 50, 200 ), mt_rand(50, 200 ) );
                //逐个输出文字
                imagettftext( $this->img, $this->size, mt_rand( -30, 30 ), $this->width / $this->length * $i + mt_rand( $this->size / 4, $this->size / 3 ), ( $this->height + $this->size ) / 2, $color, $this->font, $this->code[$i] );
            }
        }
    }

    /**
     * 制作图片干扰
     */
    private function createInterference() {
        //制作线条方格
        $line_color = "#dcdcdc";
        $color      = imagecolorallocate( $this->img, hexdec( substr( $line_color, 1, 2 ) ), hexdec( substr( $line_color, 3, 2 ) ), hexdec( substr( $line_color, 5, 2 ) ) );
        $l          = $this->height / 5;
        for ( $i = 1;$i < $l;$i ++ ) {
            $step = $i * 5;
            imageline( $this->img, 0, $step, $this->width, $step, $color );
        }
        $l = $this->width / 10;
        for ( $i = 1;$i < $l;$i ++ ) {
            $step = $i * 10;
            imageline( $this->img, $step, 0, $step, $this->height, $color );
        }
        //制作像素点
        $l = $this->width * $this->height / 25;
        for ( $i = 0;$i < $l; $i++ ) {
            $color = imagecolorallocate( $this->img, mt_rand( 0, 255 ), mt_rand( 0, 255 ), mt_rand( 0, 255 ) );
            imagesetpixel( $this->img, mt_rand( 0, $this->width), mt_rand(0, $this->height), $color);
        }
        //制作圆弧
        for ( $i = 0;$i < 5;$i ++ ) {
            // 设置画线宽度
            imagearc( $this->img, mt_rand( 0, $this->width ), mt_rand( 0, $this->height ), mt_rand( 0, $this->width ), mt_rand( 0, $this->height ), mt_rand( 0, 160 ), mt_rand( 0, 200 ), $color );
        }
    }

    /**
     * 验证GD库是否开启
     */
    private function checkGD() {
        return extension_loaded('gd') && function_exists("imagepng");
    }
}
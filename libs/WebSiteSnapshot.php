<?php

/*
 * Copyright 2018 DEVZONE TECH Ltd.
 * 
 * WebSiteSnapshot PHP class
 * Read web site address by domain name and create thumbnail image
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR 
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR 
 * IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * Free for non-commercial use. 
 * For commercial usage, please contact DEVZONE TECH Ltd. https://www.devzonetech.com/
 * 
 * SOFTWARE REQUIREMENTS:
 * PHP5 >= 5.5.0, PHP7
 * imagick module enabled
 * 
 * installed xvfb
 * installed wkhtmltopdf
 * installed convert
 */

define ('SNAPSHOTS_DIR', '/var/www/html/websitesnapshot/public/images/site_snapshots/');
define ('SNAPSHOTS_SITE_PATH', 'images/site_snapshots/'); // Without begining backslash
define ('TMP_DIR', '/tmp/');
define ('SITE_URL', 'http://localhost/websitesnapshot/public/'); // With ending backslash

class WebSiteSnapshot {

    private $domainName;
    private $siteUrl;
    private $pdfFilePath;
    private $uniqueFileName;
    private $width;
    private $height;

    public function __construct($domain, $width = null, $height = null) {

        $this->dirWriteable();
        $this->domainName = $domain;
        $this->width = $width;
        $this->height = $height;
    }

    private function dirWriteable() {
        if (!is_writable ( TMP_DIR )) {
            echo TMP_DIR . " is not writeable.\n";
            exit();
        }
        if (!is_writable ( SNAPSHOTS_DIR )) {
            echo SNAPSHOTS_DIR . " is not writeable.\n";
            exit();
        }
    }
    
    private function initSiteUrl($useHttps = false) {

        $protocol = 'http://';
        if ($useHttps) {
            $protocol = 'http://';
        }

        $this->siteUrl = $protocol . $this->domainName;

        $res = array();
        $options = array( 
            CURLOPT_RETURNTRANSFER => true,     // do not return web page 
            CURLOPT_HEADER         => false,    // do not return headers 
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects 
            CURLOPT_USERAGENT      => "spider", // who am i 
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect 
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect 
            CURLOPT_TIMEOUT        => 120,      // timeout on response 
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects 
        ); 
        $ch = curl_init( $this->siteUrl ); 
        curl_setopt_array( $ch, $options ); 
        $content = curl_exec( $ch ); 
        $err = curl_errno( $ch ); 
        $errmsg = curl_error( $ch ); 
        $header = curl_getinfo( $ch ); 
        curl_close( $ch ); 
        
        $this->siteUrl = $header['url'];
    }
    
    private function initFilesPath() {

        $this->uniqueFileName = md5($this->domainName);
        $this->pdfFilePath = TMP_DIR.$this->uniqueFileName.".pdf";
        $this->pngFilePath = SNAPSHOTS_DIR.$this->uniqueFileName.".png";
    }
    
    private function getSitePdfSnapshot() {

        $command = 'xvfb-run wkhtmltopdf '.$this->siteUrl.' '.$this->pdfFilePath.'  2>&1';              
        exec($command, $output, $ret);
               
        if ($ret) {
            echo "Error html to PDF conversion.\n";
            exit;
            return false;
        }

        return true;
    }
    
    private function convertToPngSnapshot() {

        if (!empty($this->width) && !empty($this->height)) {
            $resizeCommand = " -resize ".$this->width;
        }

        $command = "convert -density 110 -depth 8 -quality 95 -trim $resizeCommand ".$this->pdfFilePath." -append ".$this->pngFilePath;
        exec($command, $output, $ret);

        if ($ret){
            echo "Error converting.\n";
            return false;
        } else {
            unlink($this->pdfFilePath);
            if (!empty($this->width) && !empty($this->height)) {
                $image = new Imagick($this->pngFilePath);
                $image->cropImage($this->width, $this->height, 0, 0);
                $image->writeImage($this->pngFilePath);
            }
        }
        
        return true;
    }
    
    public function getSnapshotUrl($useHttps = false) {

        $this->initFilesPath();
        if (!file_exists($this->pngFilePath)) {
            $this->initSiteUrl($useHttps);
            $this->getSitePdfSnapshot();
            $this->convertToPngSnapshot();
        }

        return SITE_URL.SNAPSHOTS_SITE_PATH.$this->uniqueFileName.".png";
    }    
}


<?php
/**
 * CVExtractor - topITworks Hackathon 2016
 *
 * @author: Thomas Nguyen
 * @email: resize2011@gmail.com
 * @updated_at: 19-09-2016 20:50
 */

class ReadWord extends ParseDoc {

    private $_file;
    private $_doc_lang = 'ascii';
    private $_max_word_length = 100;

    protected $_cv_data = array();

    public function parse() {

        $text = $this->doc2Text();

        $text = $this->removeRomanIndex($text);

        $lines = $this->parseLines($text);

        $current_content_type = null;
        $contents = array();

        foreach($lines as $k=>$v) {
            $v = trim($v);

            //if($v == '') continue;

            if(in_array($v, $this->_content_title)) {
                $current_content_type = $v;
            } else {
                if ($current_content_type != null) {
                    $contents[$current_content_type][] = $v;
                } else {
                    // First paragraph
                    // Detect and set address !!It wonderful!!
                    if(!isset($this->_cv_data['profile']['address1'])) {
                        if ($this->detectedAddress($v)) {
                            $this->_cv_data['profile']['address1'] = $v;
                        }
                    }
                }
            }
        }

        $this->processContent($contents);
    }

    private function doc2Text() {

        if(file_exists($this->_file)){
            if(($fileHandle = fopen($this->_file, 'r')) !== false ) {

                $headers = fread($fileHandle, 0xa00);

                // 1 = (ord(n)*1) ; document has from 0 to 255 characters
                $n1 = ( ord($headers[0x21c]) - 1 );

                // 1 = ((ord(n)-8)*256) ; document has from 256 to 63743 characters
                $n2 = ( ( ord($headers[0x21d]) - 8 ) * 256 );

                // 1 = ((ord(n)*256)*256) ; document has from 63744 to 16775423 characters
                $n3 = ( ( ord($headers[0x21e]) * 256 ) * 256 );

                // 1 = (((ord(n)*256)*256)*256) ; document has from 16775424 to 4294965504 characters
                $n4 = ( ( ( ord($headers[0x21f]) * 256 ) * 256 ) * 256 );

                // total length of text in the document
                $length = ($n1 + $n2 + $n3 + $n4);

                $text =  fread($fileHandle, $length);

                if($this->checkUnicode($text)) {
                    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-16LE');
                    $this->_doc_lang = 'unicode';
                } else {
                    $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
                }

                $text = $this->reClean($text);

                return $text;

            }
        }
    }

    public function setFile($file, $not_check_ext = false) {

        $file = trim($file);

        if(isset($file) && !file_exists($file)) {
            throw new Exception('File not exist');
        }

        if(!$not_check_ext) {
            $p = pathinfo($file);
            $file_ext = $p['extension'];
            if ($file_ext != "doc") {
                throw new Exception('Invalid file type, please using Word97 (.doc)');
            }
        }
        $this->_file = $file;
    }

    private function checkUnicode($text) {

        if (strlen($text) != strlen(utf8_decode($text)))
        {
            return true;
        }
        return false;
    }

    private function reClean($text) {

        $arr = unpack("C*", $text);

        $i=0;
        $total = count($arr);

        $first_text = '';
        $proc_text = '';

        while($i<$total){

            $d = @$arr[$i];

            if($d == 224) { // Start remove arabic chars ഀࣆ...
                if($first_text == '') {
                    $first_text = $proc_text;
                }
                $proc_text = '';
            }

            if($d > 8) {
                $proc_text .= chr($d);
            }

            $i++;
        }

        // Remove long words
        $patterns = array(
            'long_words' => '/[^\s]{'.$this->_max_word_length.',}/'
        );

        $replacements = array(
            'long_words' => ''
        );

        $text = trim(preg_replace($patterns, $replacements, $first_text.$proc_text));

        return $text;
    }

    public function getCVData() {
        return $this->_cv_data;
    }

}
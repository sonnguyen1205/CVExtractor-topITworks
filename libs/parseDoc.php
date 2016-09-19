<?php
/**
 * CVExtractor - topITworks Hackathon 2016
 *
 * @author: Thomas Nguyen
 * @email: resize2011@gmail.com
 * @updated_at: 19-09-2016 20:50
 */

class ParseDoc extends RegexPattern {

    public function parseLines($text) {
        return preg_split($this->_pattern_new_lines, $text);
    }

    public function parseTimeRange($text, $pt = 0, $t = '\s(.*)') {
        $pattern = "/".$this->_pattern_time_range[$pt].$t."/m";
        preg_match($pattern, $text, $matches);
        return $matches;
    }

    public function parseRomanToArray($text) {
        return preg_split($this->_pattern_roman, $text);
    }

    public function removeRomanIndex($text) {
        return preg_replace($this->_pattern_roman, '', $text);
    }
    public function removeNumberIndex($text) {
        return trim(preg_replace($this->_pattern_line_num_index, '', $text));
    }
    public function removeLineIndex($text) {
        return trim(preg_replace($this->_pattern_line_index, '', $text));
    }

    public function detectedAddress($string) {
        preg_match($this->_pattern_check_address, $string, $matches);
        if(count($matches) == 3) {
            return true;
        } else {
            return false;
        }
    }

    function extract_email_address($string) {

        preg_match($this->_pattern_parse_email, $string, $matches);

        if(count($matches) > 0) {
            return $matches[0];
        }
        return '';
    }

    public function parseIndex($text) {

        $label = null;
        $value = null;

        preg_match_all($this->_pattern_index, $text, $matches);

        if (count($matches) > 2) {
            $label = @trim($matches[1][0]);
            $value = @trim($matches[2][0]);

            if($label != '' && $value != '') {
                return array($label, $value);
            }
        }

        return false;

    }

    /*
     * @param mixed $list_style : string format "%d."
     */
    public function reFormatList($content, $list_style = null) {
        $rs = array();
        $index = 1;
        foreach ($content as $k=>$li) {
            $li = $this->removeLineIndex($li);

            if($li == '') continue;

            if($list_style != null) {
                $rs[$k] = sprintf($list_style, $index);
            }
            $rs[$k] .= $li;
            $index++;
        }
        return implode(PHP_EOL, $rs);
    }

    public function processContent($contens) {

        foreach ($contens as $type=>$content) {
            // Process PROFILE
            if(in_array($type, $this->_content_title_gp['profile'])) {
                $this->_procProfile($content);
            }
            // Process Education
            if(in_array($type, $this->_content_title_gp['education'])) {
                $this->_procEducation($content);
            }
            // Process Experiences
            if(in_array($type, $this->_content_title_gp['experiences'])) {
                $this->_procExperiences($content);
            }
            // Process Skills
            if(in_array($type, $this->_content_title_gp['skills'])) {
                $this->_procSkills($content);
            }
            // Process Objective
            if(in_array($type, $this->_content_title_gp['objective'])) {
                $this->_procObjective($content);
            }
            // Process Interest
            if(in_array($type, $this->_content_title_gp['interest'])) {
                $this->_procInterest($content);
            }
        }

    }

    private function _procProfile($content) {
        foreach ($content as $v) {
            $v = $this->removeNumberIndex($v);
            $data = $this->parseIndex($v);

            if($data) {
                $this->_cv_data['profile'][$this->_label_title[$data[0]]] = $data[1];

                // Extract first, last name
                if($this->_label_title[$data[0]] == 'fullname') {
                    $p = explode(" ", $data[1], 2);
                    $this->_cv_data['profile']['firstname'] = $p[0];
                    $this->_cv_data['profile']['lastname'] = @$p[1];
                } elseif($this->_label_title[$data[0]] == 'email') {
                    $this->_cv_data['profile']['email'] = $this->extract_email_address($data[1]);
                }
            }

        }
    }

    private function _procTimeRange($content) {

        $result = array();

        $i = 0;

        foreach ($content as $k=>$line) {

            if($line == '') {
                $i++;
                continue;
            }

            $place = '';

            // Parse month year range
            $data = $this->parseTimeRange($line, 0);
            if(count($data) > 0) {
                if( @$data[1] !='' && @$data[3] != '' )  {
                    $from = $data[1];
                    $to = $data[3];
                    $place = @trim($data[4]);
                }
            } else {
                $data = $this->parseTimeRange($line, 0, '');
                if(count($data) > 0) {
                    if($data[1] != '' && $data[3] != '') {
                        $from = $data[1];
                        $to = $data[3];
                        $place = @$content[$k-1];
                    }
                }
            }

            if(count($data) == 0) {
                // Parse year range
                $data = $this->parseTimeRange($line, 1);
                if (count($data) > 0) {
                    if (@$data[1] != '' && @$data[3] != '') {
                        $from = $data[1];
                        $to = $data[3];
                        $place = @trim($data[4]);
                    }
                } else {
                    $data = $this->parseTimeRange($line, 1, '');
                    if (count($data) > 0) {
                        if ($data[1] != '' && $data[3] != '') {
                            $from = $data[1];
                            $to = $data[3];
                            $place = @$content[$k - 1];
                        }
                    }
                }
            }

            if(count($data) == 0) {
                // Parse only month year
                $data = $this->parseTimeRange($line, 2);
                if (count($data) > 0) {
                    if (@$data[1] != '' && @$data[2] != '') {
                        $from = $data[1];
                        $to = null;
                        $place = @trim($data[2]);
                    }
                } else {
                    $data = $this->parseTimeRange($line, 2, '');
                    if (count($data) > 0) {
                        if ($data[1] != '') {
                            $from = $data[1];
                            $to = null;
                            $place = @$content[$k - 1];
                        }
                    }
                }
            }

            if(count($data) == 0) {
                // Parse only year
                $data = $this->parseTimeRange($line, 3);
                if (count($data) > 0) {
                    if (@$data[1] != '' && @$data[2] != '') {
                        $from = $data[1];
                        $to = null;
                        $place = @trim($data[2]);
                    }
                } else {
                    $data = $this->parseTimeRange($line, 3, '');
                    if (count($data) > 0) {
                        if ($data[1] != '') {
                            $from = $data[1];
                            $to = null;
                            $place = @$content[$k - 1];
                        }
                    }
                }
            }

            if($place != '') {
                $i++;
                $result[$i] = array(
                    'from' => $from,
                    'to' => $to,
                    'place' => $place,
                );
            } elseif( isset($result[$i]) ) {
                $result[$i]['desc'][] = $line;
            }

        }
        return $result;
    }

    private function _procEducation($content) {
        $this->_cv_data['education'] = $this->_procTimeRange($content);
    }
    private function _procExperiences($content) {
        $this->_cv_data['experiences'] = $this->_procTimeRange($content);
    }

    private function _procSkills($content) {
        $this->_cv_data['skills'] = $this->reFormatList($content, '%d. ');
    }

    private function _procObjective($content) {
        $this->_cv_data['objective'] = $this->reFormatList($content, '%d. ');
    }

    private function _procInterest($content) {
        $this->_cv_data['interest'] = $this->reFormatList($content, '%d. ');
    }

}
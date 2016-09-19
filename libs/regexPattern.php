<?php
/**
 * CVExtractor - topITworks Hackathon 2016
 *
 * @author: Thomas Nguyen
 * @email: resize2011@gmail.com
 * @updated_at: 19-09-2016 20:50
 */

class RegexPattern {

    /*
     * So for multi break line style.
    */
    protected $_pattern_new_lines = "/\\r\\n|\\r|\\n/";

    /*
     * Parse paragraph by Roman numbers
     * I. abc
     * II. def
     * ...
     */
    protected $_pattern_roman = "/(?:X?L?(?:X{0,3}(?:IX|IV|V|V?I{1,3})|IX|X{1,3})|XL|L)\./";

    /*
     * Parse paragraph by index numbers and separate label with value by ": tab or withspace"
     * 1. abc
     * 2. def
     * ...
     */
    protected $_pattern_index = "/(.*):[\s|\t](.*)/m";

    /*
     * Parse line number.
    */
    protected $_pattern_line_num_index = "/^\d{1,2}\./";

    /*
     * Parse some line style.
    */
    protected $_pattern_line_index = "/^\d{1,2}\.|^-|^–/";

    /*
     * Parse time range for edu or working.
    */
    protected $_pattern_time_range = array(
        "(\d{1,2}\/\d{4})(.*?)(\d{1,2}\/\d{4})",
        "(\d{4})(.*?)(\d{4})",
        "(\d{1,2}\/\d{4})",
        "(\d{4})",
    );

    /*
     *
     */
    protected $_pattern_parse_email = "/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";

    /*
     *
     */
    protected $_pattern_check_address = '/(.*)Street(.*)City/m';

    /*
     * Content title
     */
    protected $_content_title = array(
        // En
        'Profile',
        'Objective',
        'Education',
        'Experiences',
        'Skills',
        'Interest',

        // VN
        'THÔNG TIN CÁ NHÂN',
        'QUÁ TRÌNH HỌC VẤN',
        'QUÁ TRÌNH LÀM VIỆC',
        'KỸ NĂNG',
        'ĐIỀU KIỆN LÀM VIỆC MONG MUỐN',
    );

    protected $_content_title_gp = array(
        "profile" => array(
            'Profile',
            'THÔNG TIN CÁ NHÂN'
        ),
        'objective' => array(
            'Objective',
            'ĐIỀU KIỆN LÀM VIỆC MONG MUỐN'
        ),
        'education' => array(
            'Education',
            'QUÁ TRÌNH HỌC VẤN'
        ),
        'experiences' => array(
            'Experiences',
            'QUÁ TRÌNH LÀM VIỆC'
        ),
        'skills' => array(
            'Skills',
            'KỸ NĂNG'
        ),
        'interest' => array(
            'Interest',
            'SỞ THÍCH'
        )
    );

    protected $_label_title = array(
        'Full Name' => 'fullname',
        'Họ & Tên' => 'fullname',

        'Giới tính' => 'gender',
        'Gender' => 'gender',

        'Ngày sinh' => 'birthday',
        'Date of Birth' => 'birthday',

        'Nơi sinh' => 'birthplace',
        'Place of birth' => 'birthplace',

        'Chiều cao' => 'height',
        'Height' => 'height',

        'Cân nặng' => 'weight',
        'Weight' => 'weight',

        'Tình trạng hôn nhân' => 'marital',
        'Marital Status' => 'marital',

        'Tôn giáo' => 'religion',
        'Religion' => 'religion',

        'Quốc tịch' => 'nationality',
        'Nationality' => 'nationality',

        'Địa chỉ thường trú' => 'address1',
        'Address 1' => 'address1',

        'Số ĐTDĐ' => 'mobile',
        'Mobile' => 'mobile',

        'Email cá nhân' => 'email',
        'E-mail' => 'email',

        'Địa chỉ tạm trú' => 'address2',
        'Address 2' => 'address2'

    );

}
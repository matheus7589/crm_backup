<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');
/**
 * @deprecated
 */
function add_encryption_key_old()
{
    $CI =& get_instance();
    $key         = generate_encryption_key();
    $config_path = APPPATH . 'config/config.php';
    $CI->load->helper('file');
    @chmod($config_path, FILE_WRITE_MODE);
    $config_file = read_file($config_path);
    $config_file = trim($config_file);
    $config_file = str_replace("\$config['encryption_key'] = '';", "\$config['encryption_key'] = '" . $key . "';", $config_file);
    if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return false;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $config_file, strlen($config_file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($config_path, FILE_READ_MODE);

    return $key;
}
/**
 * Function that will read file and will replace string
 * @param  string $path    path to file
 * @param  string $find    find string
 * @param  string $replace replace string
 * @return boolean
 */
function replace_in_file($path, $find, $replace)
{
    $CI =& get_instance();
    $CI->load->helper('file');
    @chmod($path, FILE_WRITE_MODE);
    $file = read_file($path);
    $file = trim($file);
    $file = str_replace($find, $replace, $file);
    $file = trim($file);
    if (!$fp = fopen($path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return false;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $file, strlen($file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($path, FILE_READ_MODE);

    return true;
}
/**
 * Check if the document should be RTL or LTR
 * The checking are performed in multiple ways eq Contact/Staff Direction from profile or from general settings *
 * @param  boolean $client_area
 * @return boolean
 */
function is_rtl($client_area = false)
{
    $CI =& get_instance();
    if (is_client_logged_in()) {
        $CI->db->select('direction')->from('tblcontacts')->where('id', get_contact_user_id());
        $direction = $CI->db->get()->row()->direction;
        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_client') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == true) {
        // Client not logged in and checked from clients area
        if (get_option('rtl_support_client') == 1) {
            return true;
        }
    } elseif (is_staff_logged_in()) {
        $CI->db->select('direction')->from('tblstaff')->where('staffid', get_staff_user_id());
        $direction = $CI->db->get()->row()->direction;
        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_admin') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == false) {
        if (get_option('rtl_support_admin') == 1) {
            return true;
        }
    }

    return false;
}
function refresh_notification($staffid = '')
{
    if(!is_numeric($staffid))
        return false;

    require_once __DIR__.'/../../application/libraries/Pusher.php';

    $options = array(
        'cluster' => get_option('pusher_cluster'),
        'encrypted' => true
    );
    $pusher = new Pusher(
        get_option('pusher_app_key'),
        get_option('pusher_app_secret'),
        get_option('pusher_app_id'),
        $options
    );
    $data[] = "";
    $pusher->trigger('notifications-channel-'.$staffid, 'notification',$data);
}
function refresh_panel()
{
    require_once __DIR__.'/../../application/libraries/Pusher.php';

    $options = array(
        'cluster' => get_option('pusher_cluster'),
        'encrypted' => true
    );
    $pusher = new Pusher(
        get_option('pusher_app_key'),
        get_option('pusher_app_secret'),
        get_option('pusher_app_id'),
        $options
    );
    $data["message"] = "";
    $pusher->trigger('painel', 'atualizar',$data);
}
/**
 * Generate encryption key for app-config.php
 * @return stirng
 */
function generate_encryption_key()
{
    $CI =& get_instance();
    // In case accessed from my_functions_helper.php
    $CI->load->library('encryption');
    $key = bin2hex($CI->encryption->create_key(16));

    return $key;
}
/**
 * Function used to validate all recaptcha from google reCAPTCHA feature
 * @param  string $str
 * @return boolean
 */
function do_recaptcha_validation($str = '')
{
    $CI =& get_instance();
    $CI->load->library('form_validation');
    $google_url = "https://www.google.com/recaptcha/api/siteverify";
    $secret     = get_option('recaptcha_secret_key');
    $ip         = $CI->input->ip_address();
    $url        = $google_url . "?secret=" . $secret . "&response=" . $str . "&remoteip=" . $ip;
    $curl       = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($res, true);
    //reCaptcha success check
    if ($res['success']) {
        return true;
    } else {
        $CI->form_validation->set_message('recaptcha', _l('recaptcha_error'));

        return false;
    }
}
/**
 * Get current date format from options
 * @return string
 */
function get_current_date_format($php = false)
{
    $format = get_option('dateformat');
    $format = explode('|', $format);

    $hook_data = do_action('get_current_date_format', array(
        'format' => $format,
        'php' => $php
    ));

    $format = $hook_data['format'];
    $php    = $php;

    if ($php == false) {
        return $format[1];
    } else {
        return $format[0];
    }
}
/**
 * Check if current user is admin
 * @param  mixed $staffid
 * @return boolean if user is not admin
 */
function is_admin($staffid = '')
{

    if (is_numeric($staffid)) {
        $_staffid = $staffid;
    } else {
        // checking for current user if is admin
        if(isset($GLOBALS['current_user'])){
            return $GLOBALS['current_user']->admin === '1';
        }
        $_staffid = get_staff_user_id();
    }
    $CI =& get_instance();
    $CI->db->select('1');
    $CI->db->where('admin', 1);
    $CI->db->where('staffid', $_staffid);
    $admin = $CI->db->get('tblstaff')->row();
    if ($admin) {
        return true;
    }

    return false;
}


function is_financeiro($staffid = ''){

    if(is_numeric($staffid)){
        $_staffid = $staffid;
    }else{
        $_staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db
        ->where('role = 6')
        ->where('staffid', $_staffid);
    $partner = $CI->db->get('tblstaff')->row();
    if($partner){
        return true;
    }

    return false;
}

function is_developer($staffid = '')
{
    if(is_numeric($staffid)){
        $_staffid = $staffid;
    }else{
        $_staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db
        ->where('role = 5')
        ->where('staffid', $_staffid);
    $partner = $CI->db->get('tblstaff')->row();
    if($partner){
        return true;
    }

    return false;
}


function is_atendimento_recepcao($staffid = '')
{
    if(is_numeric($staffid)){
        $_staffid = $staffid;
    }else{
        $_staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db
        ->where('role', 1)
        ->where('staffid', $_staffid);
    $atendimento = $CI->db->get('tblstaff')->row();
    if($atendimento){
        return true;
    }

    return false;
}

function get_client_contact_by_id($id){
    $CI =& get_instance();
    $CI->db
        ->where('id', $id);
    $clientcontact = $CI->db->get('tblclientscontacts')->row();
    if($clientcontact){
        return $clientcontact;
    }

    return false;
}

function get_client_by_id($id){
    $CI =& get_instance();
    $CI->db
        ->where('userid', $id);
    $client = $CI->db->get('tblclients')->row();
    if($client){
        return $client;
    }
    return false;
}

function get_clientcontact_by_relation($clientid, $contactid){
    $CI =& get_instance();
    $clientcontact = $CI->db
        ->where('clientid', $clientid)
        ->where('contactid', $contactid)
        ->get('tblclientscontacts')->row();

    if($clientcontact){
        return $clientcontact;
    }

    return false;
}

/**
 * @param string $staffid
 * @return bool
 */

function is_partner($staffid = ''){

    if(is_numeric($staffid)){
        $_staffid = $staffid;
    }else{
        $_staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db
        ->where('partner_id != 0')
        ->where('staffid', $_staffid);
    $partner = $CI->db->get('tblstaff')->row();
    if($partner){
        return true;
    }

    return false;
}

function get_partner_name($partner_id)
{
    $CI =& get_instance();
    if(is_numeric($partner_id))
        return $CI->db->select("CONCAT(firstname, ' ', lastname) as lastname")->where("partner_id",$partner_id)->get("tblpartner")->row("lastname");
}

function is_client_partner($id = '')
{
    if(is_numeric($id))
    {
        $CI =& get_instance();
        if($CI->db->where("partner_id = ".get_staff_partner_id()." AND userid = ".$id)->get("tblclients")->row()->partner_id == get_staff_partner_id())
        {
            return true;
        }
    }
    return false;
}

function get_partner_by_cnpj($cnpj = '')
{

    $CI =& get_instance();
    $CI->db->where('partner_cnpj', $cnpj);
    $partner = $CI->db->get('tblpartner')->row();

    if($partner)
        return $partner;

    return false;

}

function get_staff_role($staffid = ''){
    if(is_numeric($staffid)){
        $_staffid = $staffid;
    }else{
        $_staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db->where('staffid', $_staffid);
    $CI->db->select('role');
    $staff = $CI->db->get('tblstaff')->row();
    if($staff){
        return $staff->role;
    }

    return false;
}

/**
 * @param string $staffid
 * @return int
 */
function get_staff_partner_id($staffid = '')
{
    if(is_numeric($staffid)){
        $_staffid = $staffid;
    }else{
        if(isset($_SESSION['partner_id']))
            return $_SESSION['partner_id'];
        else
            $_staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db->where('staffid', $_staffid);
    $CI->db->select('partner_id');
    $partner = $CI->db->get('tblstaff')->row();
    if($partner) {
        return $partner->partner_id;
    }
    else
    {
        return false;
    }
}

/**
 * @param string $staffid
 * @return bool
 */
function is_not_partner($staffid = ''){
//Excluir essa função
    if(is_numeric($staffid)){
        $_staffid = $staffid;
    }else{
        $_staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db
        ->where('partner_id != 0')
        ->where('staffid', $_staffid);
    $partner = $CI->db->get('tblstaff')->row();
    if($partner){
        return false;
    }

    return true;
}

/**
 * @param $array seleciona o tipo de retorno dos dados
 * @return bool|string
 */
function get_partners($array = false){

    $CI =& get_instance();
    $CI->db->where('partner_id != ', 0);
    $partners = $CI->db->get('tblpartner')->result_array();

    if($array && $partners)
        return $partners;

    $text = '';
    foreach ($partners as $partner)
        $text .= $partner['staffid'] . ', ';

    if($partner)
        return substr($text, 0, (strlen($text)-2));

    return false;
}

/**
 * @return bool|string
 */
function get_not_partners(){

    $CI =& get_instance();
    $CI->db
        ->select('staffid')
        ->where('partner_id', 0);
    $partners = $CI->db->get('tblstaff')->result_array();
    $text = '';
    foreach ($partners as $partner){
        $text .= $partner['staffid'] . ', ';
    }
    if($partner){
        return substr($text, 0, (strlen($text)-2));
    }

    return false;
}

function get_status($id){
    $CI =& get_instance();
    $CI->db
        ->where('ticketstatusid', $id);
    $status = $CI->db->get('tblticketstatus')->row();
    if($status)
        return $status;

    return false;
}

/**
 * Is user logged in
 * @return boolean
 */
function is_logged_in()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('client_logged_in') && !$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }

    return true;
}
/**
 * Is client logged in
 * @return boolean
 */
function is_client_logged_in()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('client_logged_in')) {
        return true;
    }

    return false;
}
/**
 * Is staff logged in
 * @return boolean
 */
function is_staff_logged_in()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('staff_logged_in')) {
        return true;
    }

    return false;
}
/**
 * Return logged staff User ID from session
 * @return mixed
 */
function get_staff_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }

    return $CI->session->userdata('staff_user_id');
}
/**
 * Return logged client User ID from session
 * @return mixed
 */
function get_client_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('client_logged_in')) {
        return false;
    }

    return $CI->session->userdata('client_user_id');
}
function get_contact_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('contact_user_id')) {
        return false;
    }

    return $CI->session->userdata('contact_user_id');
}
/**
 * Get admin url
 * @param string url to append (Optional)
 * @return string admin url
 */
function admin_url($url = '')
{
    $admin_url = do_action('admin_url', ADMIN_URL);

    if ($url == '' || $url == '/') {
        if ($url == '/') {
            $url = '';
        }
        return site_url($admin_url) . '/';
    } else {
        return site_url($admin_url . '/' . $url);
    }
}
/**
 * Outputs language string based on passed line
 * @since  Version 1.0.1
 * @param  string $line  language line string
 * @param  string $label sprint_f label
 * @return string        formatted language
 */
function _l($line, $label = '', $log_errors = true)
{
    $CI =& get_instance();

    $hook_data = do_action('before_get_language_text', array('line'=>$line, 'label'=>$label));
    $line = $hook_data['line'];
    $label = $hook_data['label'];

    if (is_array($label) && count($label) > 0) {
        $_line = vsprintf($CI->lang->line(trim($line), $log_errors), $label);
    } else {
        $_line = @sprintf($CI->lang->line(trim($line), $log_errors), $label);
    }

    $hook_data = do_action('after_get_language_text', array('line'=>$line, 'label'=>$label, 'formatted_line'=>$_line));
    $_line = $hook_data['formatted_line'];
    $line = $hook_data['line'];

    if ($_line != '') {
        if (preg_match('/"/', $_line) && !is_html($_line)) {
            $_line = htmlspecialchars($_line, ENT_COMPAT);
        }

        return $CI->encoding_lib->toUTF8($_line);
    }

    if (mb_strpos($line, '_db_') !== false) {
        return 'db_translate_not_found';
    }

    return $CI->encoding_lib->toUTF8($line);
}
/**
 * Format date to selected dateformat
 * @param  date $date Valid date
 * @return date/string
 */
function _d($date)
{
    if ($date == '' || is_null($date) || $date == '0000-00-00') {
        return '';
    }
    if (strpos($date, ' ') !== false) {
        return _dt($date);
    }
    $format = get_current_date_format();
    $date   = strftime($format, strtotime($date));

    return do_action('after_format_date', $date);
}
/**
 * Format datetime to selected datetime format
 * @param  datetime $date datetime date
 * @return datetime/string
 */
function _dt($date, $is_timesheet = false)
{
    if ($date == '' || is_null($date) || $date == '0000-00-00 00:00:00') {
        return '';
    }
    $format = get_current_date_format();
    $hour12 = (get_option('time_format') == 24 ? false : true);

    if ($is_timesheet == false) {
        $date = strtotime($date);
    }

    if ($hour12 == false) {
        $tf = '%H:%M:%S';
        if ($is_timesheet == true) {
            $tf = '%H:%M';
        }
        $date   = strftime($format . ' ' . $tf, $date);
    } else {
        $date = date(get_current_date_format(true). ' g:i A', $date);
    }

    return do_action('after_format_datetime', $date);
}
/**
 * Convert string to sql date based on current date format from options
 * @param  string $date date string
 * @return mixed
 */
function to_sql_date($date, $datetime = false)
{
    if ($date == '') {
        return null;
    }

    $to_date     = 'Y-m-d';
    $from_format = get_current_date_format(true);


    $hook_data['date']        = $date;
    $hook_data['from_format'] = $from_format;
    $hook_data['datetime']    = $datetime;

    $hook_data = do_action('before_sql_date_format', $hook_data);

    $date        = $hook_data['date'];
    $from_format = $hook_data['from_format'];

    if ($datetime == false) {
        return date_format(date_create_from_format($from_format, $date), $to_date);
    } else {
        if (strpos($date, ' ') === false) {
            $date .= ' 00:00:00';
        } else {
            $hour12 = (get_option('time_format') == 24 ? false : true);
            if ($hour12 == false) {
                $_temp = explode(' ', $date);
                $time  = explode(':', $_temp[1]);
                if (count($time) == 2) {
                    $date .= ':00';
                }
            } else {
                $tmp = _simplify_date_fix($date, $from_format);
                $time = date("G:i", strtotime($tmp));
                $tmp = explode(' ', $tmp);
                $date = $tmp[0]. ' ' . $time.':00';
            }
        }

        $date = _simplify_date_fix($date, $from_format);
        $d = strftime('%Y-%m-%d %H:%M:%S', strtotime($date));

        return do_action('to_sql_date_formatted', $d);
    }
}
function _simplify_date_fix($date, $from_format)
{
    if ($from_format == 'd/m/Y') {
        $date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $date);
    } elseif ($from_format == 'm/d/Y') {
        $date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
    } elseif ($from_format == 'm.d.Y') {
        $date = preg_replace('#(\d{2}).(\d{2}).(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
    } elseif ($from_format == 'm-d-Y') {
        $date = preg_replace('#(\d{2})-(\d{2})-(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
    }

    return $date;
}
/**
 * Check if passed string is valid date
 * @param  string  $date
 * @return boolean
 */
function is_date($date)
{
    if (strlen($date) < 10) {
        return false;
    }

    return (bool) strtotime(str_replace('/', '-', $date));
}
/**
 * Get locale key by system language
 * @param  string $language language name from (application/languages) folder name
 * @return string
 */
function get_locale_key($language = 'english')
{
    $locale = 'en';
    if ($language == '') {
        return $locale;
    }

    $locales = get_locales();

    if (isset($locales[$language])) {
        $locale = $locales[$language];
    } elseif (isset($locales[ucfirst($language)])) {
        $locale = $locales[ucfirst($language)];
    } else {
        foreach ($locales as $key => $val) {
            $key      = strtolower($key);
            $language = strtolower($language);
            if (strpos($key, $language) !== false) {
                $locale = $val;
                // In case $language is bigger string then $key
            } elseif (strpos($language, $key) !== false) {
                $locale = $val;
            }
        }
    }

    $locale = do_action('before_get_locale', $locale);

    return $locale;
}

function get_categories_video($id = '')
{
    $CI =& get_instance();
    if(is_numeric($id))
        return $CI->db->query("SELECT subcategory, name FROM tblmoviessubcategory INNER JOIN tblmoviescategory ON categoryfk = categoryid WHERE idsubcategory = ".$id)->row();
    return false;
}

/**
 * Verifica se tem permissão para acessar o Vídeo
 * @param $id
 * @param string $key
 * @return bool
 */
function has_permission_video($id,$key = '')
{
    $CI =& get_instance();
    $perms = $CI->db->select("perm_public,perm_clients,perm_staff,perm_key,is_url,departments")->where("idmovie",$id)->get("tblmovies")->row();
    if(is_admin())
        return true;
    if(isset($perms)) {
        /**if ($perms->is_url == 1) Retirado para obedecer regra de permissão
            return true;*/
        if ($perms->perm_public == 1)
            return true;
        else {
            if ($perms->perm_staff == 1 && get_staff_user_id() != null) {
                if($perms->departments != "") {
                    $CI->db->query("SELECT * FROM `tblstaffdepartments` WHERE `staffid` = " . get_staff_user_id() . " AND `departmentid` IN (" . $perms->departments . ")");
                    if ($CI->db->affected_rows() > 0)
                        return true;
                }
                return false;
            }
            else if ($perms->perm_clients == 1 && get_client_user_id() != null)
                return true;
            if ($perms->perm_key != NULL && $perms->perm_key == $key)
                return true;
        }
    }
    return false;
}

/**
 * Check if staff user has permission
 * @param  string  $permission permission shortname
 * @param  mixed  $staffid if you want to check for particular staff
 * @return boolean
 */
function has_permission($permission, $staffid = '', $can = '')
{
    $CI =& get_instance();

    /**
     * Maybe permission is function?
     * Example is_admin or is_staff_member
     */
    if (function_exists($permission) && is_callable($permission)) {
        return call_user_func($permission, $staffid);
    }

    /**
     * If user is admin return true
     * Admin have all permissions
     */
    if (is_admin($staffid)) {
        return true;
    }

    $currentUser = $staffid == '' ? true : false;
    $staffid = ($staffid == '' ? get_staff_user_id() : $staffid);
    $can = ($can == '' ? 'view' : $can);

    /**
     * Stop making query if we are doing checking for current user
     * Current user is stored in $GLOBALS including the permissions
     */
    if($currentUser && isset($GLOBALS['current_user']) && is_array($GLOBALS['current_user']->permissions)){
            $hasPermission = false;
            foreach($GLOBALS['current_user']->permissions as $permObject){
                if($permObject->permission_name == $permission
                    && $permObject->{'can_' . $can} == '1'){
                    $hasPermission = true;
                break;
            }
        }

        return $hasPermission;
    }

    /**
     * Get the permission id
     */
    $CI->db->select('permissionid as id')
    ->where('shortname', $permission);

    $permission = $CI->db->get('tblpermissions')->row();
    /**
     * Permission not exists?
     * Return false
     */
    if (!$permission) {
        return false;
    }
    /**
     * Check if user have permissions
     * @var boolean
     */
    return (total_rows('tblstaffpermissions',array(
        'permissionid'=>$permission->id,
        'staffid'=>$staffid,
        'can_' . $can => 1
        )) > 0 ? true : false);
}
/**
 * Function is customer admin
 * @param  mixed  $id       customer id
 * @param  staff_id  $staff_id staff id to check
 * @return boolean
 */
function is_customer_admin($id, $staff_id = '')
{
    $_staff_id = get_staff_user_id();
    if (is_numeric($staff_id)) {
        $_staff_id = $staff_id;
    }
    $customer_admin_found = total_rows('tblcustomeradmins', array(
        'customer_id' => $id,
        'staff_id' => $_staff_id
    ));
    if ($customer_admin_found > 0) {
        return true;
    }

    return false;
}
/**
 * Check if staff member have assigned customers
 * @param  mixed $staff_id staff id
 * @return boolean
 */
function have_assigned_customers($staff_id = '')
{
    $_staff_id = get_staff_user_id();
    if (is_numeric($staff_id)) {
        $_staff_id = $staff_id;
    }
    $customers_found = total_rows('tblcustomeradmins', array(
        'staff_id' => $_staff_id
    ));
    if ($customers_found > 0) {
        return true;
    }

    return false;
}
/**
 * Check if contact has permission
 * @param  string  $permission permission name
 * @param  string  $contact_id     contact id
 * @return boolean
 */
function has_contact_permission($permission, $contact_id = '')
{
    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    $permissions = $CI->perfex_base->get_contact_permissions();
    // Contact id passed form function
    if ($contact_id != '') {
        $_contact_id = $contact_id;
    } else {
        // Current logged in contact
        $_contact_id     = get_contact_user_id();
    }
    foreach ($permissions as $_permission) {
        if ($_permission['short_name'] == $permission) {
            if (total_rows('tblcontactpermissions', array(
                'permission_id' => $_permission['id'],
                'userid' => $_contact_id
            )) > 0) {
                return true;
            }else if($permission == 'support'){
                return true;
            }
        }
    }

    return false;
}
/**
 * Check if user is staff member
 * In the staff profile there is option to check IS NOT STAFF MEMBER eq like contractor
 * Some features are disabled when user is not staff member
 * @param  string  $id staff id
 * @return boolean
 */
function is_staff_member($id = '')
{
    $CI =& get_instance();
    $staffid = $id;
    if ($staffid == '') {
        if(isset($GLOBALS['current_user'])){
            return $GLOBALS['current_user']->is_not_staff === '0';
        }
        $staffid = get_staff_user_id();
    }
    $CI->db->select('1')->from('tblstaff')->where('staffid', $staffid)->where('is_not_staff', 0);
    $row = $CI->db->get()->row();
    if ($row) {
        return true;
    }

    return false;
}
/**
 * Load language in admin area
 * @param  string $staff_id
 * @return string return loaded language
 */
function load_admin_language($staff_id = '')
{
    $CI =& get_instance();

    $CI->lang->is_loaded = array();
    $CI->lang->language  = array();

    $language = get_option('active_language');
    if (is_staff_logged_in() || $staff_id != '') {
        $staff_language = get_staff_default_language($staff_id);
        if (!empty($staff_language)) {
            if (file_exists(APPPATH . 'language/' . $staff_language)) {
                $language = $staff_language;
            }
        }
    }
    $CI->lang->load($language . '_lang', $language);
    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
        $CI->lang->load('custom_lang', $language);
    }

    $language = do_action('after_load_admin_language', $language);

    return $language;
}

/**
 * Load customers area language
 * @param  string $customer_id
 * @return string return loaded language
 */
function load_client_language($customer_id = '')
{
    $CI =& get_instance();
    $language = get_option('active_language');
    if (is_client_logged_in() || $customer_id != '') {
        $client_language = get_client_default_language($customer_id);
        if (!empty($client_language)) {
            if (file_exists(APPPATH . 'language/' . $client_language)) {
                $language = $client_language;
            }
        }
    }

    $CI->lang->load($language . '_lang', $language);
    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
        $CI->lang->load('custom_lang', $language);
    }

    $language = do_action('after_load_client_language', $language);

    return $language;
}
/**
 * Get current url with query vars
 * @return string
 */
function current_full_url()
{
    $CI =& get_instance();
    $url = $CI->config->site_url($CI->uri->uri_string());

    return $_SERVER['QUERY_STRING'] ? $url . '?' . $_SERVER['QUERY_STRING'] : $url;
}
function value_exists_in_array_by_key($array, $key, $val)
{
    foreach ($array as $item) {
        if (isset($item[$key]) && $item[$key] == $val) {
            return true;
        }
    }

    return false;
}
add_action('check_vault_entries_visibility', '_check_vault_entries_visibility');
function _check_vault_entries_visibility($entries)
{
    $new = array();
    foreach ($entries as $entry) {
        if ($entry['visibility'] != 1) {
            if ($entry['visibility'] == 2 && !is_admin() && $entry['creator'] != get_staff_user_id()) {
                continue;
            } elseif ($entry['visibility'] == 3 && $entry['creator'] != get_staff_user_id() && !is_admin()) {
                continue;
            }
        }
        $new[] = $entry;
    }

    return $new;
}
/**
 * Triggers
 * @param  array  $users id of users to receive notifications
 * @return null
 */
function pusher_trigger_notification($users = array()){

    if(get_option('pusher_realtime_notifications') == 0) {
        return false;
    }

    if(!is_array($users)){
        return false;
    }

    if(count($users) == 0) {
        return false;
    }

    if(!class_exists('Pusher')){
        require_once(APPPATH.'libraries/Pusher.php');
    }

    $app_key = get_option('pusher_app_key');
    $app_secret = get_option('pusher_app_secret');
    $app_id = get_option('pusher_app_id');

    if($app_key == "" || $app_secret == "" || $app_id == "") {
        return false;
    }

    $pusher_options = do_action('pusher_options',array());

    if(!isset($pusher_options['cluster']) && get_option('pusher_cluster') != ''){
        $pusher_options['cluster'] = get_option('pusher_cluster');
    }

    $pusher = new Pusher(
        $app_key,
        $app_secret,
        $app_id,
        $pusher_options
        );

    $channels = array();
    foreach($users as $id){
        array_push($channels,'notifications-channel-' . $id);
    }

    $channels = array_unique($channels);

    $pusher->trigger($channels, 'notification', array());
}

/**
 * @param $id
 * @return bool
 */
function get_service1_name($id)
{
    $CI =& get_instance();
    if(is_numeric($id))
        return $CI->db->select("name")->where("serviceid", $id)->get("tblservices")->row()->name;

    return false;
}

/**
 * @param $name
 * @return bool
 */
function get_service1_id($name){
    $CI =& get_instance();
    if(!is_numeric($name))
        return $CI->db->select('serviceid')->where('name', $name)->get('tblservices')->row()->serviceid;

    return false;
}

/**
 * @param $id
 * @return bool
 */
function get_service2_name($id)
{
    $CI =& get_instance();
    if(is_numeric($id))
        return $CI->db->select("name")->where("secondServiceid", $id)->get("tblsecondservice")->row("name");

    return false;
}

/**
 * @param $name
 * @return bool
 */
function get_service2_id($name, $serviceid){
    $CI =& get_instance();
    if(!is_numeric($name)) {
        return $CI->db->select('secondServiceid')->where('name', $name)
            ->where('serviceid', $serviceid)
            ->get('tblsecondservice')->row()->secondServiceid;
    }

    return false;
}

function secondsToHumanReadable(int $seconds, int $requiredParts = null)
{
    $from     = new \DateTime('@0');
    $to       = new \DateTime("@$seconds");
    $interval = $from->diff($to);
    $str      = '';

    $parts = [
        'y' => 'ano ',
        'm' => 'mês ',
        'd' => 'dia ',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    ];

    $includedParts = 0;
    foreach ($parts as $key => $text) {
        if ($requiredParts && $includedParts >= $requiredParts) {
            break;
        }

        $currentPart = $interval->{$key};

        if (empty($currentPart) && ($key != 'h' && $key != 'i' && $key != 's')) {
            continue;
        }

//        if (!empty($str)) {
//            $str .= ', ';
//        }


        if($key == 'h' || $key == 'i' || $key == 's'){
            if($currentPart < 10){
                $str .= sprintf('0%d:', $currentPart);
            }else {
                $str .= sprintf('%d:', $currentPart);
            }
        }else{
            $str .= sprintf('%d %s', $currentPart, $text);
        }

//        $str .= sprintf('%d %s', $currentPart, $text);

        if ($currentPart > 1 && ($key != 'h' && $key != 'i' && $key != 's')) {
            if($key == 'm'){
                $str = substr($str, 0, strlen($str) - 4);
                $str .= 'eses ';
            }else{
                // handle plural
                $str = substr($str, 0, strlen($str) - 1);
                $str .= 's ';
            }

        }

        $includedParts++;
    }

    return substr($str, 0, strlen($str) - 1);
//    return $str;
}

function segundosParaLeituraHumana(int $seconds, int $requiredParts = null)
{
    $from     = new \DateTime('@0');
    $to       = new \DateTime("@$seconds");
    $interval = $from->diff($to);
    $str      = '';

    $parts = [
        'y' => 'ano ',
        'm' => 'mês ',
        'd' => 'dia ',
        'h' => 'hora ',
        'i' => 'minuto ',
        's' => 'segundo ',
    ];

    $includedParts = 0;
    foreach ($parts as $key => $text) {
        if ($requiredParts && $includedParts >= $requiredParts) {
            break;
        }

        $currentPart = $interval->{$key};

        if (empty($currentPart)) {
            continue;
        }

//        if (!empty($str)) {
//            $str .= ', ';
//        }


        if($currentPart < 10){
            $str .= sprintf('0%d %s', $currentPart, $text);
        }else {
            $str .= sprintf('%d %s', $currentPart, $text);
        }

//        $str .= sprintf('%d %s', $currentPart, $text);

        if ($currentPart > 1 ) {
            if($key == 'm'){
                $str = substr($str, 0, strlen($str) - 4);
                $str .= 'eses ';
            }else{
                // handle plural
                $str = substr($str, 0, strlen($str) - 1);
                $str .= 's ';
            }

        }

        $includedParts++;
    }

    $str = substr($str, 0, strlen($str) - 1);
    $aux = explode(' ', $str);
    if(count($aux) > 2){
        $aux[count($aux) - 2] = ' e ' . $aux[count($aux) - 2];
        $str = implode(' ', $aux);
    }

    return $str;
//    return $str;
}

function biss_hours($start, $end){

    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $periodInterval = new DateInterval( "PT1H" );

    $period = new DatePeriod( $startDate, $periodInterval, $endDate );
    $count = 0;

    foreach($period as $date){

        $startofday = clone $date;
        $startofday->setTime(8,00);

        $endofday = clone $date;
        $endofday->setTime(18,00);

        if($date > $startofday && $date <= $endofday && !in_array($date->format('l'), array('Sunday','Saturday'))){

            $count++;
        }

    }

    //Get seconds of Start time
    $start_d = date("Y-m-d H:00:00", strtotime($start));
    $start_d_seconds = strtotime($start_d);
    $start_t_seconds = strtotime($start);
    $start_seconds = $start_t_seconds - $start_d_seconds;

    //Get seconds of End time
    $end_d = date("Y-m-d H:00:00", strtotime($end));
    $end_d_seconds = strtotime($end_d);
    $end_t_seconds = strtotime($end);
    $end_seconds = $end_t_seconds - $end_d_seconds;

    $diff = $end_seconds-$start_seconds;

    if($diff!=0):
        $count--;
    endif;

    $total_min_sec = date('i:s',$diff);
    $teste = explode(':', $total_min_sec);
    $teste[0] = $teste[0] * 60;
    $count = $count * 3600;
    $result = $teste[0] + $teste[1] + $count;

    return secondsToHumanReadable($result);
}
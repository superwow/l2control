<?php

defined( '_ACM_VALID' ) or die( 'Direct Access to this location is not allowed.' );

class account{

	private $login = NULL;
	private $password = NULL;
	private $lastactive = NULL;
	private $accessLevel = NULL;
	private $ip = NULL;
	private $lastServer = NULL;
	private $email = NULL;
	private $created_time = NULL;

	private static $instance;

	private function __construct() {}

	public function __clone() {
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

	public static function singleton() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	public static function load() {
		if(empty($_SESSION['acm']))			// Check if user is logged
			return ACCOUNT::singleton();

		return unserialize($_SESSION['acm']);
	}

	public function save() {
		$_SESSION['acm'] = serialize($this);
	}

	public function getLogin() {
		return $this->login;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setLogin($login) {
		$this->login = $login;
	}

	private function getAccessLevelColumn() {
		$column = (string)CONFIG::g()->accessLevel();

		if (!preg_match('/^[A-Za-z0-9_]+$/', $column)) {
			throw new RuntimeException('Invalid access level column name configured');
		}

		return $column;
	}

	public function create ($login, $pwd, $repwd, $email, $img = null) {

		if(!$this->verif_img($img)) {
			MSG::add_error(LANG::getInstance()->i18n('_image_control'));
			return false;
		}

		if($login == '') {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_UNAME1'));
			return false;
		}

		if(!$this->verif_char($login, true)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_UNAME2'));
			return false;
		}

		if(!$this->verif_char($pwd)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_VPASS1'));
			return false;
		}

		if($login == $pwd) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_UNAME3'));
			return false;
		}

		if($pwd != $repwd) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_VPASS2'));
			return false;
		}

		if(!$this->verif_limit_create()) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_LIMIT_CREATING'));
			return false;
		}

		if($this->is_login_exist($login)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_INUSE'));
			return false;
		}

		if(!$this->verif_email($email)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_MAIL'));
			return false;
		}

		if($this->is_email_exist($email)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_EMAIL_INUSE'));
			return false;
		}

		$code = $this->gen_img_cle(10);

		// accessLevel column name comes from config, not user input
		$accessLevelCol = $this->getAccessLevelColumn();

		DEBUG::add('Create a new user on the accounts table with -1 on accesslevel');

		Database::execute(
			"INSERT INTO `accounts` (`login`,`password`,`lastactive`,`{$accessLevelCol}`,`lastIp`,`email`) VALUES (?, ?, ?, '-1', ?, ?)",
			[$login, $this->l2j_encrypt($pwd), time(), $this->PseudoIpv4($_SERVER['REMOTE_ADDR']), $email]
		);

		if(!$this->is_login_exist($login)) {
			MSG::add_error(LANG::getInstance()->i18n('_creating_acc_prob'));
			return false;
		}

		DEBUG::add('Insert the activation key on account_data for checking email');

		Database::execute(
			"REPLACE INTO `account_data` (`account_name`, `var`, `value`) VALUES (?, 'activation_key', ?)",
			[$login, $code]
		);

		if(!CONFIG::g()->core_act_email) {
			$this->valid_account($code);
			MSG::add_valid(LANG::getInstance()->i18n('_account_created_act'));
		}else{
			MSG::add_valid(LANG::getInstance()->i18n('_account_created_noact'));
			EMAIL::OP()->operator($login, 'created_account_validation', $code);
		}

		return true;
	}

	private function get_number_acc() {
		DEBUG::add('Get the amounth of account on accounts table');
		return Database::fetchValue("SELECT COUNT(`login`) FROM `accounts`");
	}

	private function verif_limit_create () {

		if (CONFIG::g()->core_acc_limit == false)
			return true;

		if ($this->get_number_acc() >= CONFIG::g()->core_acc_limit)
			return false;

		return true;
	}

	private function verif_char($string, $mode = false) {

		$regex = CONFIG::g()->regex($mode);

		if (!preg_match($regex , $string))
			return false;

		return true;
	}

	private function verif_email($email) {

		$regex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

		if (!preg_match($regex, $email))
			return false;

		return true;
	}

	private function verif_img($key) {

		DEBUG::add('Check if the image verification is needed');

		if (!CONFIG::g()->core_act_img) {
			DEBUG::add(' -> No need image verification');
			return true;
		}

		DEBUG::add('Check if the image verification is correct');

		if ($key != $_SESSION['code']) {
			DEBUG::add('<li> key gived: '.$key.'</li><li> key needed: '.$_SESSION['code'].'</li>');
			return false;
		}

		return true;
	}

	private function is_login_exist($login) {

		DEBUG::add('Check if the login still exist');

		$count = Database::fetchValue(
			"SELECT COUNT(`login`) FROM `accounts` WHERE `login` = ? LIMIT 1",
			[$login]
		);

		return $count != '0';
	}

	private function is_email_exist($email) {

		if(CONFIG::g()->core_same_email)				// if we allow account with same email
			return false;

		DEBUG::add('Check if the email still exist');

		$count = Database::fetchValue(
			"SELECT COUNT(`login`) FROM `accounts` WHERE `email` = ? LIMIT 1",
			[$email]
		);

		return $count != '0';
	}

	private function valid_key($key) {
		DEBUG::add('Check if there are an activation key on account_data');

		$count = Database::fetchValue(
			"SELECT COUNT(`account_data`) FROM `account_data` WHERE `var` = 'activation_key' AND `value` = ? LIMIT 1",
			[$key]
		);

		if ($count === '0' || $count === null)
			return false;

		DEBUG::add('Get the account name linked with the activation key');

		return Database::fetchValue(
			"SELECT `account_name` FROM `account_data` WHERE `var` = 'activation_key' AND `value` = ? LIMIT 1",
			[$key]
		);
	}

	public function valid_account($key) {

		if (!($login = $this->valid_key($key)))
			return false;

		$accessLevelCol = $this->getAccessLevelColumn();

		DEBUG::add('Update accesslevel to 0');

		Database::execute(
			"UPDATE `accounts` SET `{$accessLevelCol}` = '0' WHERE `login` = ? LIMIT 1",
			[$login]
		);

		DEBUG::add('Delete activation key from account_data table');

		Database::execute(
			"DELETE FROM `account_data` WHERE `account_name` = ? AND `var` = 'activation_key' AND `value` = ? LIMIT 1",
			[$login, $key]
		);

		if ($this->valid_key($key))
			return false;

		EMAIL::OP()->operator($login, 'created_account_activation');

		return true;
	}

	private function get_logtry() {
		DEBUG::add('Get how many unsuccessfull loggin user have do');

		$nb_try = Database::fetchValue(
			"SELECT `var` FROM `account_data` WHERE `account_name` = ? AND `var` LIKE ? LIMIT 1",
			[$this->PseudoIpv4($_SERVER['REMOTE_ADDR']), 'try_%']
		);

		if ($nb_try === null)
			return false;

		return substr($nb_try, -1);
	}

	private function get_latestlogtry() {
		DEBUG::add('Get how many unsuccessfull loggin user have do');

		$value = Database::fetchValue(
			"SELECT `value` FROM `account_data` WHERE `account_name` = ? AND `var` LIKE ? LIMIT 1",
			[$this->PseudoIpv4($_SERVER['REMOTE_ADDR']), 'try_%']
		);

		return $value;
	}

	private function set_logtry($del = false) {

		$nb_try = $this->get_logtry();
		$ip = $this->PseudoIpv4($_SERVER['REMOTE_ADDR']);

		if ($nb_try === false) {
			DEBUG::add('Set the first time how many unsuccessfull loggin user have do');

			Database::execute(
				"INSERT INTO `account_data` (`account_name`, `var`, `value`) VALUES (?, 'try_0', ?)",
				[$ip, time()]
			);

			return true;
		}

		$nb_try++;

		DEBUG::add('Set how many unsuccessfull loggin user have do');

		if ($del)
			$nb_try = 0;

		Database::execute(
			"UPDATE `account_data` SET `var` = ?, `value` = ? WHERE `account_name` = ?",
			['try_'.$nb_try, time(), $ip]
		);

		return true;
	}

	public function auth ($login, $password, $img = null) {

		if(!$this->verif_img($img)) {
			MSG::add_error(LANG::getInstance()->i18n('_image_control'));
			return false;
		}

		if($this->get_latestlogtry() <= (time()-(60*CONFIG::g()->core_spam_time)))
			$this->set_logtry(true);

		if($this->get_logtry() >= CONFIG::g()->core_spam_try) {
			LOGDAEMON::l()->add('Warning : SPAMMING AUTHENTICATION');
			MSG::add_error('Warning : SPAMMING AUTHENTICATION'.'<br />');
			return false;
		}

		$this->login = htmlentities((string)($login ?? ''), ENT_QUOTES, 'UTF-8');
		$this->password = htmlentities((string)($password ?? ''), ENT_QUOTES, 'UTF-8');

		$this->password = $this->l2j_encrypt($this->password);

		$accessLevelCol = $this->getAccessLevelColumn();

		DEBUG::add('Check if login and password match on account table');

		$count = Database::fetchValue(
			"SELECT COUNT(`login`) FROM `accounts` WHERE `login` = ? AND `password` = ? AND `{$accessLevelCol}` >= 0 LIMIT 1",
			[$this->login, $this->password]
		);

		if($count != 1) {
			$this->set_logtry();
			return false;
		}

		$this->ip = $this->PseudoIpv4($_SERVER['REMOTE_ADDR']);

		$this->update_last_active();

		$this->email = $this->get_email();

		$this->save();

		return true;
	}

	public function verif () {

		if(!$this->is_logged())			// Check if user is logged
			return false;

		if($this->ip != $this->PseudoIpv4($_SERVER['REMOTE_ADDR'])){	// Check if user ip is the same than the first time
			MSG::add_error(LANG::getInstance()->i18n('_logout'));
			$this->loggout();
			return false;
		}

		$account = $this->load();

		$accessLevelCol = $this->getAccessLevelColumn();

		DEBUG::add('Verify if the user is correctly logged');

		$count = Database::fetchValue(
			"SELECT COUNT(`login`) FROM `accounts` WHERE `login` = ? AND `password` = ? AND `{$accessLevelCol}` >= 0 LIMIT 1",
			[$account->login, $account->password]
		);

		if($count != 1)	{	// Check if user session data are right
			MSG::add_error(LANG::getInstance()->i18n('_logout'));
			$this->loggout();
			return false;
		}

		return true;
	}

	private function update_last_active() {

		DEBUG::add('Update last connexion of the account');

		Database::execute(
			"UPDATE `accounts` SET `lastactive` = ?, `lastIp` = ? WHERE `login` = ? LIMIT 1",
			[time(), $this->PseudoIpv4($_SERVER['REMOTE_ADDR']), $this->login]
		);
	}

	private function change_pwd($pwd) {

		DEBUG::add('Update password of the account');

		Database::execute(
			"UPDATE `accounts` SET `password` = ?, `lastIp` = ? WHERE `login` = ? LIMIT 1",
			[$this->l2j_encrypt($pwd), $this->PseudoIpv4($_SERVER['REMOTE_ADDR']), $this->login]
		);

		EMAIL::OP()->operator($this->login, 'password_reseted', $pwd);
	}

	public function forgot_pwd($login, $email, $img = null)
	{

		if(!$this->verif_img($img)) {
			MSG::add_error(LANG::getInstance()->i18n('_image_control'));
			return false;
		}

		DEBUG::add('Check if there are a login name match with an email');

		$count = Database::fetchValue(
			"SELECT COUNT(`login`) FROM `accounts` WHERE `login` = ? AND `email` = ?",
			[$login, $email]
		);

		if($count != 1) {
			MSG::add_error(LANG::getInstance()->i18n('_wrong_auth'));
			return false;
		}

		$code = $this->gen_img_cle(5);

		DEBUG::add('Insert a random key and send it to the email for authenticate user');

		Database::execute(
			"REPLACE INTO `account_data` (`account_name`, `var`, `value`) VALUES (?, 'forget_pwd', ?)",
			[$login, $code]
		);

		EMAIL::OP()->operator($login, 'forget_password_validation', $code);

		return true;
	}

	public function forgot_pwd2($login, $key)
	{

		if(!$this->verif_tag($login, 'forget_pwd', $key)) {
			MSG::add_error(LANG::getInstance()->i18n('_activation_control'));
			return false;
		}

		DEBUG::add('User has been authenticated. Delete the ask');

		Database::execute(
			"DELETE FROM `account_data` WHERE `account_name` = ? AND `var` = 'forget_pwd' AND `value` = ? LIMIT 1",
			[$login, $key]
		);

		$this->login = $login;

		$pwd = $this->gen_img_cle(10);
		$this->change_pwd($pwd);

		return true;
	}

	private function verif_tag($login, $tag, $value){
		DEBUG::add('Check the tag on account_data');

		$count = Database::fetchValue(
			"SELECT COUNT(`account_name`) FROM `account_data` WHERE `account_name` = ? AND `var` = ? AND `value` = ? LIMIT 1",
			[$login, $tag, $value]
		);

		return $count == 1;
	}

	public function edit_password ($pass,$newpass,$renewpass)
	{

		if($this->password != $this->l2j_encrypt($pass)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_VPASS1'));
			return false;
		}

		if($this->login == $newpass) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_UNAME3'));
			return false;
		}

		if(!$this->verif_char($newpass)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_VPASS1'));
			return false;
		}

		if ($newpass != $renewpass) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_VPASS2'));
			return false;
		}

		$this->change_pwd($newpass);

		$this->auth($this->login, $newpass, $_SESSION['code']);

		return true;
	}

	public function can_chg_email() {

		if($this->email == '')
			return true;

		if(!CONFIG::g()->core_can_chg_email)
			return false;

		return true;
	}

	private function change_email($email) {

		DEBUG::add('Update the email on accounts table');

		Database::execute(
			"UPDATE `accounts` SET `email` = ?, `lastIp` = ? WHERE `login` = ? LIMIT 1",
			[$email, $this->PseudoIpv4($_SERVER['REMOTE_ADDR']), $this->login]
		);

		$this->email = $email;

		return true;
	}

	private function get_email ()
	{
		DEBUG::add('Get the email of the user');

		return Database::fetchValue(
			"SELECT `email` FROM `accounts` WHERE `login` = ? LIMIT 1",
			[$this->login]
		);
	}

	private function valid_email($login, $key) {
		DEBUG::add('Check if there are an activation key on account_data');

		$count = Database::fetchValue(
			"SELECT COUNT(`var`) FROM `account_data` WHERE `account_name` = ? AND `var` = ? LIMIT 1",
			[$login, $key]
		);

		if ($count === '0' || $count === null)
			return false;

		DEBUG::add('Get the account name linked with the activation key');

		return Database::fetchValue(
			"SELECT `value` FROM `account_data` WHERE `account_name` = ? AND `var` = ? LIMIT 1",
			[$login, $key]
		);
	}

	public function email_validation($login, $key) {

		if (!($email = $this->valid_email($login, $key)))
			return false;

		DEBUG::add('Delete activation key from account_data table');

		Database::execute(
			"DELETE FROM `account_data` WHERE `account_name` = ? AND `var` = ? LIMIT 1",
			[$login, $key]
		);

		if ($this->valid_key($login, $key))
			return false;

		EMAIL::OP()->operator($login, 'modified_email_activation', $email, NULL);		// warn the old email box

		$this->change_email($email);

		EMAIL::OP()->operator($login, 'modified_email_activation', $email, $email);		// warn the new email box

		return true;
	}

	public function edit_email ($pass,$email,$reemail)
	{

		if($this->password != $this->l2j_encrypt($pass)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_VPASS1'));
			return false;
		}

		if(!$this->verif_email($email)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_MAIL'));
			return false;
		}

		if($this->is_email_exist($email)) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_EMAIL_INUSE'));
			return false;
		}

		if ($email != $reemail) {
			MSG::add_error(LANG::getInstance()->i18n('_REGWARN_VEMAIL1'));
			return false;
		}

		$code = $this->gen_img_cle(10);

		DEBUG::add('Insert the activation key on account_data for checking email');

		Database::execute(
			"REPLACE INTO `account_data` (`account_name`, `var`, `value`) VALUES (?, ?, ?)",
			[$this->login, $code, $email]
		);

		if(!CONFIG::g()->core_act_email) {
			$this->email_validation($this->login, $code);
		}else{
			EMAIL::OP()->operator($this->login, 'email_validation', $code, $email);
		}

		return true;
	}

	private function is_logged () {
		return (!empty($_SESSION['acm'])) ? true : false;
	}

	public function loggout () {
		$_SESSION = array();
		session_destroy();
		return true;
	}

	public function gen_img_cle($num = 4) {
		$key = '';
		$chaine = "123456789";
		for ($i=0;$i<$num;$i++) $key.= $chaine[rand()%strlen($chaine)];
		return $key;
	}

	// ----------------------------------------------------------------
	// Copyright to the first account manager
		public function l2j_encrypt ($pass) {return base64_encode( hash( "whirlpool", $pass, TRUE ) );}
	// ----------------------------------------------------------------


    public function PseudoIpv4($ipv6)
    {
        $ipv6Addr = @inet_pton($ipv6);
        if ($ipv6Addr === false || strlen($ipv6Addr) !== 16) {
            return $ipv6; //throw new WrongIpv6Exception(sprintf('IPv6 address expected, "%s" given.', $ipv6));
        }

        if (strpos($ipv6Addr, chr(0x20).chr(0x02)) === 0) { // 6to4 addresses starting with 2002:
            $ipv4Addr = substr($ipv6Addr, 2, 4);
        } else {
            $ipv4Addr = '';
            for ($i = 0; $i < 8; $i += 2) { // Get first 8 bytes because the most of ISP provide addresses with mask /64
                $ipv4Addr .= chr(ord($ipv6Addr[$i]) ^ ord($ipv6Addr[$i + 1]));
            }
            $ipv4Addr[0] = chr(ord($ipv4Addr[0]) | 240); // Class E space
        }
        $ipv4 = inet_ntop($ipv4Addr);

        return $ipv4;
    }

}
?>

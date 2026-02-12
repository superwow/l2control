<?php

defined( '_ACM_VALID' ) or die( 'Direct Access to this location is not allowed.' );



class core {

	public function __construct() {
		$this->secure_post();
	}

	public function index() {
		if(ACCOUNT::load()->verif())
			$this->show_account();
		else
			$this->show_login();
	}

	public function loggout() {
		ACCOUNT::load()->loggout();
		MSG::add_valid(LANG::getInstance()->i18n('_logout'));
		$this->index();
	}

	public function login() {

		if(empty($_POST['Luser']) || empty($_POST['Lpwd']))
		{
			MSG::add_error(LANG::getInstance()->i18n('_no_id_no_pwd'));
		}else{

			$this->secure_post();

			if(!ACCOUNT::load()->auth($_POST['Luser'], $_POST['Lpwd'], $_POST['Limage'] ?? null))
				MSG::add_error(LANG::getInstance()->i18n('_wrong_auth'));
		}

		$this->index();
	}

	public function show_login() {
		SmartyObject::getInstance()->assign('vm', array(
		    'exist_account'		=> LANG::getInstance()->i18n('_exist_account'),
		    'account_length'	=> CONFIG::g()->core_id_limit,
		    'password_length'	=> CONFIG::g()->core_pwd_limit,
		    'account'			=> LANG::getInstance()->i18n('_account'),
		    'password'			=> LANG::getInstance()->i18n('_password'),
		    'login_button'		=> LANG::getInstance()->i18n('_login_button'),
		    'forgot_password'	=> LANG::getInstance()->i18n('_forgot_password'),
		    'new_account'		=> LANG::getInstance()->i18n('_new_account'),
		    'new_account_text'	=> LANG::getInstance()->i18n('_new_account_text'),
		    'create_button'		=> LANG::getInstance()->i18n('_create_button')
		));
		if(CONFIG::g()->core_act_img) {
			SmartyObject::getInstance()->assign('image', 'image');
		}
		SmartyObject::getInstance()->setTemplate('form.tpl');
	}

	public function show_account() {
		
		SmartyObject::getInstance()->assign('vm', array(
			'title_page'		=> LANG::getInstance()->i18n('_title_page'),
		    'account_text'		=> LANG::getInstance()->i18n('_chg_pwd_text')
		));
		
		$modules = array();
		
		$modules[] = array('name'=>LANG::getInstance()->i18n('_chg_pwd'), 'link'=>'?action=show_chg_pwd'.$this->session_url());
		
		if ($this->allow_char_mod())
			$modules[] = array('name'=>LANG::getInstance()->i18n('_accounts_services'), 'link'=>'?action=acc_serv'.$this->session_url());
		
		if (ACCOUNT::load()->can_chg_email())
			$modules[] = array('name'=>LANG::getInstance()->i18n('_chg_email'), 'link'=>'?action=show_chg_email'.$this->session_url());
		
		$modules[] = array('name'=>LANG::getInstance()->i18n('_logout_link'), 'link'=>'?action=loggout'.$this->session_url());
		
		SmartyObject::getInstance()->assign('modules', $modules);
		
		SmartyObject::getInstance()->register_block('dynamic', 'smarty_block_dynamic', false);
		SmartyObject::getInstance()->setTemplate('account.tpl');
	}

	public function registration() {

		if(ACCOUNT::load()->create($_POST['Luser'], $_POST['Lpwd'], $_POST['Lpwd2'], $_POST['Lemail'], $_POST['Limage'] ?? null)) {
			$this->show_login();
		}else{
			$this->show_create(true);
		}
	}

	public function show_ack(){
		SmartyObject::getInstance()->assign('vm', array(
		    'terms_and_condition'		=> LANG::getInstance()->i18n('_TERMS_AND_CONDITION'),
		    'return'					=> LANG::getInstance()->i18n('_return'),
		    'accept_button'				=> LANG::getInstance()->i18n('_accept_button')
		));
		SmartyObject::getInstance()->setTemplate('ack.tpl');
	}

	public function show_create($acka = false) {

		$ack = (($_POST['ack'] ?? '') === 'ack') ? true : false;
		$ack = ($acka) ? true : $ack;

		if(CONFIG::g()->core_ack_cond && !$ack) {
			$this->show_ack();
			return false;
		}
		
		SmartyObject::getInstance()->assign('vm', array(
		    'new_account'			=> LANG::getInstance()->i18n('_new_account'),
		    'new_account_text'		=> LANG::getInstance()->i18n('_new_account_text2'),
		    'account_length'		=> CONFIG::g()->core_id_limit,
		    'password_length'		=> CONFIG::g()->core_pwd_limit,
		    'account'				=> LANG::getInstance()->i18n('_account'),
		    'password'				=> LANG::getInstance()->i18n('_password'),
		    'password2'				=> LANG::getInstance()->i18n('_password2'),
		    'email'					=> LANG::getInstance()->i18n('_email'),
		    'image_control_desc'	=> LANG::getInstance()->i18n('_image_control_desc'),
		    'return'				=> LANG::getInstance()->i18n('_return'),
		    'create_button'			=> LANG::getInstance()->i18n('_create_button'),
		    'post_id'				=> $_POST['Luser'] ?? '',
		    'post_email'			=> $_POST['Lemail'] ?? ''
		));
		if(CONFIG::g()->core_act_img) {
			SmartyObject::getInstance()->assign('image', 'image');
		}
		SmartyObject::getInstance()->setTemplate('create.tpl');
	}

	public function show_forget() {
		SmartyObject::getInstance()->assign('vm', array(
		    'forgot_pwd'			=> LANG::getInstance()->i18n('_forgot_pwd'),
		    'forgot_pwd_text'		=> LANG::getInstance()->i18n('_forgot_pwd_text'),
		    'account_length'		=> CONFIG::g()->core_id_limit,
		    'account'				=> LANG::getInstance()->i18n('_account'),
		    'email'					=> LANG::getInstance()->i18n('_email'),
		    'image_control_desc'	=> LANG::getInstance()->i18n('_image_control_desc'),
		    'return'				=> LANG::getInstance()->i18n('_return'),
		    'forgot_button'			=> LANG::getInstance()->i18n('_forgot_button'),
		    'post_id'				=> $_POST['Luser'] ?? '',
		    'post_email'			=> $_POST['Lemail'] ?? ''
		));
		if(CONFIG::g()->core_act_img) {
			SmartyObject::getInstance()->assign('image', 'images');
		}
		SmartyObject::getInstance()->setTemplate('forgot_pwd.tpl');
	}

	public function forgot_pwd() {

		if(ACCOUNT::load()->forgot_pwd($_POST['Luser'], $_POST['Lemail'], $_POST['Limage'] ?? null)) {
			MSG::add_valid(LANG::getInstance()->i18n('_password_request'));
			$this->index();
		}else{
			$this->show_forget();
		}

		return true;
	}

	public function forgot_pwd_email() {

		if(ACCOUNT::load()->forgot_pwd2($_GET['login'], $_GET['key'])) {
			MSG::add_valid(LANG::getInstance()->i18n('_password_reseted'));
			$this->index();
		}else{
			MSG::add_error(LANG::getInstance()->i18n('_control'));
			$this->show_forget();
		}

		return true;
	}

	public function chg_pwd_form() {

		if(!ACCOUNT::load()->verif()) {
			MSG::add_error(LANG::getInstance()->i18n('_WARN_NOT_LOGGED'));
			$this->index();
			return;
		}

		$account = unserialize($_SESSION['acm']);

		if(ACCOUNT::load()->edit_password($_POST['Lpwdold'], $_POST['Lpwd'], $_POST['Lpwd2'])) {
			MSG::add_valid(LANG::getInstance()->i18n('_change_pwd_valid'));
			$this->show_account();
		}
		else
		{
			$this->show_chg_pwd();
		}
	}

	public function show_chg_pwd() {
		
		if(!ACCOUNT::load()->verif()) {
			MSG::add_error(LANG::getInstance()->i18n('_WARN_NOT_LOGGED'));
			$this->index();
			return;
		}

		SmartyObject::getInstance()->assign('vm', array(
		    'chg_pwd'				=> LANG::getInstance()->i18n('_chg_pwd'),
		    'chg_pwd_text'			=> LANG::getInstance()->i18n('_chg_pwd_text'),
		    'password_length'		=> CONFIG::g()->core_pwd_limit,
		    'passwordold'			=> LANG::getInstance()->i18n('_passwordold'),
		    'password'				=> LANG::getInstance()->i18n('_password'),
		    'password2'				=> LANG::getInstance()->i18n('_password2'),
		    'return'				=> LANG::getInstance()->i18n('_return'),
		    'chg_button'			=> LANG::getInstance()->i18n('_chg_button')
		));
		
		SmartyObject::getInstance()->setTemplate('chg_pwd.tpl');
	}

	public function chg_email_form() {

		if(!ACCOUNT::load()->verif()) {
			MSG::add_error(LANG::getInstance()->i18n('_WARN_NOT_LOGGED'));
			$this->index();
			return;
		}

		if(!ACCOUNT::load()->can_chg_email()) {
			$this->index();
			return;
		}

		if(ACCOUNT::load()->edit_email($_POST['Lpwd'], $_POST['Lemail'], $_POST['Lemail2'])) {
			MSG::add_valid(LANG::getInstance()->i18n('_change_email_valid'));
			$this->show_account();
		}
		else
		{
			$this->show_chg_email();
		}
	}

	public function show_chg_email() {
		
		if(!ACCOUNT::load()->verif()) {
			MSG::add_error(LANG::getInstance()->i18n('_WARN_NOT_LOGGED'));
			$this->index();
			return;
		}

		if(!ACCOUNT::load()->can_chg_email()) {
			$this->index();
			return;
		}
		
		SmartyObject::getInstance()->assign('vm', array(
		    'chg_pwd'				=> LANG::getInstance()->i18n('_chg_email'),
		    'chg_pwd_text'			=> LANG::getInstance()->i18n('_chg_email_text'),
		    'password_length'		=> CONFIG::g()->core_pwd_limit,
		    'password'				=> LANG::getInstance()->i18n('_password'),
		    'email'					=> LANG::getInstance()->i18n('_email'),
		    'email2'				=> LANG::getInstance()->i18n('_email2'),
		    'return'				=> LANG::getInstance()->i18n('_return'),
		    'chg_button'			=> LANG::getInstance()->i18n('_chg_button')
		));
		
		SmartyObject::getInstance()->setTemplate('chg_email.tpl');

	}

	public function email_validation() {

		if(ACCOUNT::load()->email_validation($_GET['login'], $_GET['key'])) {
			MSG::add_valid(LANG::getInstance()->i18n('_change_email_valid'));
		}else{
			MSG::add_error(LANG::getInstance()->i18n('_control'));
		}
		
		$this->index();

		return true;
	}
	
	public function acc_serv(){
		
		if(!ACCOUNT::load()->verif()) {
			MSG::add_error(LANG::getInstance()->i18n('_WARN_NOT_LOGGED'));
			$this->index();
			return;
		}
		
		if(!$this->allow_char_mod()) {
			MSG::add_error(LANG::getInstance()->i18n('_acc_serv_off'));
			$this->index();
			return;
		}
		
		SmartyObject::getInstance()->assign('vm', array(
			'select_item'			=> LANG::getInstance()->i18n('_accounts_services'),
			'select_desc'			=> '',
			'return'				=> LANG::getInstance()->i18n('_return'),
		));
		
		$items = array();
		
		if(CONFIG::g()->service_fix)
			$items[] = array('id' => 0, 'name' => LANG::getInstance()->i18n('_character_fix'), 'link' => '?action=char_fix_l'.$this->session_url());
		
		if(CONFIG::g()->service_unstuck)
			$items[] = array('id' => 1, 'name' => LANG::getInstance()->i18n('_character_unstuck'), 'link' => '?action=char_unstuck_l'.$this->session_url());
		
		if(CONFIG::g()->service_sex)
			$items[] = array('id' => 1, 'name' => LANG::getInstance()->i18n('_character_sex'), 'link' => '?action=char_sex_l'.$this->session_url());
		
		if(CONFIG::g()->service_name)
			$items[] = array('id' => 1, 'name' => LANG::getInstance()->i18n('_character_name'), 'link' => '?action=char_name_l'.$this->session_url());
		
		SmartyObject::getInstance()->assign('items', $items);
		
		SmartyObject::getInstance()->register_block('dynamic', 'smarty_block_dynamic', false);
		
		SmartyObject::getInstance()->setTemplate('select.tpl');
	}
	
	private function char_ufl($mod = null){
		
		if(!ACCOUNT::load()->verif()) {
			MSG::add_error(LANG::getInstance()->i18n('_WARN_NOT_LOGGED'));
			$this->index();
			return;
		}
		
		if(is_null($mod)) {$this->index(); return;} // shouldn't happend
		
		$mode = 'service_'.$mod;
		
		if(!$this->allow_char_mod() || !CONFIG::g()->$mode) {
			MSG::add_error(LANG::getInstance()->i18n('_acc_serv_off'));
			$this->index();
			return;
		}
		
		unset($worlds);
		$worlds = WORLD::load_worlds(); // charging world
		
		SmartyObject::getInstance()->assign('vm', array(
			'select_item'			=> LANG::getInstance()->i18n('_character_'.$mod),
			'select_desc'			=> LANG::getInstance()->i18n('_character_'.$mod.'_desc'),
		    'return'				=> LANG::getInstance()->i18n('_return')
		));
		
		$items = array();
		
		foreach  ($worlds as $world) {
			foreach  ($world->get_chars() as $char) {
				$items[] = array('id' => $world->get_id(), 'name' => $world->get_name() . ' : ' .$char->getName(), 'link' => '?action=char_'.$mod.'&wid='.$world->get_id().'&cid='.$char->getId().$this->session_url());
			}
		}
		
		if(empty($items))
			$items[] = array('id' => 0, 'name' => LANG::getInstance()->i18n('_any_character'), 'link' => '?action=acc_serv'.$this->session_url());
		
		SmartyObject::getInstance()->assign('items', $items);
		
		SmartyObject::getInstance()->register_block('dynamic', 'smarty_block_dynamic', false);
		
		SmartyObject::getInstance()->setTemplate('select.tpl');
	}
	
	public function char_unstuck_l() {
		$this->char_ufl('unstuck');
	}
	
	public function char_fix_l() {
		$this->char_ufl('fix');
	}
	
	public function char_sex_l() {
		$this->char_ufl('sex');
	}
	
	public function char_name_l() {
		$this->char_ufl('name');
	}
	
	private function char_uf($mod = null) {
		
		if(!ACCOUNT::load()->verif()) {
			MSG::add_error(LANG::getInstance()->i18n('_WARN_NOT_LOGGED'));
			$this->index();
			return;
		}
		
		if(is_null($mod)) {$this->index(); return;}

		$mode = 'service_'.$mod;
		
		if(!$this->allow_char_mod() || !CONFIG::g()->$mode) {
			MSG::add_error(LANG::getInstance()->i18n('_acc_serv_off'));
			$this->index();
			return;
		}
		
		if(empty($_GET['wid']) || empty($_GET['cid'])) {
			MSG::add_error(LANG::getInstance()->i18n('_error_select_char'));
			$this->index();
			return;
		}
		
		$char = new character($_GET['cid'], $_GET['wid']);
		
		if(is_null($char->getId())) {
			MSG::add_error(LANG::getInstance()->i18n('_error_select_char'));
			$this->index();
			return;
		}
		
		SmartyObject::getInstance()->assign('vm', array(
			'select_item'	=> LANG::getInstance()->i18n('_character_'.$mod),
			'select_desc'	=> sprintf(LANG::getInstance()->i18n('_character_'.$mod.'_confirm'), $char->getName(), world::get_name_world($char->getWorldId()), LANG::getInstance()->i18n('_character_sex_'.$char->getGender()), LANG::getInstance()->i18n('_character_sex_'.((int)(!$char->getGender())))),
		    'return'		=> LANG::getInstance()->i18n('_return')
		));
		
		$items = array();
		$items[] = array('id' => 1, 'name' => LANG::getInstance()->i18n('_confirm'), 'link' => '?action=char_'.$mod.'_confirm&wid='.$char->getWorldId().'&cid='.$char->getId().$this->session_url());
		$items[] = array('id' => 1, 'name' => LANG::getInstance()->i18n('_back'), 'link' => '?action=char_'.$mod.'_l'.$this->session_url());
		SmartyObject::getInstance()->assign('items', $items);
		
		SmartyObject::getInstance()->register_block('dynamic', 'smarty_block_dynamic', false);
		SmartyObject::getInstance()->setTemplate('select.tpl');
	}
	
	public function char_unstuck() {
		$this->char_uf('unstuck');
	}
	
	public function char_fix() {
		$this->char_uf('fix');
	}
	
	public function char_sex() {
		$this->char_uf('sex');
	}
	
	public function char_name() {
		$this->char_uf('name');
	}

	private function char_ufc($mod = null) {
		
		if(!ACCOUNT::load()->verif()) {
			MSG::add_error(LANG::getInstance()->i18n('_WARN_NOT_LOGGED'));
			$this->index();
			return;
		}
		
		if(is_null($mod)) {$this->index(); return;}
		
		$mode = 'service_'.$mod;
		
		if(!$this->allow_char_mod() || !CONFIG::g()->$mode) {
			MSG::add_error(LANG::getInstance()->i18n('_acc_serv_off'));
			$this->index();
			return;
		}
		
		if(empty($_GET['wid']) || empty($_GET['cid'])) {
			MSG::add_error(LANG::getInstance()->i18n('_error_select_char'));
			$this->index();
			return;
		}
		
		$char = new character($_GET['cid'], $_GET['wid']);

		if(!$char->$mod())
			MSG::add_error(LANG::getInstance()->i18n('_character_'.$mod.'_no'));
		else
			MSG::add_valid(LANG::getInstance()->i18n('_character_'.$mod.'_yes'));

		$this->index();

		return;
	}
	
	public function char_unstuck_confirm() {
		$this->char_ufc('unstuck');
	}
	
	public function char_fix_confirm() {
		$this->char_ufc('fix');
	}
	
	public function char_sex_confirm() {
		$this->char_ufc('sex');
	}
	
	public function char_name_confirm() {
		$this->char_ufc('name');
	}

	public function activation() {

		if(!ACCOUNT::load()->valid_account(htmlentities($_GET['key'])))
			MSG::add_error(LANG::getInstance()->i18n('_activation_control'));
		else
			MSG::add_valid(LANG::getInstance()->i18n('_account_actived'));

		$this->index();

		return;
	}
	
	// ====================================================================
	// Server Statistics Page
	// ====================================================================
	public function server_stats() {

		$stats = array(
			'total_accounts' => 0,
			'online_players' => 0,
			'total_characters' => 0,
			'total_clans' => 0
		);

		// Total accounts
		try {
			$val = Database::fetchValue("SELECT COUNT(`login`) FROM `accounts`");
			$stats['total_accounts'] = (int)($val ?? 0);
		} catch (Exception $e) {
			$stats['total_accounts'] = 0;
		}

		// Get game server stats
		$class_stats = array();
		$race_stats = array();

		$game_servers = $this->get_configured_game_servers();
		foreach ($game_servers as $gsId) {
			try {
				// Online players
				$val = Database::fetchValue(
					"SELECT COUNT(`charId`) FROM `characters` WHERE `online` = 1",
					[], $gsId
				);
				$stats['online_players'] += (int)($val ?? 0);

				// Total characters
				$val = Database::fetchValue(
					"SELECT COUNT(`charId`) FROM `characters`",
					[], $gsId
				);
				$stats['total_characters'] += (int)($val ?? 0);

				// Total clans
				$val = Database::fetchValue(
					"SELECT COUNT(`clan_id`) FROM `clan_data`",
					[], $gsId
				);
				$stats['total_clans'] += (int)($val ?? 0);

				// Class distribution
				$rows = Database::fetchAll(
					"SELECT `base_class`, COUNT(*) as cnt FROM `characters` GROUP BY `base_class` ORDER BY cnt DESC LIMIT 15",
					[], $gsId
				);
				foreach ($rows as $row) {
					$className = $this->get_class_name((int)$row['base_class']);
					if (isset($class_stats[$className])) {
						$class_stats[$className] += (int)$row['cnt'];
					} else {
						$class_stats[$className] = (int)$row['cnt'];
					}
				}
			} catch (Exception $e) {
				// Game server might not be configured
			}
		}

		// Convert class stats to template array with percentages
		arsort($class_stats);
		$total_chars = max(1, $stats['total_characters']);
		$class_arr = array();
		foreach ($class_stats as $name => $count) {
			$class_arr[] = array(
				'name' => $name,
				'count' => $count,
				'percent' => round(($count / $total_chars) * 100, 1)
			);
		}

		SmartyObject::getInstance()->assign('vm', array(
			'stat_accounts'				=> LANG::getInstance()->i18n('_stat_accounts'),
			'stat_online'				=> LANG::getInstance()->i18n('_stat_online'),
			'stat_characters'			=> LANG::getInstance()->i18n('_stat_characters'),
			'stat_clans'				=> LANG::getInstance()->i18n('_stat_clans'),
			'stat_class_distribution'	=> LANG::getInstance()->i18n('_stat_class_distribution'),
			'stat_class_name'			=> LANG::getInstance()->i18n('_stat_class_name'),
			'stat_count'				=> LANG::getInstance()->i18n('_stat_count'),
			'stat_percent'				=> LANG::getInstance()->i18n('_stat_percent'),
			'stat_race_distribution'	=> LANG::getInstance()->i18n('_stat_race_distribution'),
			'return'					=> LANG::getInstance()->i18n('_return'),
		));

		SmartyObject::getInstance()->assign('stats', $stats);
		SmartyObject::getInstance()->assign('class_stats', $class_arr);

		SmartyObject::getInstance()->register_block('dynamic', 'smarty_block_dynamic', false);
		SmartyObject::getInstance()->setTemplate('server_stats.tpl');
	}

	// ====================================================================
	// Top Players Page
	// ====================================================================
	public function top_players() {

		$players = array();
		$pvp_players = array();

		$game_servers = $this->get_configured_game_servers();
		foreach ($game_servers as $gsId) {
			try {
				// Top by level
				$rows = Database::fetchAll(
					"SELECT c.`char_name`, c.`level`, c.`base_class`, c.`online`, c.`clanid`,
					        COALESCE(cd.`clan_name`, '') as clan_name
					 FROM `characters` c
					 LEFT JOIN `clan_data` cd ON c.`clanid` = cd.`clan_id`
					 WHERE c.`accesslevel` = 0
					 ORDER BY c.`level` DESC, c.`exp` DESC
					 LIMIT 25",
					[], $gsId
				);

				$rank = count($players) + 1;
				foreach ($rows as $row) {
					$players[] = array(
						'rank' => $rank++,
						'char_name' => $row['char_name'],
						'level' => $row['level'],
						'class_name' => $this->get_class_name((int)$row['base_class']),
						'online' => (int)$row['online'],
						'clan_name' => $row['clan_name']
					);
				}

				// Top PvP
				$rows = Database::fetchAll(
					"SELECT `char_name`, `pvpkills`, `pkkills`, `base_class`
					 FROM `characters`
					 WHERE `accesslevel` = 0 AND `pvpkills` > 0
					 ORDER BY `pvpkills` DESC
					 LIMIT 15",
					[], $gsId
				);

				$rank = count($pvp_players) + 1;
				foreach ($rows as $row) {
					$pvp_players[] = array(
						'rank' => $rank++,
						'char_name' => $row['char_name'],
						'pvpkills' => $row['pvpkills'],
						'pkkills' => $row['pkkills'],
						'class_name' => $this->get_class_name((int)$row['base_class'])
					);
				}
			} catch (Exception $e) {
				// skip if game server not available
			}
		}

		SmartyObject::getInstance()->assign('vm', array(
			'top_title'		=> LANG::getInstance()->i18n('_top_title'),
			'top_desc'		=> LANG::getInstance()->i18n('_top_desc'),
			'top_name'		=> LANG::getInstance()->i18n('_top_name'),
			'top_level'		=> LANG::getInstance()->i18n('_top_level'),
			'top_class'		=> LANG::getInstance()->i18n('_top_class'),
			'top_clan'		=> LANG::getInstance()->i18n('_top_clan'),
			'top_status'	=> LANG::getInstance()->i18n('_top_status'),
			'top_pvp_title'	=> LANG::getInstance()->i18n('_top_pvp_title'),
			'return'		=> LANG::getInstance()->i18n('_return'),
		));

		SmartyObject::getInstance()->assign('players', $players);
		if (!empty($pvp_players)) {
			SmartyObject::getInstance()->assign('pvp_players', $pvp_players);
		}

		SmartyObject::getInstance()->register_block('dynamic', 'smarty_block_dynamic', false);
		SmartyObject::getInstance()->setTemplate('top_players.tpl');
	}

	// ====================================================================
	// World Map Page
	// ====================================================================
	public function world_map() {

		$towns = array(
			array('name' => 'Talking Island',    'x' => -84176,  'y' => 243382,  'z' => -3126),
			array('name' => 'Elven Village',     'x' => 45525,   'y' => 48376,   'z' => -3059),
			array('name' => 'Dark Elf Village',  'x' => 12181,   'y' => 16675,   'z' => -4580),
			array('name' => 'Orc Village',       'x' => -45232,  'y' => -113603, 'z' => -224),
			array('name' => 'Dwarven Village',   'x' => 115074,  'y' => -178115, 'z' => -880),
			array('name' => 'Gludio',            'x' => -14138,  'y' => 122042,  'z' => -2988),
			array('name' => 'Gludin',            'x' => -82856,  'y' => 150901,  'z' => -3128),
			array('name' => 'Dion',              'x' => 18823,   'y' => 145048,  'z' => -3126),
			array('name' => 'Giran',             'x' => 81236,   'y' => 148638,  'z' => -3469),
			array('name' => 'Oren',              'x' => 80853,   'y' => 54653,   'z' => -1524),
			array('name' => 'Aden',              'x' => 147391,  'y' => 25967,   'z' => -2012),
			array('name' => 'Hunters Village',   'x' => 117163,  'y' => 76511,   'z' => -2712),
			array('name' => 'Heine',             'x' => 111381,  'y' => 219064,  'z' => -3543),
			array('name' => 'Rune',              'x' => 43894,   'y' => -48330,  'z' => -797),
			array('name' => 'Goddard',           'x' => 148558,  'y' => -56030,  'z' => -2781),
			array('name' => 'Schuttgart',        'x' => 87331,   'y' => -142842, 'z' => -1317),
			array('name' => 'Floran Village',    'x' => 18823,   'y' => 145048,  'z' => -3126),
			array('name' => 'Primeval Isle',     'x' => 10468,   'y' => -24569,  'z' => -3645),
			array('name' => 'Kamael Village',    'x' => -118092, 'y' => 46955,   'z' => 360),
		);

		SmartyObject::getInstance()->assign('vm', array(
			'map_title'				=> LANG::getInstance()->i18n('_map_title'),
			'map_desc'				=> LANG::getInstance()->i18n('_map_desc'),
			'map_players'			=> LANG::getInstance()->i18n('_map_players'),
			'map_legend'			=> LANG::getInstance()->i18n('_map_legend'),
			'map_legend_town'		=> LANG::getInstance()->i18n('_map_legend_town'),
			'map_legend_location'	=> LANG::getInstance()->i18n('_map_legend_location'),
			'return'				=> LANG::getInstance()->i18n('_return'),
		));

		SmartyObject::getInstance()->assign('towns', $towns);

		SmartyObject::getInstance()->register_block('dynamic', 'smarty_block_dynamic', false);
		SmartyObject::getInstance()->setTemplate('world_map.tpl');
	}

	// ====================================================================
	// Helper: Get configured game server IDs
	// ====================================================================
	private function get_configured_game_servers() {
		$ids = array();
		try {
			$rows = Database::fetchAll("SELECT `server_id` FROM `gameservers`");
			foreach ($rows as $row) {
				$gs = CONFIG::g()->select_game_server($row['server_id']);
				if (!empty($gs)) {
					$ids[] = (int)$row['server_id'];
				}
			}
		} catch (Exception $e) {
			// if no gameservers table, return empty
		}
		return $ids;
	}

	// ====================================================================
	// Helper: Get L2 class name by base_class ID (High Five)
	// ====================================================================
	private function get_class_name($classId) {
		$classes = array(
			0 => 'Human Fighter', 1 => 'Warrior', 2 => 'Gladiator', 3 => 'Warlord',
			4 => 'Human Knight', 5 => 'Paladin', 6 => 'Dark Avenger',
			7 => 'Rogue', 8 => 'Treasure Hunter', 9 => 'Hawkeye',
			10 => 'Human Mystic', 11 => 'Human Wizard', 12 => 'Sorceror', 13 => 'Necromancer',
			14 => 'Warlock', 15 => 'Cleric', 16 => 'Bishop', 17 => 'Prophet',
			18 => 'Elven Fighter', 19 => 'Elven Knight', 20 => 'Temple Knight',
			21 => 'Swordsinger', 22 => 'Elven Scout', 23 => 'Plains Walker',
			24 => 'Silver Ranger', 25 => 'Elven Mystic', 26 => 'Elven Wizard',
			27 => 'Spellsinger', 28 => 'Elemental Summoner', 29 => 'Elven Oracle',
			30 => 'Elven Elder', 31 => 'Dark Fighter', 32 => 'Palus Knight',
			33 => 'Shillien Knight', 34 => 'Bladedancer', 35 => 'Assassin',
			36 => 'Abyss Walker', 37 => 'Phantom Ranger', 38 => 'Dark Mystic',
			39 => 'Dark Wizard', 40 => 'Spellhowler', 41 => 'Phantom Summoner',
			42 => 'Shillien Oracle', 43 => 'Shillien Elder',
			44 => 'Orc Fighter', 45 => 'Orc Raider', 46 => 'Destroyer',
			47 => 'Orc Monk', 48 => 'Tyrant', 49 => 'Orc Mystic',
			50 => 'Orc Shaman', 51 => 'Overlord', 52 => 'Warcryer',
			53 => 'Dwarven Fighter', 54 => 'Scavenger', 55 => 'Bounty Hunter',
			56 => 'Artisan', 57 => 'Warsmith',
			88 => 'Duelist', 89 => 'Dreadnought', 90 => 'Phoenix Knight',
			91 => 'Hell Knight', 92 => 'Sagittarius', 93 => 'Adventurer',
			94 => 'Archmage', 95 => 'Soultaker', 96 => 'Arcana Lord',
			97 => 'Cardinal', 98 => 'Hierophant',
			99 => 'Eva Templar', 100 => 'Sword Muse', 101 => 'Wind Rider',
			102 => 'Moonlight Sentinel', 103 => 'Mystic Muse',
			104 => 'Elemental Master', 105 => 'Eva Saint',
			106 => 'Shillien Templar', 107 => 'Spectral Dancer',
			108 => 'Ghost Hunter', 109 => 'Ghost Sentinel',
			110 => 'Storm Screamer', 111 => 'Spectral Master',
			112 => 'Shillien Saint',
			113 => 'Titan', 114 => 'Grand Khavatari', 115 => 'Dominator',
			116 => 'Doomcryer', 117 => 'Fortune Seeker', 118 => 'Maestro',
			123 => 'Male Soldier', 124 => 'Female Soldier',
			125 => 'Trooper', 126 => 'Warder',
			127 => 'Berserker', 128 => 'Male Soulbreaker',
			129 => 'Female Soulbreaker', 130 => 'Arbalester',
			131 => 'Doombringer', 132 => 'Male Soulhound',
			133 => 'Female Soulhound', 134 => 'Trickster',
			135 => 'Inspector', 136 => 'Judicator',
		);

		return $classes[$classId] ?? 'Unknown ('.$classId.')';
	}

	private function allow_char_mod() {
		CONFIG::g()->cb('service_name', false);
	
		if(SID != '')					// SID by URL aren't safe we prohibit accounts services when we can't use cookies
			return false;
		
		if(!CONFIG::g()->service_allow)
			return false;
		
		if(!CONFIG::g()->service_fix && !CONFIG::g()->service_unstuck && !CONFIG::g()->service_name && !CONFIG::g()->service_sex)
			return false;
		
		return true;
	}
	
	private function session_url() {
		if(SID != '')
			return '&'.(SID);
		
		return '';
	}

	private function secure_post() {

		if (empty($_POST)) return;

		foreach($_POST as $key => $value) {
			$value = (string)($value ?? '');

			if ($key === 'Luser')
				$_POST[$key] = substr($value, 0, CONFIG::g()->core_id_limit);

			if ($key === 'Lpwd')
				$_POST[$key] = substr($value, 0, CONFIG::g()->core_pwd_limit);

			if ($key === 'Lpwd2')
				$_POST[$key] = substr($value, 0, CONFIG::g()->core_pwd_limit);

			if ($key === 'Lpwdold')
				$_POST[$key] = substr($value, 0, CONFIG::g()->core_pwd_limit);

			if ($key === 'Lemail')
				$_POST[$key] = substr($value, 0, CONFIG::g()->core_email_limit);

			if ($key === 'Lemail2')
				$_POST[$key] = substr($value, 0, CONFIG::g()->core_email_limit);

			if ($key === 'Limage')
				$_POST[$key] = substr($value, 0, 5);

			if ($key === 'key')
				$_GET[$key] = substr($value, 0, 10);

			if (!($key === 'wid' && is_numeric($value)))
				$_GET[$key] = NULL;

			if (!($key === 'cid' && is_numeric($value)))
				$_GET[$key] = NULL;

		}

		$_POST = array_map(function($v) { return htmlentities((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }, $_POST);
		$_POST = array_map(function($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }, $_POST);

		return;
	}

	public function gen_img_cle($num = 5) {
		$key = '';
		$chaine = "ABCDEF123456789";
		for ($i=0;$i<$num;$i++) $key.= $chaine[rand()%strlen($chaine)];
		$_SESSION['code'] = $key;
	}
}
?>
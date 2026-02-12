<?php

defined( '_ACM_VALID' ) or die( 'Direct Access to this location is not allowed.' );

define ('DEBUG', false);								// Enable debug mode ; for set on replace false by true
define ('LOG', true);									// Enable log mode ; for set off replace true by false

//##########################################################################################
//############################### Login Server Configuration ###############################
//##########################################################################################

CONFIG::g()->ca('login_server', array(
									'hostname' => '',					// login host database DB
									'user' => '',							// login user
									'password' => '',							// login password
									'database' => ''						// login database name
								));

//##########################################################################################
//############################### Game Server Configuration ################################
//##########################################################################################

CONFIG::g()->add_game_server(array(
								'id' => 1,										// game id
								'hostname' => '',						// game host database DB
								'user' => '',								// game user
								'password' => '',								// game password
								'database' => ''							// game database name
							));

/*
CONFIG::g()->add_game_server(array(
								'id' => 2,										// game id (must be same as registred in gameservers table)
								'hostname' => 'localhost',						// game host database DB
								'user' => 'root',								// game user
								'password' => '',								// game password
								'database' => 'l2jdb'							// game database name
							));
*/

//##########################################################################################
//################################### Web Configuration ####################################
//##########################################################################################

CONFIG::g()->cs('core_server_name', 'server name');							// server name

CONFIG::g()->cs('core_iso_type', 'utf-8');										// set the charset type

CONFIG::g()->cb('core_act_img', true);											// Activate image verification ; set false if you want deactivate

CONFIG::g()->ci('core_spam_try', 5);											// We allow to the user 5 times unsuccessfull login after that system block him IP.
CONFIG::g()->ci('core_spam_time', 30);											// Need to wait 30 minutes after been blocked by anti spam system

CONFIG::g()->cb('core_act_email', true);										// Activate email verification ; set false if you want deactivate
CONFIG::g()->cb('core_same_email', true);										// Allow to have same email with different account ; set false if you want deactivate
CONFIG::g()->cb('core_can_chg_email', false);									// User can change email ; set false if you want prohibit

CONFIG::g()->cbi('core_acc_limit', false);										// How many account can be registered ; set false for unlimited

CONFIG::g()->cs('core_language', 'russian');									// language use by the Account Manager ; name of language file in language folder

CONFIG::g()->cb('core_ack_cond', true);										// User must accept before register ; conditions can be edited on the language file

CONFIG::g()->cs('core_template', 'l2moon');										// template directory (l2moon, kamael, black, blue, gracia, pro)

CONFIG::g()->cb('core_interlude', false);										// If you are using interlude database set to true

//##########################################################################################
//################################### Characters Config ####################################
//##########################################################################################

CONFIG::g()->cb('service_allow', true);										// Enable Account Services
CONFIG::g()->cb('service_allow_with_karma', true);								// Enable Account Services for player who have karma

CONFIG::g()->cs('service_server_name', './servername.xml');						// ServerName list (refer to the l2j xml list on datapack)

CONFIG::g()->cb('service_name', false);											// Enable Account services feature (change name)
CONFIG::g()->cs('service_name_regex', '`^[[:alnum:]]{3,16}$`');					// allow alphanumeric character in char name (3-16 char.)

CONFIG::g()->cb('service_sex', false);											// Enable Account services feature (change gender)
CONFIG::g()->ci('service_sex_time', 7);											// Need to wait 7 days after change gender before to change another time
CONFIG::g()->ca('service_sex_item_female', array(8559,8913,8917));				// Check female-use only items before to change gender
CONFIG::g()->ca('service_sex_item_male', array(8923));							// Check male-use only items before to change gender

CONFIG::g()->cb('service_fix', false);											// Enable fixing feature for offline character
CONFIG::g()->ci('service_fix_time', 24);										// Need to wait 24 hours before to use fix or unstuck against

CONFIG::g()->cb('service_unstuck', true);										// Enable unstuck feature for offline character
CONFIG::g()->cb('service_unstuck_static', false);								// Use static coords when using unstuck and fix feature
CONFIG::g()->ca('service_unstuck_default', array(0,0,0));						// When coord static enable tp player to the coords (x,y,z)

//##########################################################################################
//######################################### Email ##########################################
//##########################################################################################

CONFIG::g()->ce('email_from', 'noreply@mail');								// Specify an adress email source

CONFIG::g()->cb('email_smtp_use', true);										// Set to true if you want use an smtp server
CONFIG::g()->cs('email_smtp_address', 'ssl://');						// Address of the smtp server
CONFIG::g()->ci('email_smtp_port', 465 );											// Port of the smtp server
CONFIG::g()->cs('email_smtp_login', 'noreply@mail');										// login of the smtp server if you need authentication
CONFIG::g()->cs('email_smtp_password', '');										// password of the smtp server if you need authentication
CONFIG::g()->cs('email_smtp_domain', '');										// domain of your host

//##########################################################################################
//####################################### End Config #######################################
//##########################################################################################

?>
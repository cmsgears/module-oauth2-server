<?php
/**
 * This file is part of CMSGears Framework. Please view License file distributed
 * with the source code for license details.
 *
 * @link https://www.cmsgears.org/
 * @copyright Copyright (c) 2015 VulpineCode Technologies Pvt. Ltd.
 */

/**
 * The app migration inserts the database tables of oauth2 server module. It also insert the foreign
 * keys if FK flag of migration component is true.
 *
 * @since 1.0.0
 */
class m170115_110321_oauth2_server extends \cmsgears\core\common\base\Migration {

	// Public Variables

	public $fk;
	public $options;

	// Private Variables

	private $prefix;

	public function init() {

		// Table prefix
		$this->prefix = Yii::$app->migration->cmgPrefix;

		// Get the values via config
		$this->fk		= Yii::$app->migration->isFk();
		$this->options	= Yii::$app->migration->getTableOptions();

		// Default collation
		if( $this->db->driverName === 'mysql' ) {

			$this->options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
	}

	public function up() {

		// Client
		$this->upClient();

		if( $this->fk ) {

			$this->generateForeignKeys();
		}
	}

	private function upClient() {

		$this->createTable( $this->prefix . 'oauth_client', [
			'id' => $this->bigPrimaryKey( 20 ),
			'createdBy' => $this->bigInteger( 20 )->notNull(),
			'modifiedBy' => $this->bigInteger( 20 ),
			'clientId' => $this->string( Yii::$app->core->smallText )->notNull(),
			'clientSecret' => $this->string( Yii::$app->core->smallText )->defaultValue( null ),
			'redirectUri' => $this->string( Yii::$app->core->xxxLargeText )->notNull(),
			'grantTypes' => $this->string( Yii::$app->core->xLargeText )->notNull(),
			'scope' => $this->string( Yii::$app->core->xtraLargeText )->defaultValue( null ),
			'createdAt' => $this->dateTime()->notNull(),
			'modifiedAt' => $this->dateTime(),
			'content' => $this->mediumText(),
			'data' => $this->mediumText(),
			'gridCache' => $this->longText(),
			'gridCacheValid' => $this->boolean()->notNull()->defaultValue( false ),
			'gridCachedAt' => $this->dateTime()
		], $this->options );

		// Index for columns site, theme, creator and modifier
		$this->createIndex( 'idx_' . $this->prefix . 'oauth_client_creator', $this->prefix . 'oauth_client', 'createdBy' );
		$this->createIndex( 'idx_' . $this->prefix . 'oauth_client_modifier', $this->prefix . 'oauth_client', 'modifiedBy' );
	}

	private function generateForeignKeys() {

		// Client
		$this->addForeignKey( 'fk_' . $this->prefix . 'oauth_client_creator', $this->prefix . 'oauth_client', 'createdBy', $this->prefix . 'core_user', 'id', 'RESTRICT' );
		$this->addForeignKey( 'fk_' . $this->prefix . 'oauth_client_modifier', $this->prefix . 'oauth_client', 'modifiedBy', $this->prefix . 'core_user', 'id', 'SET NULL' );
	}

	public function down() {

		if( $this->fk ) {

			$this->dropForeignKeys();
		}

		$this->dropTable( $this->prefix . 'oauth_client' );
	}

	private function dropForeignKeys() {

		// Client
		$this->dropForeignKey( 'fk_' . $this->prefix . 'oauth_client_creator', $this->prefix . 'oauth_client' );
		$this->dropForeignKey( 'fk_' . $this->prefix . 'oauth_client_modifier', $this->prefix . 'oauth_client' );
	}

}

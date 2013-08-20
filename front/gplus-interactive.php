<?php

if ( !defined('RHMD_VERSION') )
{
    header('HTTP/1.0 403 Forbidden');
    die;
}

class RHMetaData_GPlusInteractive
{
    public $ctalabels = array('ACCEPT' => 'Accept'
    ,'ACCEPT_GIFT' => 'Accept gift'
    ,'ADD' => 'Add'
    ,'ADD_FRIEND' => 'Add friend'
    ,'ADD_ME' => 'Add me'
    ,'ADD_TO_CART' => 'Add to cart'
    ,'ADD_TO_CALENDAR' => 'Add to calendar'
    ,'ADD_TO_FAVORITES' => 'Add to favorites'
    ,'ADD_TO_QUEUE' => 'Add to queue'
    ,'ADD_TO_WISH_LIST' => 'Add to wish list'
    ,'ANSWER' => 'Answer'
    ,'ANSWER_QUIZ' => 'Answer quiz'
    ,'APPLY' => 'Apply'
    ,'ASK' => 'Ask'
    ,'ATTACK' => 'Attack'
    ,'BEAT' => 'Beat'
    ,'BID' => 'Bid'
    ,'BOOK' => 'Book'
    ,'BOOKMARK' => 'Bookmark'
    ,'BROWSE' => 'Browse'
    ,'BUY' => 'Buy'
    ,'CAPTURE' => 'Capture'
    ,'CHALLENGE' => 'Challenge'
    ,'CHANGE' => 'Change'
    ,'CHAT' => 'Chat'
    ,'CHECKIN' => 'Check-in'
    ,'COLLECT' => 'Collect'
    ,'COMMENT' => 'Comment'
    ,'COMPARE' => 'Compare'
    ,'COMPLAIN' => 'Complain'
    ,'CONFIRM' => 'Confirm'
    ,'CONNECT' => 'Connect'
    ,'CONTRIBUTE' => 'Contribute'
    ,'COOK' => 'Cook'
    ,'CREATE' => 'Create'
    ,'DEFEND' => 'Defend'
    ,'DINE' => 'Dine'
    ,'DISCOVER' => 'Discover'
    ,'DISCUSS' => 'Discuss'
    ,'DONATE' => 'Donate'
    ,'DOWNLOAD' => 'Download'
    ,'EARN' => 'Earn'
    ,'EAT' => 'Eat'
    ,'EXPLAIN' => 'Explain'
    ,'FIND' => 'Find'
    ,'FIND_A_TABLE' => 'Find a table'
    ,'FOLLOW' => 'Follow'
    ,'GET' => 'Get'
    ,'GIFT' => 'Gift'
    ,'GIVE' => 'Give'
    ,'GO' => 'Go'
    ,'HELP' => 'Help'
    ,'IDENTIFY' => 'Identify'
    ,'INSTALL' => 'Install'
    ,'INSTALL_APP' => 'Install app'
    ,'INTRODUCE' => 'Introduce'
    ,'INVITE' => 'Invite'
    ,'JOIN' => 'Join'
    ,'JOIN_ME' => 'Join me'
    ,'LEARN' => 'Learn'
    ,'LEARN_MORE' => 'Learn more'
    ,'LISTEN' => 'Listen'
    ,'MAKE' => 'Make'
    ,'MATCH' => 'Match'
    ,'MESSAGE' => 'Message'
    ,'OPEN' => 'Open'
    ,'OPEN_APP' => 'Open app'
    ,'OWN' => 'Own'
    ,'PAY' => 'Pay'
    ,'PIN' => 'Pin'
    ,'PIN_IT' => 'Pin it'
    ,'PLAN' => 'Plan'
    ,'PLAY' => 'Play'
    ,'PURCHASE' => 'Purchase'
    ,'RATE' => 'Rate'
    ,'READ' => 'Read'
    ,'READ_MORE' => 'Read more'
    ,'RECOMMEND' => 'Recommend'
    ,'RECORD' => 'Record'
    ,'REDEEM' => 'Redeem'
    ,'REGISTER' => 'Register'
    ,'REPLY' => 'Reply'
    ,'RESERVE' => 'Reserve'
    ,'REVIEW' => 'Review'
    ,'RSVP' => 'RSVP'
    ,'SAVE' => 'Save'
    ,'SAVE_OFFER' => 'Save offer'
    ,'SEE_DEMO' => 'See demo'
    ,'SELL' => 'Sell'
    ,'SEND' => 'Send'
    ,'SIGN_IN' => 'Sign in'
    ,'SIGN_UP' => 'Sign up'
    ,'START' => 'Start'
    ,'STOP' => 'Stop'
    ,'SUBSCRIBE' => 'Subscribe'
    ,'TAKE_QUIZ' => 'Take quiz'
    ,'TAKE_TEST' => 'Take test'
    ,'TRY_IT' => 'Try it'
    ,'UPVOTE' => 'Upvote'
    ,'USE' => 'Use'
    ,'VIEW' => 'View'
    ,'VIEW_ITEM' => 'View item'
    ,'VIEW_MENU' => 'View menu'
    ,'VIEW_PROFILE' => 'View profile'
    ,'VISIT' => 'Visit'
    ,'VOTE' => 'Vote'
    ,'WANT' => 'Want'
    ,'WANT_TO_SEE' => 'Want to see'
    ,'WANT_TO_SEE_IT' => 'Want to see it'
    ,'WATCH' => 'Watch'
    ,'WATCH_TRAILER' => 'Watch trailer'
    ,'WISH' => 'Wish'
    ,'WRITE' => 'Write');

    public function __construct()
    {
        $this->options = get_option('rhmd_settings');

        if ( !isset( $this->options['gplus_enable'] ) || !$this->options['gplus_enable'] )
        {
            return;
        }
        add_action('wp_footer', array( $this, 'addScriptToFooter' ));
        add_shortcode('gpi', array($this, 'gplus_interactive'));
    }

    /**
     * Generate the drop down box for selecting a CTA label
     *
     * @param string $name
     * @param array $options
     * @param string $default
     * @return string
     */
    public function generateSelect($name = '', $options = array(), $default = '')
    {
        $html = '<select id="gpi_cta" name="'.$name.'">';
        foreach ($options as $value => $option) {
            if ($value == $default) {
                $html .= '<option value='.$value.' selected="selected">'.$option.'</option>';
            } else {
                $html .= '<option value='.$value.'>'.$option.'</option>';
            }
        }

        $html .= '</select>';
        return $html;
    }

    /**
     * Generate the HTML needed for the share to Google+ Interactive button
     * @param $atts
     * @return string
     */
    public function gplus_interactive($atts)
    {
        global $post;

        $postId = abs($atts['id']);
        if (!isset($postId) || is_null($postId) || $postId == 0)
            $postId = $post->ID;

        $rhmd_options	= get_option('rhmd_settings');

        $permalink= get_post_meta($postId, 'gpi_content_url', true);
        if (!isset($permalink) || $permalink == '')
        {
            $permalink = get_permalink( $postId );
        }

        $gpiCTALabel = get_post_meta($postId, 'gpi_cta', true);
        if (!isset($gpiCTALabel) || $gpiCTALabel == '')
        {
            $gpiCTALabel = $rhmd_options['gpi_cta_global'];
        }

        $gpiCTAUrl	= get_post_meta($postId, 'gpi_cta_url', true);
        if (!isset($gpiCTAUrl) || $gpiCTAUrl == '')
        {
            $gpiCTAUrl = $rhmd_options['gpi_cta_url_global'];
        }

        $gpiPrefillText	= get_post_meta($postId, 'gpi_prefill', true);
        if (!isset($gpiPrefillText) || $gpiPrefillText == '')
        {
            $gpiPrefillText = $rhmd_options['gpi_prefill_global'];
        }

        $gpiButtonText	= get_post_meta($postId, 'gpi_btntxt', true);
        if (!isset($gpiButtonText) || $gpiButtonText == '')
        {
            $gpiButtonText = $rhmd_options['gpi_btntxt_global'];
        }
        $client_id = $rhmd_options['gplus_api_clientid_global'];

        return '<button class="g-interactivepost" style="padding:5px;background:#fff;cursor:pointer;line-height:20px;border:1px solid #e6e6e6;border-radius:4px;" data-contenturl="'.$permalink.'" data-clientid="'.$client_id.'.apps.googleusercontent.com" data-cookiepolicy="single_host_origin" data-prefilltext="'.$gpiPrefillText.'" data-calltoactionlabel="'.$gpiCTALabel.'" data-calltoactionurl="'.$gpiCTAUrl.'"><span style="width:20px;height:20px;display:inline-block;background:url(\''. RHMETADATA_URL .'assets/images/btn_icons_sprite.png\') transparent 0 -40px no-repeat;"></span>'.$gpiButtonText.'</button>';
    }

    public function addScriptToFooter()
    {
        ?>
        <script type="text/javascript">
            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/client:plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
        </script>
    <?php
    }
}

global $gplusInteractive;
$gplusInteractive = new RHMetaData_GPlusInteractive();

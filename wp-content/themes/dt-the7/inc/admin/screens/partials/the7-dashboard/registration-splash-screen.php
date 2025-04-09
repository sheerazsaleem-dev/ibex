<?php
/**
 * The7 dashboard thickbox splash screen.
 * @package The7\Admin
 */

defined( 'ABSPATH' ) || exit;

$import_demo_name = "blank";
?>
<script type="text/javascript">
    var the7SplashScreen = {
        confirmText: "<?php echo esc_html_x( 'This action will add (or overwrite) Global Colors and Fons in Elementor Design System. It will also overwrite Theme Style and some other Site Settings. Would you like to continue?', 'admin', 'the7mk2' ) ?>"
    };
</script>
<style>
    body.the7-modal-open ul#adminmenu a.wp-has-current-submenu:after,
    body.the7-modal-open ul#adminmenu > li.current > a.current:after {
        border-right-color: rgb(72,72,72) !important;
    }

    #registration-splash-screen-container.welcome-panel {
        margin: 0;
    }

    #registration-splash-screen-container .welcome-panel-close,
    #registration-splash-screen-container .welcome-panel-close:before {
        color: white;
    }

    #registration-splash-screen-container .welcome-panel-close:hover,
    #registration-splash-screen-container .welcome-panel-close:hover:before {
        color: rgb(103, 222, 235);
    }

    #registration-splash-screen-container .welcome-panel-header {
        padding: 48px 300px 48px 48px;
        background-size: cover;
    }

    #registration-splash-screen-container .welcome-panel-header h2,
    #registration-splash-screen-container .welcome-panel-header p {
        color: white;
    }

    #registration-splash-screen-container .welcome-panel-header-image {
        right: 100px;
        top: 30px;
        bottom: 30px;
        width: auto;
        height: auto;
    }

    #registration-splash-screen-container .welcome-panel-header-image svg {
        height: 100%;
        width: auto;
    }

    #registration-splash-screen-container [class*=welcome-panel-icon] {
        background-color: #e0eefb;
    }

    #registration-splash-screen-container [class*=welcome-panel-icon] svg {
        width: 40%;
        height: 40%;
        margin: 30%;
        color: #1690e1;
    }

    #registration-splash-screen-container .button-primary {
        background-color: #1690e1;
        border-color: #1690e1;
    }

    #registration-splash-screen-container .button-primary:hover {
        background-color: #137abe;
        border-color: #137abe;
    }

    #registration-splash-screen-container .button-secondary {
        color: #1690e1;
        border-color: #1690e1;
        background-color: transparent;
    }

    #registration-splash-screen-container .button-secondary:hover {
        color: #137abe;
        border-color: #137abe;
        background-color: #e0eefb;
    }

    @media screen and (max-width: 782px) {
        #registration-splash-screen-container .welcome-panel-header {
            padding: 48px;
        }

        #registration-splash-screen-container .welcome-panel-header-image svg {
            display: none;
        }
    }
</style>

<div id="registration-splash-screen-popup" style="display:none;">
    <div id="registration-splash-screen-container" class="welcome-panel">
        <a class="the7-popup-dismiss welcome-panel-close dismiss-notice" href="#"
           aria-label="Dismiss the welcome panel"><?php echo esc_html_x( 'Dismiss', 'admin', 'the7mk2' ); ?></a>
        <div class="welcome-panel-content">
            <div class="welcome-panel-header">
                <div class="welcome-panel-header-image">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="hero" x="0px" y="0px" viewBox="0 0 900 900" style="enable-background:new 0 0 900 900;" xml:space="preserve">
                        <style type="text/css">
                            .st0{fill:#FFFFFF;}
                            .st1{fill:url(#mround_00000057135941259242882040000012095884290655671725_);}
                        </style>
                            <g>
                                <g id="mlogo">
                                    <path class="st0" d="M350.4,114.3c-1.9-1.9-2.9-4.4-2.9-7.5V53h-16c-3.3,0-5.8-0.7-7.4-2.1c-1.6-1.4-2.4-3.6-2.4-6.5    s0.8-5,2.4-6.4s4.1-2.1,7.4-2.1h53c3.3,0,5.8,0.7,7.4,2.1c1.6,1.4,2.4,3.5,2.4,6.4s-0.8,5-2.4,6.5c-1.6,1.4-4.1,2.1-7.4,2.1h-16    v53.8c0,3.1-1,5.6-2.9,7.5s-4.5,2.8-7.6,2.8S352.3,116.1,350.4,114.3z"></path>
                                    <path class="st0" d="M479.1,38.2c1.8,1.9,2.8,4.4,2.8,7.5v61.1c0,3.1-0.9,5.6-2.8,7.5c-1.8,1.9-4.3,2.9-7.4,2.9s-5.6-0.9-7.4-2.8    s-2.8-4.4-2.8-7.6V84h-32.6v22.8c0,3.1-0.9,5.6-2.8,7.5c-1.8,1.9-4.3,2.9-7.4,2.9s-5.6-0.9-7.4-2.8s-2.8-4.4-2.8-7.6V45.7    c0-3.1,0.9-5.6,2.8-7.5c1.8-1.9,4.3-2.8,7.4-2.8s5.6,0.9,7.4,2.8c1.8,1.9,2.8,4.4,2.8,7.5v21.6h32.6V45.7c0-3.1,0.9-5.6,2.8-7.5    c1.8-1.9,4.3-2.8,7.4-2.8S477.3,36.3,479.1,38.2z"></path>
                                    <path class="st0" d="M508.4,114c-1.7-1.7-2.5-4.1-2.5-7.2V45.7c0-3.1,0.8-5.5,2.5-7.2s4-2.5,7.1-2.5h39.2c3.2,0,5.7,0.7,7.4,2.1    s2.5,3.4,2.5,5.9c0,5.5-3.3,8.2-9.8,8.2h-28.7v15.3h26.3c6.6,0,9.8,2.7,9.8,8.1c0,2.6-0.8,4.6-2.5,5.9c-1.7,1.4-4.1,2.1-7.4,2.1    h-26.2v16.7h28.5c6.6,0,9.8,2.7,9.8,8.2c0,2.6-0.8,4.6-2.5,5.9c-1.7,1.4-4.1,2.1-7.4,2.1h-39.2C512.4,116.5,510,115.6,508.4,114z"></path>
                                    <path class="st0" d="M602.9,278.4c3.7,3.8,5.5,8.5,5.5,14.5c0,5.5-1.8,11.6-5.3,18.2l-194,368.5c-4.7,8.6-11.5,13-20.4,13    c-6.2,0-11.8-2.2-16.6-6.4c-4.9-4.4-7.3-9.6-7.3-16c0-4.4,1.4-8.6,4.1-13l182.2-343.8H349.4c-7,0-12.4-1.8-16.1-5.4    s-5.5-8.4-5.5-14.8c0-6.6,1.9-11.8,5.5-15.4c3.7-3.6,9-5.4,16.1-5.4h237.3C593.9,272.8,599.2,274.7,602.9,278.4z"></path>

                                    <linearGradient id="mround_00000036972524991844822790000008516397401078148535_" gradientUnits="userSpaceOnUse" x1="95.2386" y1="6347.127" x2="804.9467" y2="5937.3774" gradientTransform="matrix(1 0 0 -1 0 6595.3501)">
                                        <stop offset="6.680000e-02" style="stop-color:#13E3EE"></stop>
                                        <stop offset="0.9107" style="stop-color:#029CF5"></stop>
                                    </linearGradient>
                                    <path id="mround" style="fill:url(#mround_00000036972524991844822790000008516397401078148535_);" d="M629.9,82.7L629.9,82.7    c-2.5-1.3-5.3-2.1-8.3-2.1c-9.4,0-17,7.7-17,17.2c0,5.8,2.9,11,7.3,14.1l-0.1,0.1c0.4,0.2,0.8,0.4,1.2,0.6    c0.8,0.5,1.6,0.9,2.4,1.2c124,61.6,209.3,190.4,209.3,339.2c0,208.8-167.8,378-374.7,378S75.3,661.8,75.3,453    c0-148.9,85.3-277.7,209.4-339.3c6.3-2.5,10.7-8.7,10.7-15.9c0-9.5-7.6-17.2-17-17.2c-2.4,0-4.8,0.5-6.9,1.5V82    C135.3,148.9,41.3,289.9,41.3,453c0,227.7,183,412.4,408.8,412.4S858.9,680.8,858.9,453C858.7,290.4,765.4,149.8,629.9,82.7z"></path>
                                </g>
                            </g>
                        </svg>
                </div>
                <h2><?php echo esc_html_x( 'Welcome to The7!', 'admin', 'the7mk2' ) ?></h2>
                <p>
					<?php echo esc_html_x( 'Your copy of theme is registered and ready to rock!', 'admin', 'the7mk2' );
					echo '<br>';
					echo esc_html_x( 'We are excited and honored to see a new member of the ever-growing The7 family.', 'admin', 'the7mk2' );
					?>
                </p>
            </div>
            <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
                    <div class="welcome-panel-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-columns" viewBox="0 0 16 16">
                            <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V2zm8.5 0v8H15V2H8.5zm0 9v3H15v-3H8.5zm-1-9H1v3h6.5V2zM1 14h6.5V6H1v8z"></path>
                        </svg>
                    </div>
                    <div class="welcome-panel-column-content">
                        <h3><?php echo esc_html_x( 'Build website from scratch', 'admin', 'the7mk2' ); ?></h3>
                        <p><?php echo esc_html_x( 'The7 will automatically install Elementor, set it up, and import basic templates for header, footer, page, etc.', 'admin', 'the7mk2' ); ?></p>
                        <form action="<?php echo admin_url( 'admin.php?page=the7-demo-content&amp;step=2' ); ?>"
                              method="post">
							<?php wp_nonce_field( 'import-wordpress' ); ?>
                            <input type="hidden" name="demo_id" value="<?php echo $import_demo_name; ?>"/>
                            <input type="hidden" name="import_theme_options" value="1"/>
                            <input type="hidden" name="import_post_types" value="1"/>
                            <input type="hidden" name="install_plugins" value="1"/>
                            <input type="hidden" name="import_attachments" value="1"/>
                            <input type="hidden" name="import_type" value="full_import"/>
                            <?php \Presscore_Modules_TGMPAModule::print_protected_plugins_installation_form_field() ?>

                            <button type="submit" class="button button-primary build-from-scratch"
                                    title="<?php echo esc_html_x( 'Build from scratch', 'admin', 'the7mk2' ); ?>"><?php echo esc_html_x( 'Build from scratch', 'admin', 'the7mk2' ); ?></button>
                        </form>
                    </div>
                </div>
                <div class="welcome-panel-column">
                    <div class="welcome-panel-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-box-arrow-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1h-2z"></path>
                            <path fill-rule="evenodd"
                                  d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z"></path>
                        </svg>
                    </div>
                    <div class="welcome-panel-column-content">
                        <h3><?php echo esc_html_x( 'Import pre-made website', 'admin', 'the7mk2' ); ?></h3>
                        <p><?php echo esc_html_x( 'This option will import a pre-made website (demo) of your choice. Required plugins will also be automatically installed.', 'admin', 'the7mk2' ); ?></p>
                        <a href="<?php echo admin_url( 'admin.php?page=the7-demo-content' ); ?>"
                           class="button button-primary dismiss-notice"><?php echo esc_html_x( 'Import pre-made website', 'admin', 'the7mk2' ); ?></a>
                    </div>
                </div>
                <div class="welcome-panel-column">
                    <div class="welcome-panel-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-arrow-bar-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M6 8a.5.5 0 0 0 .5.5h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L12.293 7.5H6.5A.5.5 0 0 0 6 8zm-2.5 7a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5z"></path>
                        </svg>
                    </div>
                    <div class="welcome-panel-column-content">
                        <h3><?php echo esc_html_x( 'Skip this screen.', 'admin', 'the7mk2' ); ?>
                            <br><?php echo esc_html_x( 'I\'m good', 'admin', 'the7mk2' ); ?></h3>
                        <p><?php echo esc_html_x( 'This option will skip the import of pre-made content, plugins, and settings. Choose if you are already familiar with The7.', 'admin', 'the7mk2' ); ?></p>
                        <button type="submit" class="button button-secondary the7-popup-dismiss dismiss-notice"
                                title="<?php echo esc_html_x( 'Skip', 'admin', 'the7mk2' ); ?>"><?php echo esc_html_x( 'Skip', 'admin', 'the7mk2' ); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

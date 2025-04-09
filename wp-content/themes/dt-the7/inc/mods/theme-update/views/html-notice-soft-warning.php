<?php
$domains_count = isset( $domains_count ) ? (int) $domains_count : 'N';
?>
<p>
    <strong>Hey!</strong> <br>
    We have noticed that you were using this purchase code on <?php echo $domains_count ?> domain names (excluding most common staging addresses, subdomains, subfolders, etc.). <br>
    This is a friendly reminder that according to <a href="<?php echo The7_Remote_API::LICENSE_URL ?>" target="_blank" rel="nofollow">Envat Standard Licenses</a>, you can't use one license for multiple projects, clients, or jobs. You must purchase a separate license for each website you build instead. Moreover, you cannot transfer a license from one website to another even if a previous website goes offline. <br>
    You can purchase more licenses <a href="<?php echo The7_Remote_API::THEME_THEMEFOREST_PAGE_URL ?>" target="_blank" rel="nofollow">here</a> and manage them at <a href="<?php echo The7_Remote_API::PURCHASE_CODES_MANAGE_URL ?>" target="_blank" rel="nofollow">my.the7.io</a>. <br>
    Thank you!
</p>

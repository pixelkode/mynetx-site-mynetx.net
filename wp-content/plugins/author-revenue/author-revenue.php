<?php

/*
* Plugin Name: Author revenue
* Plugin URI: http://mynetx.net/
* Description: Displays the author revenue.
* Version: 1.1
* Author: mynetx Creations
* Author URI: http://mynetx.net/
*/

define('ADSENSE_CURRENCY', 'GBP');
define('AUTHOR_REVENUE_PERCENT',  40   /100);

function author_revenue_setcookies()
{
	$arrOptions = author_revenue_options();
	if (isset($_POST['author-revenue-options'])) {
		$arrOptions['currency'] = $_POST['author-revenue-currency'];
	}
	if (isset($_POST['author-revenue-options'])) {
		$strOptions = serialize($arrOptions);
		setcookie('author-revenue-options', $strOptions, 2147483647);
		if (get_magic_quotes_gpc()) {
			//$strOptions = addslashes($strOptions);
		}
		$_COOKIE['author-revenue-options'] = $strOptions;
	}
}

function author_revenue_options()
{
	$arrOptions = array('currency' => 'GBP');
	if (isset($_COOKIE['author-revenue-options'])) {
		$_COOKIE['author-revenue-options'] = stripslashes($_COOKIE['author-revenue-options']);
		$arrOptions = unserialize($_COOKIE['author-revenue-options']);
	}
	return $arrOptions;
}

function author_revenue_dashboard_config()
{
	$arrOptions = author_revenue_options();
	$arrCurrencies = author_revenue_currencies();

?>
	<form action="./" method="post">
	<table style="margin-bottom:0; width:100%" summary="<?php

	_e('Author revenue', 'author-revenue');

?>" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<th class="textleft" scope="row">
					<span class="auraltext"><?php

	_e('Your currency', 'author-revenue');

?></span></th>
				<td class="textright">
					<select name="author-revenue-currency" size="1">
						<?php

	foreach ($arrCurrencies as $strCode => $arrData) {

?>
							<option value="<?php

		echo $strCode;

?>"
								<?php

		if ($arrOptions['currency'] == $strCode) echo ' selected="selected"';

?>
							><?php

		echo $strCode . ' (' . $arrData[0] . ')';

?></option>
						<?php

	}

?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="author-revenue-options" value="1" />
	<input class="button-primary" type="submit" value="Submit" />
	<a href="./">Cancel</a>
	</form>
	<?php

}

function author_revenue_dashboard()
{
	global $wpdb, $user_ID, $user_login;
	if(isset($_GET['edit']) && $_GET['edit'] == 'author_revenue_dashboard') {
		author_revenue_dashboard_config();
		return;
	}
	else {
		?>
		<div style="text-align: right; position: relative; top: -2.35em; right: 2.5em; margin-bottom: -1em">
		<a href="./?edit=author_revenue_dashboard#author_revenue_dashboard"><?php _e('Configure'); ?></a>
		</div>
		<?php
	}
	if ($user_ID == 2 || $user_ID == 90) {
		author_revenue_enterrevenue();
		return;
	}
	$arrOptions = author_revenue_options();
	$arrRevenue = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}author_revenue WHERE author = '" .
		mysql_real_escape_string($user_login) . "'");
	$strDisplayName = $wpdb->get_var("SELECT display_name FROM {$wpdb->prefix}users WHERE user_login = '" .
		mysql_real_escape_string($user_login) . "'");

	if (!$arrRevenue) {
		$arrRevenue = new stdClass;
		$arrRevenue->archive = $arrRevenue->lastmonth = $arrRevenue->thismonth = $arrRevenue->
			allpayments = $arrRevenue->lastpayment = 0;
		$arrRevenue->lastpaymentdate = '0000-00-00';
		$arrRevenue->lastupdate = date('Y-m-d');
		$arrRevenue->lang = false;
	}
	$total = $arrRevenue->lastmonth + $arrRevenue->thismonth + $arrRevenue->
		archive - $arrRevenue->allpayments;
	echo "<!--$total-->\r\n";

?>
	<table style="width:100%" summary="<?php

	_e('Author revenue', 'author-revenue');

?>" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<th class="textleft" scope="row" style="border-bottom: 1px dotted #999">
					<span class="auraltext"><?php

	_e('This month', 'author-revenue');

?></span></th>
				<td class="textright"><strong><?php

	echo author_revenue_currency($arrRevenue->thismonth);

?></strong></td>
			</tr>
			<tr>
				<th class="textleft" scope="row" style="border-bottom: 1px dotted #999; padding-top: 10px">
					<span class="auraltext"><?php

	_e('Last month', 'author-revenue');

?></span></th>
				<td class="textright" style="padding-top: 10px">
					<?php

	echo author_revenue_currency($arrRevenue->lastmonth);

?></td>
			</tr>
			<tr>
				<th class="textleft" scope="row" style="border-bottom: 1px dotted #999">
					<span class="auraltext"><?php

	_e('Earlier earnings', 'author-revenue');

?></span></th>
				<td class="textright"><?php

	echo author_revenue_currency($arrRevenue->archive);

?></td>
			</tr>
			<tr>
				<th class="textleft" scope="row" style="border-bottom: 1px dotted #999">
					<span class="auraltext"><?php

	_e('Possible payment (if &gt; ' . author_revenue_currency(8.34) . ')',
		'author-revenue');

?></span></th>
				<td class="textright"><?php

	echo author_revenue_currency($total);

?></td>
			</tr>
			<tr>
				<th class="textleft" scope="row" style="border-bottom: 1px dotted #999; padding-top: 10px">
					<span class="auraltext"><?php

	_e('Last payment', 'author-revenue');

?></span></th>
				<td class="textright" style="padding-top: 10px"><?php

	if ($arrRevenue->lastpayment > 0) {
		$strDate = $arrRevenue->lastpaymentdate;
		printf(__('%s on %s', 'author-revenue'), author_revenue_currency($arrRevenue->
			lastpayment), date(__('Y/m/d'), mktime(0, 0, 0, substr($strDate, 5, 2),
			substr($strDate, 8, 2), substr($strDate, 0, 4))));
	}
	else {
		_e('never', 'author-revenue');
	}

?></td>
			</tr>
			<tr>
				<th class="textleft" scope="row" style="border-bottom: 1px dotted #999">
					<span class="auraltext"><?php

	_e('All payments', 'author-revenue');

?></span></th>
				<td class="textright"><?php

	echo author_revenue_currency($arrRevenue->allpayments);

?></td>
			</tr>
			<tr>
				<th class="textleft" scope="row" style="border-bottom: 1px dotted #999">
					<span class="auraltext"><?php

	_e('Revenue data last updated', 'author-revenue');

?></span></th>
				<td class="textright"><?php

	if ($arrRevenue->lastupdate != '0000-00-00') {
		$strDate = $arrRevenue->lastupdate;
		echo date(__('Y/m/d'), mktime(0, 0, 0, substr($strDate, 5, 2), substr($strDate,
			8, 2), substr($strDate, 0, 4)));
	}
	else {
		_e('never', 'author-revenue');
	}

?></td>
			</tr>
		</tbody>
	</table>
	<p><?php

	if ($arrRevenue->lang) {
		$strLanguage = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}icl_languages_translations WHERE language_code='{$arrRevenue->lang}' AND display_language_code='en'");
		echo ' ';
		printf(__('You are responsible for posts in %s. ', 'author-revenue'), $strLanguage);
	}
	printf(__('A payout is pending as soon as your balance reaches %s. ',
		'author-revenue'), author_revenue_currency(8.34));
	_e('Pending payouts are paid on the first day of the next month. ',
		'author-revenue');
	_e('Click <em>Configure</em> to change the displayed currency. ',
		'author-revenue');
	echo '</p>';
}

function author_revenue_currency($fltAmount)
{
	$arrOptions = author_revenue_options();
	$strCurrency = $arrOptions['currency'];
	$arrCurrencies = author_revenue_currencies();
	$strTemplate = $arrCurrencies[$strCurrency][1];
	$fltAmount = author_revenue_currencyconvert($fltAmount, $strCurrency);
	$strAmount = number_format($fltAmount, 2, '.', ',');
	return sprintf($strTemplate, $strAmount);
}

function author_revenue_currencyconvert($fltAmount, $strCurrency, $strCurrencyFrom =
	'GBP')
{
	if ($strCurrency == $strCurrencyFrom) return $fltAmount;
	static $arrValueCache = array();
	if (!isset($arrValueCache[$strCurrency . $strCurrencyFrom])) {
		if ($fltAmount > 0) {
			$ch = curl_init('http://www.google.com/ig/calculator?hl=en&q=100' . $strCurrencyFrom .
				'%3D%3F' . $strCurrency);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$strJson = curl_exec($ch);
			$strJson = substr($strJson, strpos($strJson, 'rhs: ') + 6);
			$arrValueCache[$strCurrency . $strCurrencyFrom] = floatval(floatval($strJson) /
				100);
		}
	}
	return $fltAmount * $arrValueCache[$strCurrency . $strCurrencyFrom];
}

function author_revenue_currencies()
{
	$arrCurrencies = array('BRL' => array('Real', 'R$%s'), 'EUR' => array('Euro',
		'&euro;%s EUR'), 'GBP' => array('Pound Sterling', '&pound;%s GBP'), 'NOK' =>
		array('Norwegian Krone', '%s NOK'), 'USD' => array('US Dollar', '$%s USD'));
	return $arrCurrencies;
}

function author_revenue_add_dashboard()
{
	wp_add_dashboard_widget('author_revenue_dashboard', __('Author revenue'),
		'author_revenue_dashboard');
}

function author_revenue_enterrevenue()
{
	global $wpdb, $user_ID;
	if ($user_ID != 2 && $user_ID != 90) return;
	if (isset($_POST['adsensevalue'])) {
		author_revenue_adsense_query();
	}
	if (isset($_POST['this-month-start'])) {
		update_option('this_month_start', intval($_POST['this-month-start']));
		update_option('last_month_start', intval($_POST['last-month-start']));
	}
	$arrAuthors = $wpdb->get_results("
		SELECT u.display_name as name, u.user_login as login, u.ID as ID
		FROM {$wpdb->prefix}users u, {$wpdb->prefix}usermeta um
		WHERE u.ID = um.user_id
			AND um.meta_key = '{$wpdb->prefix}user_level'
			AND u.ID != 2
		ORDER BY name ASC");
	$intNow = time();
	$intThisMonthStart = mktime(0, 0, 0, date('m'), 1, date('Y'));
	$intLastMonthMiddle = $intThisMonthStart - 14 * 86400;
	$intLastMonthStart = mktime(0, 0, 0, date('m', $intLastMonthMiddle), 1, date('Y',
		$intLastMonthMiddle));

	// this month
	$intPostsTotal = 0;
	for ($i = 0; $i < count($arrAuthors); $i++) {
		$intPosts = $wpdb->get_var("
			SELECT COUNT(*) FROM {$wpdb->posts}
			WHERE post_status = 'publish'
			AND post_date_gmt < '" . gmdate("Y-m-d H:i:s", $intNow) . "'
			AND ID >= '" . get_option('this_month_start') . "'
			AND post_author = '" . $arrAuthors[$i]->ID . "'");
		$arrAuthors[$i]->posts = $intPosts;
		$intPostsTotal += $arrAuthors[$i]->posts;
	}
	usort($arrAuthors, 'author_revenue_frontendsidebar_sort');

	$fltAdsense = get_option('author_revenue_fltadsense');
	$fltTotal = 0;

?>
	<h3><?php

	echo date('F Y', $intNow);

?></h3><br />
	<form action="" method="post">
	<table style="width:100%" summary="<?php

	_e('Author revenue', 'author-revenue');

?>" cellpadding="0" cellspacing="0">
	<tbody>
	<?php

	foreach ($arrAuthors as $objAuthor) {
		if ($objAuthor->posts == 0) {
			continue;
		}
		$fltPercent = 100 * $objAuthor->posts / $intPostsTotal;

?>
		<tr>
			<th class="textleft" scope="row" style="border-bottom: 1px dotted #999">
				<span class="auraltext"><?php

		echo $objAuthor->name;

?></span></th>
			<td class="textright"><?php

		printf(__('%d posts', 'author-revenue'), $objAuthor->posts);

?></td>
		<?php

		$fltRevenue = $wpdb->get_var("
				SELECT thismonth FROM {$wpdb->prefix}author_revenue
				WHERE author = '{$objAuthor->login}'");
		if (!$fltRevenue) {
			$fltRevenue = 0;
		}
		$fltTotal += $fltRevenue;

?>
			<td class="textright"><?php

		echo author_revenue_currency($fltRevenue);

?></td>
		</tr>
	<?php

	}

?>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<th class="textleft" colspan="2" scope="row" style="border-bottom: 1px dotted #999">
				<span class="auraltext">AdSense revenue</span></th>
			<td class="textright"><?php

	echo author_revenue_currency($fltAdsense);

?></td>
		</tr>
		<tr>
			<th class="textleft" colspan="2" scope="row" style="border-bottom: 1px dotted #999">
				<span class="auraltext">Left for us</span></th>
			<td class="textright"><?php

	echo author_revenue_currency($fltAdsense - $fltTotal);

?></td>
		</tr>
		<tr>
			<th class="textleft" scope="row">
				<span class="auraltext"><a href="https://www.google.com/adsense/v3/app#viewreports/ag=product&d=thismonth">Revenue this month:</a></span></th>
			<td class="textright"><input type="text" size="5" name="adsensevalue" />
				<?php

	echo ADSENSE_CURRENCY;

?></td>
			<td class="textright"><input type="submit" value="Update" class="button" /></td>
		</tr>
	</tbody>
	</table>
	</form>
	<?php

	// last month
	$intPostsTotal = 0;
	for ($i = 0; $i < count($arrAuthors); $i++) {
		$intPosts = $wpdb->get_var("
			SELECT COUNT(*) FROM {$wpdb->posts}
			WHERE post_status = 'publish'
			AND post_date_gmt < '" . gmdate("Y-m-d H:i:s", $intThisMonthStart) . "'
			AND ID >= '" . get_option('last_month_start') . "'
			AND post_author = '" . $arrAuthors[$i]->ID . "'");
		$arrAuthors[$i]->posts = $intPosts;
		$intPostsTotal += $arrAuthors[$i]->posts;
	}
	usort($arrAuthors, 'author_revenue_frontendsidebar_sort');

?><p></p>
	<h3><?php

	echo date('F Y', $intLastMonthStart);

?></h3><br />
	<form action="" method="post">
	<table style="width:100%" summary="<?php

	_e('Author revenue', 'author-revenue');

?>" cellpadding="0" cellspacing="0">
	<tbody>
	<?php

	foreach ($arrAuthors as $objAuthor) {

		$fltPercent = 100 * $objAuthor->posts / $intPostsTotal;
		if (isset($_POST['revenue-last']) && $_POST['revenue-last'] > 0) {
			$fltRevenueLast = $_POST['revenue-last'];
			$fltRevenue = round($fltRevenueLast * AUTHOR_REVENUE_PERCENT * $fltPercent / 100, 2);
			$intLastUpdate = strtotime($wpdb->get_var("
				SELECT lastupdate FROM {$wpdb->prefix}author_revenue
				WHERE author = '{$objAuthor->login}'
			"));
			$wpdb->query("
				UPDATE {$wpdb->prefix}author_revenue
				SET archive = archive + lastmonth
				WHERE author = '{$objAuthor->login}'");
			$wpdb->query("
				UPDATE {$wpdb->prefix}author_revenue
				SET lastmonth = '$fltRevenue', thismonth = '0.00', lastupdate = CURDATE()
				WHERE author = '{$objAuthor->login}'");
		}
		else {
			$fltRevenue = $wpdb->get_var("
				SELECT lastmonth FROM {$wpdb->prefix}author_revenue
				WHERE author = '{$objAuthor->login}'");
			if (!$fltRevenue) {
				$fltRevenue = 0;
			}
		}
		if ($objAuthor->posts == 0) {
			continue;
		}

?>
		<tr>
			<th class="textleft" scope="row" style="border-bottom: 1px dotted #999">
				<span class="auraltext"><?php

		echo $objAuthor->name;

?></span></th>
			<td class="textright"><?php

		printf(__('%d posts', 'author-revenue'), $objAuthor->posts);

?></td>
			<td class="textright"><?php

		echo author_revenue_currency($fltRevenue);

?></td>
		</tr>
	<?php

	}

?>
		<tr>
			<th class="textleft" scope="row">
				<span class="auraltext">Revenue last month:</span></th>
			<td class="textright"><input type="text" name="revenue-last" size="6" />
				<?php

	echo ADSENSE_CURRENCY;

?></td>
			<td class="textright"><input type="submit" value="Update" class="button" /></td>
		</tr>
	</tbody>
	</table>
		<p>Newest ID: <?php

	echo $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}posts ORDER BY ID DESC LIMIT 1") +
		1;

?>
			<br />
			This month: <input type="text" name="this-month-start" size="4" value="<?php

	echo get_option('this_month_start');

?>" />
			Last month: <input type="text" name="last-month-start" size="4" value="<?php

	echo get_option('last_month_start');

?>" />

		</p>
	</form>
	<?php

	if (isset($_POST['pay-author'])) author_revenue_payauthors();

	$arrPendingPayments = $wpdb->get_results("
		SELECT u.display_name as name, u.user_login as login,
			archive + lastmonth - allpayments as balance
		FROM {$wpdb->prefix}users u, {$wpdb->prefix}author_revenue ar
		WHERE u.user_login = ar.author
			AND u.ID != 2
		ORDER BY balance DESC");
	if (count($arrPendingPayments)) {

?>
		<form action="./" method="post">
		<h3><?php

		_e('Balance pending payout', 'author-revenue');

?></h3><br />
		<table style="width:100%" summary="<?php

		_e('Author revenue', 'author-revenue');

?>" cellpadding="0" cellspacing="0">
		<tbody>
		<?php

		foreach ($arrPendingPayments as $objAuthor) {

?>
			<tr style="<?php

			echo $objAuthor->balance < 8.34 ? 'color: #999' : '';

?>">
				<th class="textleft" scope="row" colspan="2" style="border-bottom: 1px dotted #999">
					<span class="auraltext"><?php

			echo $objAuthor->name;

?></span></th>
				<td class="textright"><?php

			echo author_revenue_currency($objAuthor->balance);

?></td>
				<td class="textright">
					<?php

			if ($objAuthor->balance >= 8.34) {

?>
					<input type="checkbox" name="pay-author[]" value="<?php

				echo $objAuthor->login;

?>" />
					<?php

			}

?>
				</td>
			</tr>
			<?php

		}

?>
		</tbody>
		</table>
		<p>
			<input type="submit" value="Pay selected" class="button-primary" />
		</p>
		</form>
		<?php

	}
}

function author_revenue_payauthors()
{
	global $wpdb;
	$arrPayees = $_POST['pay-author'];
	$arrPendingPayments = $wpdb->get_results("
		SELECT u.display_name as name, u.user_login as login,
			paysystem, payee, u.user_email as mail,
			archive + lastmonth - allpayments as balance
		FROM {$wpdb->prefix}users u, {$wpdb->prefix}author_revenue ar
		WHERE u.user_login = ar.author
			AND u.ID != 2
		ORDER BY balance DESC");
	foreach ($arrPendingPayments as $objPayee) {
		if (!in_array($objPayee->login, $arrPayees)) continue;
		if ($objPayee->paysystem == 'paypal' && $objPayee->payee != '')
				author_revenue_sendmoney_add($objPayee->payee, $objPayee->balance);
		else  $wpdb->query("
				UPDATE {$wpdb->prefix}author_revenue
				SET allpayments = allpayments + {$objPayee->balance},
				lastpayment = {$objPayee->balance},
				lastpaymentdate = CURDATE()
				WHERE author = '{$objPayee->login}'
			");
	}
	if (author_revenue_sendmoney_send()) {
		foreach ($arrPendingPayments as $objPayee) {
			if (!in_array($objPayee->login, $arrPayees)) continue;
			if ($objPayee->paysystem != 'paypal' || $objPayee->payee == '') continue;
			$wpdb->query("
				UPDATE {$wpdb->prefix}author_revenue
				SET allpayments = allpayments + {$objPayee->balance},
				lastpayment = {$objPayee->balance},
				lastpaymentdate = CURDATE()
				WHERE author = '{$objPayee->login}'
			");
		}
	}
}

$author_revenue_arrPayees = array();
function author_revenue_sendmoney_add($strPayee, $fltBalance)
{
	global $author_revenue_arrPayees;
	$author_revenue_arrPayees[] = array($strPayee, $fltBalance);
}
function author_revenue_sendmoney_send()
{
	global $author_revenue_arrPayees;
	if (count($author_revenue_arrPayees) == 0) return;
	$strQuery = 'USER=me_api1.joshuaatkins.co.uk&' . 'PWD=BTWSA8BTHVZAN2HF&' .
		'SIGNATURE=AFcWxV21C7fd0v3bYYYRCpSSRl31AI219JDXt34gwjLoTumdJK8HSz.j&' .
		'VERSION=2.3&METHOD=MassPay&EMAILSUBJECT=mynetx%20Author%20Revenue&' .
		'CURRENCYCODE=GBP&RECEIVERTYPE=EmailAddress&';
	for ($i = 0; $i < count($author_revenue_arrPayees); $i++) {
		$strMail = $author_revenue_arrPayees[$i][0];
		$fltBalance = $author_revenue_arrPayees[$i][1];
		$strQuery .= "L_EMAIL$i=$strMail&L_AMT$i=$fltBalance&";
	}
	$ch = curl_init('https://api-3t.paypal.com/nvp');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $strQuery);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$strReply = curl_exec($ch);
	if (stristr($strReply, 'Insufficient%20funds')) echo
			'<p style="color: red">Insufficient funds.</p>';
	if (stristr($strReply, 'User%20is%20blocked')) echo
			'<p style="color: red">Mass payments blocked.</p>';
	$author_revenue_arrPayees = array();
	return !!stristr($strReply, 'ACK=Success');
}

function author_revenue_frontendsidebar_sort($objAuthor1, $objAuthor2)
{
	if ($objAuthor1->posts < $objAuthor2->posts) return 1;
	else
		if ($objAuthor1->posts > $objAuthor2->posts) return - 1;
		else  return 0;
}

function author_revenue_adsense_query()
{
	global $wpdb;
	if (!isset($_POST['adsensevalue'])) {
		$fltAdsense = get_option('author_revenue_fltadsense');
	}
	else {
		$fltAdsense = $_POST['adsensevalue'];
		update_option('author_revenue_fltadsense', $fltAdsense);
	}

	$arrAuthors = $wpdb->get_results("
		SELECT u.display_name as name, u.user_login as login, u.ID as ID
		FROM {$wpdb->prefix}users u, {$wpdb->prefix}usermeta um
		WHERE u.ID = um.user_id
			AND um.meta_key = '{$wpdb->prefix}user_level'
			AND u.ID != 2
		ORDER BY name ASC");
	$intNow = time();

	// this month
	$intPostsTotal = 0;
	for ($i = 0; $i < count($arrAuthors); $i++) {
		$intPosts = $wpdb->get_var("
			SELECT COUNT(*) FROM {$wpdb->posts}
			WHERE post_status = 'publish'
			AND post_date_gmt < '" . gmdate("Y-m-d H:i:s", $intNow) . "'
			AND ID >= '" . get_option('this_month_start') . "'
			AND post_author = '" . $arrAuthors[$i]->ID . "'");
		$arrAuthors[$i]->posts = $intPosts;
		$intPostsTotal += $arrAuthors[$i]->posts;
	}
	usort($arrAuthors, 'author_revenue_frontendsidebar_sort');
	$strText = 'Total estimated revenue: EUR ' . $fltAdsense . '

Authors:
';
	$fltTotal = 0;
	if ($intPostsTotal > 0) {
		foreach ($arrAuthors as $objAuthor) {
			$fltPercent = 100 * $objAuthor->posts / $intPostsTotal;
			$fltRevenue = round($fltAdsense * AUTHOR_REVENUE_PERCENT * $fltPercent / 100, 2);
			$arrLastMonth = $wpdb->get_row("
				SELECT lastupdate, thismonth FROM {$wpdb->prefix}author_revenue
				WHERE author = '{$objAuthor->login}'
			");
			$intLastUpdateMonth = substr($arrLastMonth->lastupdate, 5, 2);
			if ($intLastUpdateMonth != date('m')) {
				continue;
			}
			$wpdb->query("
				UPDATE {$wpdb->prefix}author_revenue
				SET thismonth = '$fltRevenue', lastupdate = CURDATE()
				WHERE author = '{$objAuthor->login}'");
			$fltTotal += $fltRevenue;
			$strText .= sprintf("%-20.20s   %-15.15s %3s posts (%3d%%) %6s EUR\r\n",
				htmlentities(utf8_decode($objAuthor->name)), $objAuthor->login, $objAuthor->
				posts, intval($fltPercent), $fltRevenue);
		}
	}
	$fltTotal = number_format($fltTotal, 2, '.', '');
	$strText .= "------------------------------------------------------------------\r\n" .
		'Summe                                                   ' . sprintf('%6s', $fltTotal) .
		' EUR';
	$strText = '<pre style="font-size: .8em">' . $strText . '</pre>';
	/*
	* mail(get_option('admin_email'),
	* 'Author revenue report',
	* $strText,
	* 'From: wordpress@'.$_SERVER['HTTP_HOST']."\r\nContent-Type: text/html; charset=UTF-8");
	*/
}
if (isset($_GET['abcd'])) author_revenue_adsense_query();

register_activation_hook(__FILE__, 'author_revenue_adsense_query_activation');
register_deactivation_hook(__FILE__, 'author_revenue_adsense_query_deactivation');
add_action('author_revenue_adsense_query_hook', 'author_revenue_adsense_query');

function author_revenue_adsense_query_activation()
{
	wp_schedule_event(time(), 'hourly', 'author_revenue_adsense_query_hook');
}
function author_revenue_adsense_query_deactivation()
{
	wp_clear_scheduled_hook('author_revenue_adsense_query_hook');
}
add_option('author_revenue_fltadsense', 0);

add_action('admin_init', 'author_revenue_setcookies');
add_action('wp_dashboard_setup', 'author_revenue_add_dashboard');

?>

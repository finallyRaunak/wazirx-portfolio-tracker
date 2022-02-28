<?php
//for profit
if (!empty($invests) && ($total_profit >= 0)) {
    $wdgetIcon = 'fa-arrow-alt-circle-up text-success-light';
    $wdgetTxt = 'Profit %';
    $wdgetClass = 'bg-success';
}
//for loss
else {
    $wdgetIcon = 'fa-arrow-alt-circle-down text-success-light';
    $wdgetTxt = 'Loss %';
    $wdgetClass = 'bg-danger';
}
?>
<!doctype html>
<html lang="en">
    <?php include_once 'partials/header.php'; ?>
    <body>

        <div id="page-container">

            <!-- Main Container -->
            <main id="main-container">

                <!-- Hero -->
                <div class="bg-body-light">
                    <div class="content content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                            <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">WazirX Portfolio Tracker</h1>
                            <ul class="nav-main nav-main-horizontal">
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="https://github.com/finallyRaunak/wazirx-portfolio-tracker">
                                        <i class="nav-main-link-icon fab fa-2x fa-github"></i>
                                        <span class="nav-main-link-name">Github</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="https://github.com/finallyRaunak/wazirx-portfolio-tracker/issues/new?labels=bug">
                                        <i class="nav-main-link-icon fa fa-2x fa-bug"></i>
                                        <span class="nav-main-link-name">Report a Bug</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="https://github.com/finallyRaunak/wazirx-portfolio-tracker/discussions/new?category=ideas">
                                        <i class="nav-main-link-icon far fa-2x fa-lightbulb"></i>
                                        <span class="nav-main-link-name">Suggest a New Feature</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="javascript:void(0)">
                                        <i class="nav-main-link-icon fab fa-2x fa-bitcoin"></i>
                                        <span class="nav-main-link-name">Donate</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- END Hero -->

                <!-- Page Content -->
                <div class="content">

                    <?php
                    if (!empty($alert_message)) {
                        include_once 'partials/alert.php';
                    }
                    ?>

                    <!-- Layouts -->
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">API Credentials</h3>
                        </div>
                        <div class="block-content">
                            <!-- Inline Layout -->
                            <div class="row push">
                                <div class="col-lg-12">
                                    <p>To use this tool you have to get the WazirX API credentials from <a href="https://wazirx.com/blog/create-wazirx-api-key/" target="_blank" rel="nofollow" title="How to create WazirX API Key?">here</a>. after getting that copy-paste it over here <em>also store it at your end for future use</em>.</p>
                                    <p>Select the currency/trading pair that you generally use for buying crypto then fill the API Key and API Secret and hit the "Fetch Orders" button. That's it within a few seconds it will pull all the orders and will do a heavy calculation in the backend and will display the report below.</p>
                                </div>
                                <div class="col-lg-12 space-y-2">
                                    <!-- Form Inline - Default Style -->
                                    <form class="row row-cols-lg-auto g-3 align-items-center" action="<?= getSiteURL('index.php') ?>" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                                        <div class="col-lg-3 col-xl-3">
                                            <select class="js-select2 form-select" id="example-select2-multiple" name="trading_pair[]" style="width: 100%;" data-placeholder="Choose trading pair..." multiple="multiple">
                                                <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                                <option value="inr" selected>INR</option>
                                                <option value="wrx">WRX</option>
                                                <option value="usdt">USDT</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-xl-3">
                                            <input type="text" class="form-control" name="api_key" placeholder="API Key" required value="<?= !empty($_SESSION['api_key']) ? $_SESSION['api_key'] : ''; ?>"/>
                                        </div>
                                        <div class="col-lg-3 col-xl-3">
                                            <input type="password" class="form-control" name="api_secret" placeholder="API Secret" required value="<?= !empty($_SESSION['api_secret']) ? $_SESSION['api_secret'] : ''; ?>"/>
                                        </div>
                                        <div class="col-lg-3 col-xl-3">
                                            <button type="submit" class="btn btn-hero btn-primary" data-toggle="click-ripple" name="consent" value="yes">
                                                Fetch Orders <i class="fa fa-fw fa-rocket me-1"></i>
                                            </button>
                                        </div>
                                    </form>
                                    <!-- END Form Inline - Default Style -->
                                </div>
                            </div>
                            <!-- END Inline Layout -->

                        </div>
                    </div>
                    <!-- END Layouts -->

                    <?php
                    if (!empty($invests)) {
                        include_once 'partials/statistics.php';
                        include_once 'partials/table.php';
                    }
                    ?>

                    <div class="block block-rounded">
                        <div class="block-content">
                            <h4>Please Note:</h4>
                            <ul class="fa-ul">
                                <li>
                                    <span class="fa-li">
                                        <i class="fa fa-arrow-right"></i>
                                    </span>
                                    We <mark><strong>do not</strong></mark> keep or store your API information.
                                </li>
                                <li>
                                    <span class="fa-li">
                                        <i class="fa fa-arrow-right"></i>
                                    </span>
                                    When you are creating an API key, <mark><strong>only</strong></mark> give the <mark><strong>Read-Only</strong></mark> permissions
                                </li>
                                <li>
                                    <span class="fa-li">
                                        <i class="fa fa-arrow-right"></i>
                                    </span>
                                    To provide a better user experience we do the following things, also by doing this compile with the WazirX API limiting policies
                                    <ul class="fa-ul">
                                        <li>
                                            <span class="fa-li">
                                                <i class="fa fa-angle-right"></i>
                                            </span>
                                            We cache or temporarily store your order information in an encoded format for <?= MY_ORDER_TTL; ?>min.
                                        </li>
                                        <li>
                                            <span class="fa-li">
                                                <i class="fa fa-angle-right"></i>
                                            </span>
                                            Your wallet information is cached for <?= MY_WALLET_TTL; ?>min.
                                        </li>
                                        <li>
                                            <span class="fa-li">
                                                <i class="fa fa-angle-right"></i>
                                            </span>
                                            And we refresh market at every <?= TICKERS_TTL; ?>min.
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <span class="fa-li">
                                        <i class="fa fa-arrow-right"></i>
                                    </span>
                                    This tool only considers the trade/order that happen or executed in the WazirX platform. if you had transferred it from Binance and or from any other platform then this tool will not be able to give you the correct result for that specific coin as there is no API to pull those details.
                                </li>
                                <li>
                                    <span class="fa-li">
                                        <i class="fa fa-arrow-right"></i>
                                    </span>
                                    Also if you bought a coin in WazirX and transferred it to some other exchange of wallet then those coins of quantity will not be taken into account as that coin is not in your WazirX wallet.
                                </li>
                                <li>
                                    <span class="fa-li">
                                        <i class="fa fa-arrow-right"></i>
                                    </span>
                                    This tool is still in the beta phase. If you found any discrepancy or error or an issue then please let me via <a href="https://github.com/finallyRaunak/wazirx-portfolio-tracker/issues/new?labels=bug" target="_blank" rel="nofollow" title="Reort an issue.">this form</a> or you can DM me on <a href="https://twitter.com/__raunakGupta" target="_blank" rel="nofollow" title="Follow me on Twitter">Twitter</a>.
                                </li>
                            </ul>
                            <p class="text-muted">Disclaimer: This is an <strong>unofficial</strong> WazirX Portfolio Tracker which enhances the wallet user experience. The brand and copyright of the word "WazirX" belong to WazirX (https://wazirx.com).<p>
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->

        <?php include_once 'partials/footer.php'; ?>
        </div>
        <!-- END Page Container -->

        <?php include_once 'partials/script.php'; ?>
    </body>
</html>
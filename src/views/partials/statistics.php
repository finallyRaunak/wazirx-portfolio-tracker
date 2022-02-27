<div class="content">

    <!-- Overview -->
    <div class="row items-push">
        <div class="col-md-6 col-xl-3">
            <a class="block block-rounded block-link-shadow bg-primary" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fab fa-2x fa-bitcoin text-primary-lighter"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="text-white fs-3 fw-medium mb-0">
                            <?= $total_crypto; ?>
                        </p>
                        <p class="text-white-75 mb-0">
                            Total Crypto
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a class="block block-rounded block-link-shadow <?= $wdgetClass; ?>" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div>
                        <i class="far fa-2x <?= $wdgetIcon; ?>"></i>
                    </div>
                    <div class="ms-3 text-end">
                        <p class="text-white fs-3 fw-medium mb-0">
                            <?= $avg_percentage; ?>%
                        </p>
                        <p class="text-white-75 mb-0">
                            <?= $wdgetTxt; ?>
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a class="block block-rounded block-link-shadow <?= $wdgetClass; ?>" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div class="me-3">
                        <p class="text-white fs-3 fw-medium mb-0">
                            <?= $total_profit; ?>
                        </p>
                        <p class="text-white-75 mb-0">
                            Earnings
                        </p>
                    </div>
                    <div>
                        <i class="far fa-2x fa-money-bill-alt text-success-light"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a class="block block-rounded block-link-shadow bg-warning" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                    <div class="me-3">
                        <p class="text-white fs-3 fw-medium mb-0">
                            &#8377; <?= $wallet_balance; ?>
                        </p>
                        <p class="text-white-75 mb-0">
                            Funds
                        </p>
                    </div>
                    <div>
                        <i class="fa fa-2x fa-wallet text-success-light"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <!-- END Overview -->

</div>
<!-- Dynamic Table Full Pagination -->
<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">Order Summary <small>Full list of Crypto</small></h3>
    </div>
    <div class="table-responsive-sm table-responsive-md">
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                <thead>
                <tr>
                    <th class="text-center" style="width 80px;">#</th>
                    <th>Crypto</th>
                    <th>Qty</th>
                    <th>Total Invested</th>
                    <th>Current Value</th>
                    <th>Returns</th>
                    <th>Returns %</th>
                    <th>Avg. Buying Price</th>
                    <th>Current Buying Price</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($invests as $invest) {
                    $t = explode('/', $invest['symbol']); ?>
                    <tr>
                        <td class="text-center"><?= (1 + $invest['ctr']); ?></td>
                        <td class="has-symbol">
                            <span><?= $t[0]; ?></span>&nbsp;/&nbsp;<?= $t[1]; ?>
                        </td>
                        <td><?= $invest['qty']; ?></td>
                        <td class="fw-semibold"><?= $invest['inv']; ?></td>
                        <td class="fw-semibold"><?= $invest['curr']; ?></td>
                        <?php
                        if ($invest['pl_per'] > 0) {
                            ?>
                            <td><span class="badge bg-success"><?= $invest['returns']; ?></span></td>
                            <td><span class="badge bg-success"><?= $invest['pl_per']; ?>%</span></td>
                            <?php
                        } else {
                            ?>
                            <td><span class="badge bg-danger"><?= $invest['returns']; ?></span></td>
                            <td><span class="badge bg-danger"><?= $invest['pl_per']; ?>%</span></td>
                            <?php
                        } ?>
                        <td><?= $invest['dca']; ?></td>
                        <td><?= $invest['current_price']; ?></td>
                    </tr>
                    <?php
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- END Dynamic Table Full Pagination -->
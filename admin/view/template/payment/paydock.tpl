<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div id="content">
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/payment.png" alt=""/>&nbsp;<?php echo $heading_title; ?></h1>

            <div class="buttons"><a onclick="$('#form').submit();"
                                    class="button"><span><?php echo $button_save; ?></span></a><a
                        onclick="location = '<?php echo $cancel; ?>';"
                        class="button"><span><?php echo $button_cancel; ?></span></a></div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><span class="required">*</span> <?php echo $entry_api_key; ?></td>
                        <td><input type="text" name="paydock_api_key" value="<?php echo $paydock_api_key; ?>"/>
                            <?php if ($error_api_key) { ?>
                            <span class="error"><?php echo $error_api_key; ?></span>
                            <?php } ?></td>
                    </tr>

                    <tr>
                        <td><span class="required">*</span> <?php echo $entry_secret_key; ?></td>
                        <td><input type="text" name="paydock_secret_key" value="<?php echo $paydock_secret_key; ?>"/>
                            <?php if ($error_secret_key) { ?>
                            <span class="error"><?php echo $error_secret_key; ?></span>
                            <?php } ?></td>
                    </tr>

                    <tr>
                        <td><?php echo $entry_order_status; ?></td>
                        <td><select name="paydock_order_status_id">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $paydock_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"
                                        selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_geo_zone; ?></td>
                        <td><select name="paydock_geo_zone_id">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $paydock_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                                        selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status; ?></td>
                        <td><select name="paydock_status">
                                <?php if ($paydock_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_sort_order; ?></td>
                        <td><input type="text" name="paydock_sort_order" value="<?php echo $paydock_sort_order; ?>"
                                   size="1"/></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?>
<?php echo $header; ?><?php echo $column_left;
ini_set('display_errors',1);
error_reporting(E_ALL ^E_NOTICE);

?>
<link href="view/stylesheet/mysklad.css" rel="stylesheet">

<script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
<div id="content" style="margin-left:50px;">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-category" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo "Настройка модуля"; ?></h3>
      </div>
      <div class="panel-body">
        <div id="tabs" class="htabs">
          <a href="#tab-general"><?php echo $text_tab_general; ?></a>
          <a href="#tab-product"><?php echo $text_tab_product; ?></a>
          <a href="#tab-order"><?php echo $text_tab_order; ?></a>
          <a href="#tab-manual"><?php echo $text_tab_manual; ?></a>
        </div>
        <!--
        Начало формы
          !-->
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-category" class="form-horizontal">

          <div id="tab-general">
            <table class="form">
              <tr>
                <td><?php echo $entry_username; ?></td>
                <td><input name="myskladoc21_username" type="text" value="<?php echo $myskladoc21_username; ?>" /></td>
              </tr>
              <tr>
                <td><?php echo $entry_password; ?></td>
                <td><input name="myskladoc21_password" type="password" value="<?php echo $myskladoc21_password; ?>" /></td>
              </tr>

              <tr>
                <td><?php echo $entry_status; ?></td>
                <td><select name="myskladoc21_status">
                    <?php if ($myskladoc21_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select></td>
              </tr>

            </table>
          </div>

          <div id="tab-product">


          </div>

          <div id="tab-order">
            <table class="form">

              <tr>
                <td><?php echo $entry_order_status_to_exchange; ?></td>
                <td>
                  <select name="myskladoc21_order_status_to_exchange">
                    <option value="0" <?php echo ($myskladoc21_order_status_to_exchange == 0)? 'selected' : '' ;?>><?php echo $entry_order_status_to_exchange_not; ?></option>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <option value="<?php echo $order_status['order_status_id'];?>" <?php echo ($myskladoc21_order_status_to_exchange == $order_status['order_status_id'])? 'selected' : '' ;?>><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>

              <tr>
                <td><?php echo $entry_order_status; ?></td>
                <td>
                  <select name="myskladoc21_order_status">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <option value="<?php echo $order_status['order_status_id'];?>" <?php echo ($myskladoc21_order_status == $order_status['order_status_id'])? 'selected' : '' ;?>><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>

              <tr>
                <td><label for="myskladoc21_order_currency"><?php echo $entry_order_currency; ?></label></td>
                <td>
                  <input type="text" name="myskladoc21_order_currency" value="<?php echo $myskladoc21_order_currency; ?>">
                </td>
              </tr>
   </table>
          </div>

          <div id="tab-manual">
            <table class="form">
              <tr>
                <td>
                  <?php echo $entry_upload; ?>
                </td>
                <td>
                  <a id="button-upload" class="button"><?php echo $button_upload; ?></a>
                </td>
                <td>
                  <?php echo $text_max_filesize; ?>
                </td>
              </tr>
            </table>
          </div>

        </form>

        <!--
        Конец формы
          !-->
      </div>
    </div>
  </div>
</div>

<script type="text/javascript"><!--
  $('#tabs a').tabs();
  //--></script>
<script type="text/javascript" src="view/javascript/jquery/ajaxupload.js"></script>

<script type="text/javascript"><!--
  new AjaxUpload('#button-upload', {
    action: 'index.php?route=extension/module/myskladoc21/manualImport&token=<?php echo $token; ?>',
    name: 'file',
    autoSubmit: true,
    responseType: 'json',
    onSubmit: function(file, extension) {
      $('#button-upload').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');
      $('#button-upload').attr('disabled', true);
    },
    onComplete: function(file, json) {
      $('#button-upload').attr('disabled', false);
      $('.loading').remove();

      if (json['success']) {
        alert(json['success']);
      }

      if (json['error']) {
        alert(json['error']);
      }
    }
    

  });
  //--></script>
<script type="text/javascript"><!--
  var price_row = <?php echo $price_row; ?>;

  function addConfigPriceType() {
    html  = '';
    html += '  <tr id="myskladoc21_price_type_row' + price_row + '">';
    html += '    <td class="left"><input type="text" name="myskladoc21_price_type[' + price_row + '][keyword]" value="" /></td>';
    html += '    <td class="left"><select name="myskladoc21_price_type[' + price_row + '][customer_group_id]">';
  <?php foreach ($customer_groups as $customer_group) { ?>
      html += '      <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>';
    <?php } ?>
    html += '    </select></td>';
    html += '    <td class="center"><input type="text" name="myskladoc21_price_type[' + price_row + '][quantity]" value="0" size="2" /></td>';
    html += '    <td class="center"><input type="text" name="myskladoc21_price_type[' + price_row + '][priority]" value="0" size="2" /></td>';
    html += '    <td class="center"><a onclick="$(\'#myskladoc21_price_type_row' + price_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
    html += '  </tr>';

    $('#myskladoc21_price_type_id tfoot').before(html);

    $('#config_price_type_row' + price_row + ' .date').datepicker({dateFormat: 'yy-mm-dd'});
    price_row++;
  }
  //--></script>
<script type="text/javascript"><!--
  function image_upload(field, thumb) {
    $('#dialog').remove();

    $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

    $('#dialog').dialog({
      title: '<?php echo $text_image_manager; ?>',
      close: function (event, ui) {
        if ($('#' + field).attr('value')) {
          $.ajax({
            url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
            dataType: 'text',
            success: function(data) {
              $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
            }
          });
        }
      },
      bgiframe: false,
      width: 800,
      height: 400,
      resizable: false,
      modal: false
    });
  };
  //--></script>
<?php echo $footer; ?>
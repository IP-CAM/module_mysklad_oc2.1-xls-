<?php echo $header; ?><?php echo $column_left;
ini_set('display_errors',1);
error_reporting(E_ALL ^E_NOTICE);

$token = $_GET['token'];

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
            <table class="form">
              <tr>
                <td>
                  <?php echo $entry_upload; ?>
                </td>
                <td>
                  <a id="button-upload" class="button"><?php echo $button_upload; ?></a>
                </td>
              </tr>
              <tr>
                <td>
                  <?php echo $entry_download; ?>
                </td>
                <td>
                  <a id="button-downoload" class="button"><?php echo $button_download; ?></a>
                </td>

              </tr>
            </table>
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
<script type="text/javascript">
  $(document).ready(function(){
    $('#button-downoload').click(function(){
      $.ajax({
        url : 'index.php?route=module/myskladoc21/download&token=<?php echo $token;?>',
        type : 'post',
        dataType:'text',
        data :{
          xls : 1
        },
        success:function(data){
          location.href = data;

        },

      });
    });

  });


</script>



<?php echo $footer; ?>
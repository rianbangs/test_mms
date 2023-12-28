<style>

    .bg-aqua, .callout.callout-info, .alert-info, .label-info, .modal-info .modal-body {
        background-color: #00c0ef !important;
    }

    .bg-red, .bg-yellow, .bg-aqua, .bg-blue, .bg-light-blue, .bg-green, .bg-navy, .bg-teal, .bg-olive, .bg-lime, .bg-orange, .bg-fuchsia, .bg-purple, .bg-maroon, .bg-black, .bg-red-active, .bg-yellow-active, .bg-aqua-active, .bg-blue-active, .bg-light-blue-active, .bg-green-active, .bg-navy-active, .bg-teal-active, .bg-olive-active, .bg-lime-active, .bg-orange-active, .bg-fuchsia-active, .bg-purple-active, .bg-maroon-active, .bg-black-active, .callout.callout-danger, .callout.callout-warning, .callout.callout-info, .callout.callout-success, .alert-success, .alert-danger, .alert-error, .alert-warning, .alert-info, .label-danger, .label-info, .label-warning, .label-primary, .label-success, .modal-primary .modal-body, .modal-primary .modal-header, .modal-primary .modal-footer, .modal-warning .modal-body, .modal-warning .modal-header, .modal-warning .modal-footer, .modal-info .modal-body, .modal-info .modal-header, .modal-info .modal-footer, .modal-success .modal-body, .modal-success .modal-header, .modal-success .modal-footer, .modal-danger .modal-body, .modal-danger .modal-header, .modal-danger .modal-footer {
        color: #fff !important;
    }

    .small-box {
        border-radius: 2px;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        margin-left: 40px;
        margin-right: 30px;
    }
    .small-box>.inner {
        padding: 35px;
    }
    .small-box h3, .small-box p {
        z-index: 5;
    }
    .small-box h3 {
        font-size: 38px;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box h3, .small-box p {
        z-index: 5;
    }
    .small-box p {
        font-size: 25px;
    }
    p {
        margin: 0 0 10px;
    }
    .small-box .icon_d {
        -webkit-transition: all .3s linear;
        -o-transition: all .3s linear;
        transition: all .3s linear;
        position: absolute;
        top: -10px;
        right: 20px;
        z-index: 0;
        font-size: 90px;
        color: rgba(0,0,0,0.15);
    }
    
    .small-box>.small-box-footer {
        position: relative;
        text-align: center;
        padding: 10px 0;
        color: #fff;
        color: rgba(255,255,255,0.8);
        display: block;
        z-index: 10;
        background: rgba(0,0,0,0.1);
        text-decoration: none;
    }
    
</style>



<section class="content-header">
  
    <div class="row">
      <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <p>Pending <b>Reorder</b></p>
            <?php
                    $pending  = 0;
                    $approved = 0;

                    $current_user_login  = $this->Mms_mod->get_user_connection($_SESSION['user_id']);
                    $reorder_list        = $this->Mms_mod->get_entries_reorder_report_data_header_final_by_bu('');
                  
                    if(!empty($reorder_list))
                    {                        
                        $po_calendar         = $this->Mms_mod->get_po_calendar($reorder_list[0]['supplier_code']);
                        foreach($reorder_list as $list)
                        {
                             if($list['status'] == 'Pending')
                             {
                                 $pending += 1;
                             }
                             else 
                             if(
                                  ($po_calendar[0]['approver'] == 'Category-Head' && $list["status"] == 'Approved by-category-head')  || 
                                  ($po_calendar[0]['approver'] == 'Corp-Manager' && in_array($list["status"],array('Approved by-corp-manager','Approved by-incorporator')) )                                    )
                               {
                                    $approved += 1;
                               }
                        }
                    }
                    
                    echo '<p style="font-size:16px;">Regular <span id="rr_pending_span" class="badge badge-danger">'.$pending.'</span></p>';
             ?>

            <p style="font-size:16px;">Season <span id="sr_pending_span" class="badge badge-danger">0</span></p>
          </div>
          <div class="icon_d">
            <i class="fa fa-shopping-cart"></i>
          </div>
          <a href="#" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <p>Approved <b>Reorder</b></p>
            <p style="font-size:16px;">Regular <span id="rr_approved_span" class="badge badge-danger"><?php echo $approved;  ?></span></p>
            <p style="font-size:16px;">Season <span id="sr_approved_span" class="badge badge-danger">0</span></p>
          </div>
          <div class="icon_d">
            <i class="fas fa-clipboard-check"></i>
          </div>
          <a href="#" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

</section>

<script>
    
    function showCount(){
        $.ajax({
            url: '<?php echo site_url('Po_ctrl/countSeasonPo')?>', 
            type: 'POST',
            success: function(response) {
                var jObj = JSON.parse(response);
                $("#sr_pending_span").html(jObj.pending);
                $("#sr_approved_span").html(jObj.approved);
            }

          });
    }

    showCount();

</script>


<?php require_once './header.php'; 
include('inc/code-generator.php');
include('inc/analytics.php');
?>

<?php


if(isset($_POST['addEvent'])){

  $title = trim($_POST['title']);
  $customer_id = trim($_POST['customer_id']);
  $description = trim($_POST['description']);
  $start = $_POST['start'];
  $end = $_POST['end'];
  $color = trim($_POST['color']);
  $appt_code = trim($_POST['appt_code']);
  if($customer_id==0){
    $isPersonal="true";
  } else{
    $isPersonal='false';
  }

  $insert = $pdo->prepare("INSERT INTO events( appt_code, customer_id, title, description, start, end, color, status, isPersonal) values ( :appt_code, :customer_id, :title, :description, :start, :end, :color, 'PENDING',:isPersonal)");
  $insert->bindParam(":appt_code", $appt_code);
  $insert->bindParam(":customer_id", $customer_id);
  $insert->bindParam(":title", $title);
  $insert->bindParam(":description", $description);
  $insert->bindParam(":start", $start);
  $insert->bindParam(":end", $end);
  $insert->bindParam(":color", $color);
  $insert->bindParam(":isPersonal", $isPersonal);

  $insert->execute();

  if ($insert->rowCount()) {
            //echo "Application Submitted Successfully";
    ?>

    <script type="text/javascript">
      window.addEventListener("load", function() {
        swal({
          title: "Event Added Successfully",
          text: "<?php echo $appt_code ?>, Has Been Scheduled",
          icon: "success",
          showCancelButton: false,
          showConfirmButton: false,
          buttons: false
        });
      });
    </script>
    <?php
  } else {
    ?>
    <script>
      window.addEventListener("load", function() {
        swal({
          title: "Error",
          text: "Add Event Failed",
          icon: "error",
          showConfirmButton: false,
          showCancelButton: false,
          buttons: false
        });
      });
    </script>

    <?php
  }
  header("refresh:2,appointments.php");

}



if(isset($_POST['editEvent'])){

  $id = trim($_POST['event_id']);
  $title = trim($_POST['title']);
  $customer_id = trim($_POST['customer_id']);
  $description = trim($_POST['description']);
  $color = trim($_POST['color']);
  $appt_code = trim($_POST['appt_code']);
  if($customer_id==0){
    $isPersonal="true";
  } else{
    $isPersonal='false';
  }

  $update = $pdo->prepare("UPDATE events set  appt_code=:appt_code, customer_id=:customer_id, title=:title, description=:description, color=:color, isPersonal=:isPersonal where id=:id ");
  $update->bindParam(":appt_code", $appt_code);
  $update->bindParam(":customer_id", $customer_id);
  $update->bindParam(":title", $title);
  $update->bindParam(":description", $description);
  $update->bindParam(":color", $color);
  $update->bindParam(":isPersonal", $isPersonal);
  $update->bindParam(":id", $id);

  if ($update->execute()) {
            //echo "Application Submitted Successfully";
    ?>

    <script type="text/javascript">
      window.addEventListener("load", function() {
        swal({
          title: "Event Updated Successfully",
          text: "<?php echo $appt_code ?>, Has Been Updated",
          icon: "success",
          showCancelButton: false,
          showConfirmButton: false,
          buttons: false
        });
      });
    </script>
    <?php
  } else {
    ?>
    <script>
      window.addEventListener("load", function() {
        swal({
          title: "Error",
          text: "Update Event Failed",
          icon: "error",
          showConfirmButton: false,
          showCancelButton: false,
          buttons: false
        });
      });
    </script>

    <?php
  }
  header("refresh:2,appointments.php");

}

if(isset($_POST['confirmEvent'])){

  $id = trim($_POST['event_id']);
  $color = trim($_POST['color']);

  $update = $pdo->prepare("UPDATE events set  status='PENDING' where id=:id ");
  $update->bindParam(":id", $id);

  if ($update->execute()) {
            //echo "Application Submitted Successfully";
    ?>

    <script type="text/javascript">
      window.addEventListener("load", function() {
        swal({
          title: "Request Confirmed Successfully",
          text: "<?php echo $_POST['event_code'] ?>, Has Been Confirmed",
          icon: "success",
          showCancelButton: false,
          showConfirmButton: false,
          buttons: false
        });
      });
    </script>
    <?php
  } else {
    ?>
    <script>
      window.addEventListener("load", function() {
        swal({
          title: "Error",
          text: "Confirmation Failed",
          icon: "error",
          showConfirmButton: false,
          showCancelButton: false,
          buttons: false
        });
      });
    </script>

    <?php
  }
  header("refresh:2,appointments.php");

}


function fill_customers()
{
  global $pdo;
  $output = '';
  $select_customer = $pdo->prepare("SELECT id, name from customers");
  $select_customer->execute();
  if ($select_customer->rowCount()) {
    while ($row = $select_customer->fetch(PDO::FETCH_OBJ)) {
      $output .= "<option value='{$row->id}'>{$row->name}</option>";
    }
  }
  return $output;
}


$sql = "SELECT * FROM events where (events.isPersonal='false' or events.customer_id=0) and status not in ('DECLINED','CANCELLED','FOR APPROVAL') ";
$req = $pdo->prepare($sql);
$req->execute();

$events = $req->fetchAll();


if(isset($_GET['cancelled'])){

  ?>

  <script type="text/javascript">
    window.addEventListener("load", function() {
      swal({
        title: "Notice",
        text: "Event Cancelled",
        icon: "info",
        showConfirmButton: false,
        showCancelButton: false,
        buttons: false
      });
    });
  </script>
  <?php


  header("refresh:2,appointments.php");
}

if(isset($_GET['done'])){

  ?>

  <script type="text/javascript">
    window.addEventListener("load", function() {
      swal({
        title: "Success",
        text: "Marked as Done",
        icon: "success",
        showConfirmButton: false,
        showCancelButton: false,
        buttons: false
      });
    });
  </script>
  <?php


  header("refresh:2,appointments.php");
}

if(isset($_GET['declined'])){

  ?>

  <script type="text/javascript">
    window.addEventListener("load", function() {
      swal({
        title: "Notice",
        text: "Appointment Declined",
        icon: "info",
        showConfirmButton: false,
        showCancelButton: false,
        buttons: false
      });
    });
  </script>

  <?php

  header("refresh:2,appointments.php");

}


?>

<script type="text/javascript">
  $('#nav-appt').find('a').toggleClass('active');
</script>

<style type="text/css">
  .fc-timegrid-slot {
    height: 2.5em !important; 
    border-bottom: 0 !important;
  }
  #editEvent #appt_code,#editEvent #title,#editEvent #description,#editEvent #customer_id,#editEvent #color{
    background-color: white !important;
  }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">

    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid"> 
      <!-- Info boxes -->
      <div class="row mt-2">
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar-days"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">APPOINTMENTS LEFT</span>
              <span class="info-box-number" id="appt-left-today-counter"><?=$left_today?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-check"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">ATTENDED TODAY</span>
              <span class="info-box-number"><?=$attended_today?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-hourglass-half"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">PENDING APPROVAL</span>
              <span class="info-box-number"><?=$for_approval?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      <div class="row mt-3" >
        <!-- /.col -->
        <div class="col-md-7">
          <div class="card card-primary" style="height: 100%;" >
            <div class="card-body p-0"  style="height: 100%;">
              <!-- THE CALENDAR -->
              <div id="calendar" style="height: 100%; padding: 10px;"></div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-5">

          <div class="card card-primary card-outline card-outline-tabs" style="height: 100%">
            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" id="schedulesTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="today-list" data-toggle="pill" href="#schedToday" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">SCHEDULED TODAY</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="requests-list" data-toggle="pill" href="#apptReq" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">REQUESTS</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="history-list" data-toggle="pill" href="#schedHistory" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">HISTORY</a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content" id="schedulesTabContent">
               <div class="tab-pane fade show active" id="schedToday" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                <!-- TABLE: LATEST ORDERS -->
                <div class="card " style="border: none">
                  <div class="card-body p-0" style="overflow-x: auto">
                      <table id="sched-today-tbl" class="table m-0 table-striped">
                        <thead>
                          <tr>
                            <th>CUSTOMER</th>
                            <th>EVENT</th>
                            <th>TIME</th>
                            <th style="width: 10px !important">ACTIONS</th>
                          </tr>
                        </thead>
                        <tbody id="event-today-list-tbody">
                         <?php
                         $get_event = $pdo->prepare("SELECT events.id, events.appt_code, events.customer_id, customers.name, events.title, events.description, events.start, events.end from events inner join customers on events.customer_id=customers.id where status='PENDING' and start >= concat(curdate(),' ','00:00:00') and start <= concat(curdate(),' ','23:59:59') ORDER BY `start` ASC");
                         $get_event->execute();
                         while ($event = $get_event->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                          ?>

                          <tr>
                            <input type="hidden" id="event-details-id-<?=$event->id?>" value="<?=$event->id?>">
                            <input type="hidden" id="event-details-description-<?=$event->id?>" value="<?=$event->description?>">
                            <input type="hidden" id="event-details-title-<?=$event->id?>" value="<?=$event->title?>">
                            <input type="hidden" id="event-details-name-<?=$event->id?>" value="<?=$event->name?>"> 
                            <input type="hidden" id="event-details-customer_id-<?=$event->id?>" value="<?=$event->customer_id?>">                         
                            <input type="hidden" id="event-details-appt_code-<?=$event->id?>" value="<?=$event->appt_code?>">
                            <td><?=$event->name?></td>
                            <td><?=$event->title?></td>
                            <td id="event-today-time-<?=$event->id?>"><?=date("h:i a",strtotime(explode(" ", $event->start)[1]))?></td>
                            <td ><center><button type="button" class="shadow btn btn-info btn-xs mr-1" onclick="eventDetails(<?=$event->id?>)"><i class="fa fa-eye"></i></button></center></td>

                          </tr>
                          <?php  
                        }
                        ?>
                      </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <div class="tab-pane fade" id="apptReq" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
             <!-- TABLE: LATEST ORDERS -->
             <div class="card " style="border: none">
              <!-- /.card-header -->
              <div class="card-body p-0" style="overflow-x: auto">
                  <table id="appt-req-tbl" class="table m-0 table-striped">
                    <thead>
                      <tr>
                        <th>CUSTOMER</th>
                        <th>EVENT</th>
                        <th>DATE</th>
                        <th style="width: 10px !important">VIEW</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php
                     $get_event = $pdo->prepare("SELECT events.id, events.appt_code, events.customer_id, customers.name, events.title, events.description, events.start, events.end from events left join customers on events.customer_id=customers.id where status='FOR APPROVAL' and isPersonal='false' ORDER BY `start` DESC");
                     $get_event->execute();
                     while ($event = $get_event->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                      ?>

                      <tr>
                        <input type="hidden" id="event-id-<?=$event->id?>" value="<?=$event->id?>">
                        <input type="hidden" id="event-description-<?=$event->id?>" value="<?=$event->description?>">
                        <input type="hidden" id="event-title-<?=$event->id?>" value="<?=$event->title?>">
                        <input type="hidden" id="event-name-<?=$event->id?>" value="<?=$event->name?>"> 
                        <input type="hidden" id="event-customer_id-<?=$event->id?>" value="<?=$event->customer_id?>">                         
                        <input type="hidden" id="event-appt_code-<?=$event->id?>" value="<?=$event->appt_code?>">
                        <td><?=$event->name?></td>
                        <td><?=$event->title?></td>
                        <td><?=$event->start?></td>
                        <td><center><button type="button" class="shadow btn btn-info btn-xs" onclick="viewEvent(<?=$event->id?>)"><i class="fa fa-eye"></i></button></center></td>

                      </tr>
                      <?php  
                    }
                    ?>
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>

         <div class="tab-pane fade" id="schedHistory" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
             
            <div class="card " style="border: none">
                <div class="card-body p-0" style="overflow-x: auto">
                  <table id="sched-history-tbl" class="table m-0 table-striped">
                    <thead>
                      <tr>
                        <th>CUSTOMER</th>
                        <th>EVENT</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                        <th>VIEW</th>
                      </tr>
                    </thead>
                    <tbody id="event-history-list-tbody">
                     <?php
                     $get_event = $pdo->prepare("SELECT events.id, events.appt_code, customers.name, events.isPersonal, events.status, events.customer_id, events.title, events.description, DATE_FORMAT(events.start, '%d - %M %Y %h:%i %p') as start, events.end, decline_info.note from events left join customers on events.customer_id=customers.id left join decline_info on events.id=decline_info.event_id where events.isPersonal='false' or events.customer_id=0 ORDER BY `start` DESC");
                     $get_event->execute();
                     while ($event = $get_event->fetch(PDO::FETCH_OBJ)) {
                                        // var_dump($product);
                      ?>
        
                      <tr>
                        <input type="hidden" id="event-history-description-<?=$event->id?>" value="<?=$event->description?>">
                        <input type="hidden" id="event-history-title-<?=$event->id?>" value="<?=$event->title?>">
                        <input type="hidden" id="event-history-status-<?=$event->id?>" value="<?=$event->status?>">
        
                        <td><?php if($event->customer_id!=0) echo $event->name; else echo "PERSONAL";?></td>
                        <td><?=$event->title?></td>
                        <td id="event-history-time-<?=$event->id?>"><?=$event->start?></td>
                        <td>
                           <?php
                              if($event->status=='DONE'){
                                echo '<span class="badge badge-success">'.$event->status.'</span>';
                              } else if($event->status=='CANCELLED' || $event->status=='DECLINED'){
                                echo '<span class="badge badge-danger">'.$event->status.'</span>';
                              } else if($event->status=='PENDING'){
                                echo '<span class="badge badge-warning">'.$event->status.'</span>';
                              } else {
                                echo '<span class="badge badge-info">'.$event->status.'</span>';
                              }
                              
                            ?>
                        </td>
                        <td >
                          <center>
                            <button class="shadow btn btn-info btn-xs" data-html="true" id="pop-<?= $event->id?>" tabindex="0" role="button" data-toggle="popover" data-trigger="click"  data-container="body" title="<?= $event->title?>" 
                              data-content="
                              <?php 
                                echo $event->description;
                                if($event->status=='DECLINED' || $event->status=='CANCELLED'){
                                  echo '<hr><strong>Cancel Note:</strong><br>'.$event->note;
                                }
                              ?>">
                              <span><i class="fa fa-eye"></i></span></button>
                          </center>
                        </td>
        
                      </tr>
                      <?php  
                    }
                    ?>
                    </tbody>
                  </table>
                 </div>
             </div>
         </div>

      </div>
    </div>
  </div>

</div>
</div>
<!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<div class="modal fade" id="addEvent">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content" style="">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">Event Info</h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label style="width: 100%">Appt. Code</label>
                  <input style="display: block !important; width: 100%" type="text" id="appt_code" name="appt_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" value="" >
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label style="width: 100%">Customer</label>
                  <select style="width: 100%" name="customer_id" id="customer_id" class="form-control select select2" required><option value="0">Personal</option><?=fill_customers()?></select>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Title</label>
                  <input type="text" name="title" class="form-control" id="title" placeholder="Title" required>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Description</label>
                  <textarea name="description" class="form-control" id="description" placeholder="Description" rows="3" required></textarea> 
                </div>
              </div>
              <br>
              <div class="form-row">
                <label for="color" class=" control-label" style="width: 100%;">Color</label>
                <div class="" style="width:100%;">
                  <select name="color" class="form-control" id="color" required>
                    <option style="color:#0071c5;" value="#0071c5">&#9724; Dark blue</option>
                    <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
                    <option style="color:#008000;" value="#008000">&#9724; Green</option>             
                    <option style="color:#FFD700;" value="#FFD700">&#9724; Yellow</option>
                    <option style="color:#FF8C00;" value="#FF8C00">&#9724; Orange</option>
                    <option style="color:#FF0000;" value="#FF0000">&#9724; Red</option>
                    <option style="color:#000;" value="#000">&#9724; Black</option>

                  </select>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-sm-6">
                  <label for="start" class="col-sm-12 control-label">Start date</label>
                  <div class="col-sm-12">
                    <input type="text" name="start" class="form-control" id="start" readonly>
                  </div>
                </div>
                <div class="col-sm-6">
                  <label for="end" class="col-sm-12 control-label">End date</label>
                  <div class="col-sm-12">
                    <input type="text" name="end" class="form-control" id="end" readonly>
                  </div>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-6">
                  <input type="submit" name="addEvent" value="Add Event" class="btn btn-success" value="">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<div class="modal fade" id="editEvent">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content" style="">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">Event Info</h3 ><span id="edit-event-badge" class="ml-2"></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label style="width: 100%">Appt. Code</label>
                  <input style="display: block !important; width: 100%" type="text" id="appt_code" name="appt_code" value="" class="form-control" value="" >
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label style="width: 100%">Customer</label>
                  <select style="width: 100%" name="customer_id" class="form-control select select2" id="customer_id" required><option value="0">Personal</option><?=fill_customers()?></select>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Title</label>
                  <input type="text" name="title" class="form-control" id="title" placeholder="Title" required>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Description</label>
                  <textarea name="description" class="form-control" id="description" placeholder="Description" rows="3" required></textarea> 
                </div>
              </div>
              <br>
              <div class="form-row">
                <label for="color" class=" control-label" style="width: 100%;">Color</label>
                <div class="" style="width:100%;">
                  <select name="color" class="form-control" id="color" required>
                    <option style="color:#0071c5;" value="#0071c5">&#9724; Dark blue</option>
                    <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
                    <option style="color:#008000;" value="#008000">&#9724; Green</option>             
                    <option style="color:#FFD700;" value="#FFD700">&#9724; Yellow</option>
                    <option style="color:#FF8C00;" value="#FF8C00">&#9724; Orange</option>
                    <option style="color:#FF0000;" value="#FF0000">&#9724; Red</option>
                    <option style="color:#000;" value="#000">&#9724; Black</option>

                  </select>
                </div>
              </div>
              <input type="hidden" name="event_id" id="id" value="">

              <br> 
              <div class="form-row" id="cancel-note-div" style="display: none">
                <div class="col-md-12">
                  <label>Note</label>
                  <textarea name="note" class="form-control" id="cancel-note" placeholder="Reason to cancel this event" rows="2"></textarea> 
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12" style="text-align: left" id="editEventBtn">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<div class="modal fade" id="viewEvent">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content" style="">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important" id="view-event-code"></h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-6">
                  <label style="width: 100%">Customer</label>
                  <p id="view-event-customer"></p>
                </div>
                <div class="col-md-6">
                  <label>Title</label>
                  <p id="view-event-title"></p>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Description</label>
                  <textarea style="background-color: transparent;" name="description" class="form-control" id="view-event-description" placeholder="Description" rows="3" readonly></textarea> 
                </div>
              </div>
              <br>
              <div class="form-row" id="event-color-div">
                <label for="color" class=" control-label" style="width: 100%;">Color</label>
                <div class="" style="width:100%;">
                  <select name="color" class="form-control" id="view-event-color" required>
                    <option style="color:#0071c5;" value="#0071c5">&#9724; Dark blue</option>
                    <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
                    <option style="color:#008000;" value="#008000">&#9724; Green</option>             
                    <option style="color:#FFD700;" value="#FFD700">&#9724; Yellow</option>
                    <option style="color:#FF8C00;" value="#FF8C00">&#9724; Orange</option>
                    <option style="color:#FF0000;" value="#FF0000">&#9724; Red</option>
                    <option style="color:#000;" value="#000">&#9724; Black</option>

                  </select>
                </div>
              </div>
              <input type="hidden" name="event_id" id="view-event-id" value="">
              <input type="hidden" name="event_code" id="view-event-code-input" value="">
              <br> 
              <div class="form-row" id="decline-note-div" style="display: none">
                <div class="col-md-12">
                  <label>Note</label>
                  <textarea name="note" class="form-control" id="decline-note" placeholder="Reason to decline this appointment" rows="2" ></textarea> 
                </div>
              </div>
              <div class="form-row" id="appt-payment-div" style="display: none">
                <div class="col-md-12">
                  <label>Bill</label>
                  <input type="number" name="bill" class="form-control" id="appt-payment-bill" placeholder="Bill" > 
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12" style="text-align: left" >
                  <button type="button" id="decline-btn" class="btn btn-danger" onclick="declineEvent()" >Decline</button>
                  <input type="submit" id="event-submit-btn" name="confirmEvent" value="Confirm" class="btn btn-success" value="">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="addEvent">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content" style="">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">Event Info</h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label style="width: 100%">Appt. Code</label>
                  <input style="display: block !important; width: 100%" type="text" id="appt_code" name="appt_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" value="" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label style="width: 100%">Customer</label>
                  <select style="width: 100%" name="customer_id" id="customer_id" class="form-control select select2" required><option value="0">Personal</option><?=fill_customers()?></select>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Title</label>
                  <input type="text" name="title" class="form-control" id="title" placeholder="Title" required>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Description</label>
                  <textarea name="description" class="form-control" id="description" placeholder="Description" rows="3" required></textarea> 
                </div>
              </div>
              <br>
              <div class="form-row">
                <label for="color" class=" control-label" style="width: 100%;">Color</label>
                <div class="" style="width:100%;">
                  <select name="color" class="form-control" id="color" required>
                    <option style="color:#0071c5;" value="#0071c5">&#9724; Dark blue</option>
                    <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
                    <option style="color:#008000;" value="#008000">&#9724; Green</option>             
                    <option style="color:#FFD700;" value="#FFD700">&#9724; Yellow</option>
                    <option style="color:#FF8C00;" value="#FF8C00">&#9724; Orange</option>
                    <option style="color:#FF0000;" value="#FF0000">&#9724; Red</option>
                    <option style="color:#000;" value="#000">&#9724; Black</option>

                  </select>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-sm-6">
                  <label for="start" class="col-sm-12 control-label">Start date</label>
                  <div class="col-sm-12">
                    <input type="text" name="start" class="form-control" id="start" readonly>
                  </div>
                </div>
                <div class="col-sm-6">
                  <label for="end" class="col-sm-12 control-label">End date</label>
                  <div class="col-sm-12">
                    <input type="text" name="end" class="form-control" id="end" readonly>
                  </div>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-6">
                  <input type="submit" name="addEvent" value="Add Event" class="btn btn-success" value="">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- fullCalendar 2.2.5 --> 
<script src="<?= $baseurl?>assets/plugins/moment/moment.min.js"></script>
<script src="<?= $baseurl?>assets/plugins/fullcalendar/main.js"></script>

<script src='https://cdn.jsdelivr.net/npm/rrule@2.6.4/dist/es5/rrule.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@5.5.0/main.global.min.js'></script>
<!-- jQuery Version 1.11.1 -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>



<!-- DataTables  & Plugins -->
<script src="<?=$baseurl ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/jszip/jszip.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
  $(function () {

    var date = new Date()
    var d    = date.getDate(),
    m    = date.getMonth(),
    y    = date.getFullYear()

    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;
    var calendarEl = document.getElementById('calendar');


    var calendar = new Calendar(calendarEl, {

      headerToolbar: {
        left  : 'prev,next today',
        center: 'title',
        right : false,
      },
      buttonText:{
        listMonth: 'listMonth',
      },
      themeSystem: 'bootstrap',
      //Random default events
      events: [
      <?php foreach($events as $event): 

        $start = explode(" ", $event['start']);
        $end = explode(" ", $event['end']);
        if($start[1] == '00:00:00'){
          $start = $start[0];
        }else{
          $start = $event['start'];
        }
        if($end[1] == '00:00:00'){
          $end = $end[0];
        }else{
          $end = $event['end'];
        }
        ?>
        {
          id: '<?php echo $event['id']; ?>',
          title: '<?php echo $event['title']; ?>',
          extendedProps: {
            desc: '<?php echo addslashes($event['description']); ?>',
            customer_id: '<?php echo $event['customer_id']; ?>',
            appt_code: '<?php echo $event['appt_code']; ?>',
            status: '<?php echo $event['status']; ?>'
          },
          start: '<?php echo $start; ?>',
          end: '<?php echo $end; ?>',
          backgroundColor: '<?php echo $event['color']; ?>',
          borderColor: '<?php echo $event['color']; ?>',
          editable: '<?php if($event['status']=="DONE") echo false; else echo true  ?>'
        },
      <?php endforeach; ?>
      ],
      loading: function( isLoading, view ) {
          if(isLoading) {// isLoading gives boolean value
            $('#pre-loader').css("display","flex");
          } else {
            $('#pre-loader').css("display","none");
          }
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        editable  : true,
      droppable : true, // this allows things to be dropped onto the calendar !!!
      //navLinks: true,
      //initialDate: '2020-12-12',
      selectable: true,
      select: function(event) {
        const d1 = new Date(event.startStr.split(' ')[0]);
        
        if(isSunday(d1)){
            $("#addEvent #customer_id").attr("disabled","disabled");
            toastr.info("Cannot schedule clinical appointments on Sundays!")
        } else {
          $("#addEvent #customer_id").removeAttr("disabled");
        }

        $('#addEvent #start').val(moment(event.startStr).format('YYYY-MM-DD HH:mm:ss'));
        $('#addEvent #end').val(moment(event.endStr).format('YYYY-MM-DD HH:mm:ss'));
        $('#addEvent').modal('show');
      },

      eventClick: function(info) {
        const d1 = new Date(info.event.startStr.split(' ')[0]);
        var d2 = new Date(info.event.endStr.split(' ')[0]);
        d2 = moment(d2).subtract(1, 'days').toDate();
        
        $('#editEvent #id').val(info.event.id);
        $('#editEvent #appt_code').val(info.event.extendedProps.appt_code);
        $('#editEvent #title').val(info.event.title);
        $('#editEvent #description').val(info.event.extendedProps.desc);
        $('#editEvent #customer_id').val(info.event.extendedProps.customer_id);
        $('#editEvent #customer_id').trigger('change');
        $('#editEvent #color').val(info.event.backgroundColor);
       
         if(info.event.extendedProps.status == "DONE"){
          $('#editEvent #edit-event-badge').html('<span class="badge badge-success ml-2">'+info.event.extendedProps.status+'</span>');
        } else if(info.event.extendedProps.status == "PENDING"){
          $('#editEvent #edit-event-badge').html('<span class="badge badge-warning ml-2">'+info.event.extendedProps.status+'</span>');
        } else{
          $('#editEvent #edit-event-badge').html('<span class="badge badge-info ml-2">'+info.event.extendedProps.status+'</span>');
        }

        $("#cancel-note-div").css("display", "none");
        $("#cancel-note").val("");

        if(isSunday(d1)||isSunday(d2)){
            $("#editEvent #customer_id").attr("disabled","disabled");
            toastr.info("Cannot schedule clinical appointments on Sundays!")
        } else {
          $("#editEvent #customer_id").removeAttr("disabled");
        }

          
          $('#edit-event-save-btn').remove();
          $('#edit-event-delete-btn').remove();

        if(info.event.extendedProps.status == "DONE"){
          $('#editEvent #appt_code').attr("readonly","readonly");
          $('#editEvent #title').attr("readonly","readonly");
          $('#editEvent #description').attr("readonly","readonly");
          $('#editEvent #customer_id').attr("disabled","disabled");
          $('#editEvent #color').attr("disabled","disabled");

        } else {

          $('#editEvent #appt_code').removeAttr("readonly");
          $('#editEvent #title').removeAttr("readonly");
          $('#editEvent #description').removeAttr("readonly");
          $('#editEvent #customer_id').removeAttr("disabled");
          $('#editEvent #color').removeAttr("disabled");

          $("#editEventBtn").prepend(`
              <button type="button" class="btn btn-danger" id="edit-event-delete-btn" onclick="cancelEvent()" >Delete Event</button>
              <input type="submit" name="editEvent" id="edit-event-save-btn" value="Save Changes" class="btn btn-success" value="">
          `);

        }

        $('#editEvent').modal('show');
      },
      eventDrop: function(info) { // si changement de position

         if (moment().diff(info.event.startStr, moment(new Date())) <= 0) {
            const d1 = new Date(info.event.startStr.split(' ')[0]);
            var d2 = new Date(info.event.endStr.split(' ')[0]);
            d2 = moment(d2).subtract(1, 'days').toDate();
            if((isSunday(d1)||isSunday(d2)) && info.event.extendedProps.customer_id != 0){
              toastr.error("Cannot schedule clinical appointments on Sundays!");
              info.revert();
            } else {
              edit(info);
            }

        } else{
          toastr.error('Not allowed!');
          info.revert();
        }

      },
      eventResize: function(info) { // si changement de longueur

        if (moment().diff(info.event.startStr, moment(new Date())) <= 0) {
            const d1 = new Date(info.event.startStr.split(' ')[0]);
            var d2 = new Date(info.event.endStr.split(' ')[0]);
            d2 = moment(d2).subtract(1, 'days').toDate();
            if((isSunday(d1) || isSunday(d2)) && info.event.extendedProps.customer_id != 0){
              toastr.error("Cannot schedule clinical appointments on Sundays!");
              info.revert();
            } else {
              edit(info);
            }

        } else{
          toastr.error('Not allowed!');
          info.revert();
        }

      },
       selectAllow: function(select) {
        return moment().diff(select.start, moment(new Date())) <= 0
     }
    });

    calendar.render();
    // $('#calendar').fullCalendar()


    /* ADDING EVENTS */

    var currColor = '#3c8dbc' //Red by default
    // Color chooser button
    $('#color-chooser > li > a').click(function (e) {
      e.preventDefault()
      // Save color
      currColor = $(this).css('color')
      // Add color effect to button
      $('#add-new-event').css({
        'background-color': currColor,
        'border-color'    : currColor
      })
    }); 

    var parent = $('.fc-toolbar-chunk').eq(2);
    parent.find('div').remove();

    var div = $('<div>',{class: 'dropdown'});

    var select = $('<select>',{class: 'btn btn-primary',id:"calendar-view"})
    //dayGridMonth,timeGridWeek,timeGridDay,listMonth,listWeek,listDay
    select.append('<option value="dayGridMonth" selected>Month</option>')
    .append('<option value="timeGridWeek">Week</option>')
    .append('<option value="timeGridDay">Day</option>')
    .append('<option value="listMonth">Month List</option>')
    .append('<option value="listWeek">Week List</option>')
    .append('<option value="listDay">Day List</option>');
    
    div.append(select);
    parent.append(div);

  $("#calendar-view").on( 'input' ,function(e){
    calendar.changeView($("#calendar-view").val());
  });

  function isSunday(date = new Date()) {
    return date.getDay() === 0;
  }

  function edit(info){
   start = moment(info.event.startStr).format('YYYY-MM-DD HH:mm:ss');
   if(info.event.endStr){
    end = moment(info.event.endStr).format('YYYY-MM-DD HH:mm:ss');
  }else{
    end = start;
  }

  id =  info.event.id;

  Event = [];
  Event[0] = id;
  Event[1] = start;
  Event[2] = end;
  
  $.ajax({
   url: 'ajax.php',
   type: "POST",
   data: {Event:Event},
    beforeSend: function(){
        $("#pre-loader").css("display","flex");
    },
   success: function(rep) {

    const data = JSON.parse(rep);
        //console.log(data);
        if(data.response == 'ok'){
              $.ajax({
                url: 'ajax.php',
                data: {
                  dateChange: 'change',
                  id: id
                },
                type: 'POST',
                success: function(res){
                  const events = JSON.parse(res);
                  let html = '';
                  if(events.length > 0){
                    events.forEach( item => {
                      html+= `
            
                      <tr>
                      <input type="hidden" id="event-details-id-${item.id}" value="${item.id}">
                      <input type="hidden" id="event-details-description-${item.id}" value="${item.description}">
                      <input type="hidden" id="event-details-title-${item.id}" value="${item.title}">
                      <input type="hidden" id="event-details-name-${item.id}" value="${item.name}"> 
                      <input type="hidden" id="event-details-customer_id-${item.id}" value="${item.customer_id}">                         
                      <input type="hidden" id="event-details-appt_code-${item.id}" value="${item.appt_code}">
                      <td>${item.name}</td>
                      <td>${item.title}</td>
                      <td>${item.time}</td>
                      <td ><center><button type="button" class="shadow btn btn-info btn-xs mr-1" onclick="eventDetails(${item.id})"><i class="fa fa-eye"></i></button></center></td>
            
                      </tr>
            
                      `;
                      
            
                    });
                    //console.log(counter);
                    $('#event-today-list-tbody').html(html);
                   
                  } else{
            
                      var table = $('#sched-today-tbl').DataTable();
                   
                      table.clear();
                      table.draw();
                  } 
                    $('span#appt-left-today-counter').text(events.length);
                      
                }
              });
        }else{
          toastr.error('Could not be saved. try again.'); 
        }
                $("#pre-loader").css("display","none");
                toastr.success('Saved');
      }
    });




}



});


</script>

<script>

  function declineEvent(){

    $("#appt-payment-div").css("display","none");

    if($("#decline-note-div").is(':hidden')) {
      $("#decline-note-div").css("display", "block");
      if($("#decline-btn").hasClass('cancel-event')){
        toastr.info("Add reason why cancel this appointment.");

      } else {
        toastr.info("Add reason why decline this appointment.");
      }
    } else{
      if($("#decline-note").val().trim().length == 0){
        toastr.error("Please add note!")
      } else {
        $.ajax({
          url: 'ajax.php',
          type: 'POST',
        beforeSend: function(){
            $("#pre-loader").css("display","flex");
        },
          data: {
            note: $("#decline-note").val(),
            event_id: $("#view-event-id").val()
          },
          success: function(res){
            const data = JSON.parse(res);
            if(data.response == 'ok'){
              if($("#decline-btn").hasClass('cancel-event')){

                location.href="appointments.php?cancelled";
              } else {

                location.href="appointments.php?declined";
              }
            } else {
              toastr.error('An error occurred!')
            }

                $("#pre-loader").css("display","none");
          }
        });
      }
    }
  }

  function cancelEvent(){
    if($("#cancel-note-div").is(':hidden')) {
      $("#cancel-note-div").css("display", "block");
      toastr.info("Add reason why cancel this event.");
    } else{
      if($("#cancel-note").val().trim().length == 0){
        toastr.error("Please add note!")
      } else {
        $.ajax({
          url: 'ajax.php',
          type: 'POST',
            beforeSend: function(){
                $("#pre-loader").css("display","flex");
            },
          data: {
            note: $("#cancel-note").val(),
            event_id: $('#editEvent #id').val(),
            cancel: 'cancel'
          },
          success: function(res){
            const data = JSON.parse(res);
            if(data.response == 'ok'){
              location.href="appointments.php?cancelled";
            } else {
              toastr.error('An error occurred!')
            }

                $("#pre-loader").css("display","none");
          }
        });
      }
    }

  }

  function viewEvent(id){

    $("#event-color-div").css("display","flex");
    $("#decline-btn").text("Decline");
    $("#decline-btn").removeClass("cancel-event");
    $("#event-submit-btn").attr("name","confirmEvent");
    $("#event-submit-btn").attr("value","Confirm");
    $("#event-submit-btn").attr("type","submit");
    $("#event-submit-btn").removeAttr("onclick");

    $("#decline-note-div").css("display", "none");
    $("#appt-payment-div").css("display","none");
    $("#decline-note").val("");
    $("#view-event-id").val($("#event-id-"+id).val());
    $("#view-event-code").text($("#event-appt_code-"+id).val());
    $("#view-event-code-input").val($("#event-appt_code-"+id).val());
    $("#view-event-title").text($("#event-title-"+id).val());
    $("#view-event-description").val($("#event-description-"+id).val());
    $("#view-event-customer").text($("#event-name-"+id).val());

    $("#viewEvent").modal().show();
  }

  function addPayment() {

    var id =  $("#view-event-id").val();
    $("#decline-note-div").css("display","none");

    if($("#appt-payment-div").is(':hidden')) {

      $("#appt-payment-div").css("display","flex");
      toastr.info("Please enter total bill.");

    } else{
      if($("#appt-payment-bill").val().trim().length == 0){
        toastr.error("Please enter total bill!")
      } else {
        $.ajax({
          url: 'ajax.php',
          type: 'POST',
        beforeSend: function(){
            $("#pre-loader").css("display","flex");
        },
          data: {
            bill: $("#appt-payment-bill").val(),
            id: id
          },
          success: function(res){
            const data = JSON.parse(res);
            if(data.response == 'ok'){
              location.href="appointments.php?done";
            } else {
              toastr.error('An error occurred!')
            }
            
                $("#pre-loader").css("display","none");
          }
        });
      }
    }


  }

  function eventDetails(id){

    $("#event-color-div").css("display","none");
    $("#decline-btn").addClass("cancel-event");
    $("#decline-btn").text("Cancel Event");
    $("#event-submit-btn").attr("name","doneEvent");
    $("#event-submit-btn").attr("value","Mark as Done");
    $("#event-submit-btn").attr("type","button");
    $("#event-submit-btn").attr("onclick","addPayment()");


    $("#decline-note-div").css("display", "none");
    $("#appt-payment-div").css("display","none");
    $("#decline-note").val("");
    $("#view-event-id").val($("#event-details-id-"+id).val());
    $("#view-event-code").text($("#event-details-appt_code-"+id).val());
    $("#view-event-title").text($("#event-details-title-"+id).val());
    $("#view-event-description").val($("#event-details-description-"+id).val());
    $("#view-event-customer").text($("#event-details-name-"+id).val());

    $("#viewEvent").modal().show();
  }

  $(document).ready(function(){
    var urlHashVal = window.location.hash;
    if(urlHashVal=='#schedules'){
      $('#calendar-view').val('listDay');
      $('#calendar-view').trigger('input');
    }

    $("#sched-today-tbl").DataTable({
       "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
       order: [[2, 'desc']],
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "oLanguage": {
       "oPaginate": {
         "sPrevious": "«",
         "sNext": "»"
       }
     },
     "pageLength": 6,
      "language": {
      "emptyTable": "No appointments left to attend today. Good job!"
    }
   });

    $("#appt-req-tbl").DataTable({
       "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
       order: [[2, 'desc']],
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "oLanguage": {
       "oPaginate": {
         "sPrevious": "«",
         "sNext": "»"
       }
     },
     "pageLength": 6, 
     "language": {
      "emptyTable": "No requests received as of the moment"
    }
   });

  $("#sched-history-tbl").DataTable({
       "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
       order: [[2, 'desc']],
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "oLanguage": {
       "oPaginate": {
         "sPrevious": "«",
         "sNext": "»"
       }
     },
      "language": {
      "emptyTable": "No records found!"
    },
    "pageLength": 5
   });


  });

</script>

<script type="text/javascript">

    $("[data-toggle=popover]").popover();

      $('[data-toggle=popover]').on('click', function (e) {
        $('[data-toggle=popover]').not(this).popover('hide');
    });

</script>

<?php require_once './footer.php'; ?>
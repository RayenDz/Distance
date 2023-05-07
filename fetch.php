<!DOCTYPE html>
<html>
 <head>
  <title>How to Search Table Data by Typehead with PHP Ajax</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
 </head>
 <body>
  <br /><br />
  <div class="container">
   <h2 align="center">How to Search Table Data by Typehead with PHP Ajax</h2>
   <br /><br />
   <label>Search Employee Details</label>
   <div id="search_area">
    <input type="text" name="employee_search" id="employee_search" class="form-control input-lg" autocomplete="off" placeholder="Type Employee Name" />
   </div>
   <br />
   <br />
   <div id="employee_data"></div>
  </div>
 </body>
</html>

<script>
$(document).ready(function(){
 
 load_data('');
 
 function load_data(query, typehead_search = 'yes')
 {
  $.ajax({
   url:"fetch.php",
   method:"POST",
   data:{query:query, typehead_search:typehead_search},
   success:function(data)
   {
    $('#employee_data').html(data);
   }
  });
 }
 
 $('#employee_search').typeahead({
  source: function(query, result){
   $.ajax({
    url:"fetch.php",
    method:"POST",
    data:{query:query},
    dataType:"json",
    success:function(data){
     result($.map(data, function(item){
      return item;
     }));
     load_data(query, 'yes');
    }
   });
  }
 });
 
 $(document).on('click', 'li', function(){
  var query = $(this).text();
  load_data(query);
 });
 
});
</script>
<?php
//fetch.php
if(isset($_POST["query"]))
{
 $connect = mysqli_connect("localhost", "root", "", "testing");
 $request = mysqli_real_escape_string($connect, $_POST["query"]);
 $query = "
  SELECT * FROM tbl_employee 
  WHERE name LIKE '%".$request."%' 
  OR gender LIKE '%".$request."%' 
  OR designation LIKE '%".$request."%'
 ";
 $result = mysqli_query($connect, $query);
 $data =array();
 $html = '';
 $html .= '
  <table class="table table-bordered table-striped">
   <tr>
    <th>Name</th>
    <th>Gender</th>
    <th>Designation</th>
   </tr>
  ';
 if(mysqli_num_rows($result) > 0)
 {
  while($row = mysqli_fetch_array($result))
  {
   $data[] = $row["name"];
   $data[] = $row["gender"];
   $data[] = $row["designation"];
   $html .= '
   <tr>
    <td>'.$row["name"].'</td>
    <td>'.$row["gender"].'</td>
    <td>'.$row["designation"].'</td>
   </tr>
   ';
  }
 }
 else
 {
  $data = 'No Data Found';
  $html .= '
   <tr>
    <td colspan="3">No Data Found</td>
   </tr>
   ';
 }
 $html .= '</table>';
 if(isset($_POST['typehead_search']))
 {
  echo $html;
 }
 else
 {
  $data = array_unique($data);
  echo json_encode($data);
 }
}

?>

  $(document).ready(function() {
    var countCheck = document.getElementById("countCheck").innerHTML;
    if(countCheck > 0)
    {
      for (let i = 0; i < countCheck; i++) {
        var consignee_city_to_tpl=document.getElementById('consignee_city_to_tpl_'+i).value;
        var shipper_city=document.getElementById('shipper_city_'+i).value;      
        if(shipper_city==consignee_city_to_tpl){
          var delivery_type=document.getElementById('delivery_type_'+i).value;
            $("#delivery_type_"+i+" option[value='1']").attr("disabled",false);
          }else{
            $("#delivery_type_"+i+" option[value='1']").attr("disabled",true );
          $("#delivery_type_"+i+" option[value='1']").prop('selected', false);
          }
        }
    }

    

    $('#shipper_city').on('change', function() {
      var consignee_city_to_tpl=document.getElementById('consignee_city_to_tpl').value;
      if(this.value==consignee_city_to_tpl){
        $("#delivery_type option[value='1']").attr("disabled",false);
      }else{
        $("#delivery_type option[value='1']").attr("disabled","true" );
        $("#delivery_type option[value='1']").prop('selected', false);
      }
    });
    $('#dtBasicExample').DataTable({
          pageLength : 10,
    });
    $('.dataTables_length').addClass('bs-select');
    toastr.options = {
          'closeButton': true,
          'debug': false,
          'newestOnTop': false,
          'progressBar': false,
          'positionClass': 'toast-top-right',
          'preventDuplicates': false,
          'showDuration': '2000',
          'hideDuration': '2000',
          'timeOut': '5000',
          'extendedTimeOut': '2000',
          'showEasing': 'swing',
          'hideEasing': 'linear',
          'showMethod': 'fadeIn',
          'hideMethod': 'fadeOut',
      }
  });
  function changeCityConsignee(key) {
    var consignee_city_to_tpl=document.getElementById('consignee_city_to_tpl_'+key).value;
    var shipper_city=document.getElementById('shipper_city_'+key).value;
    var delivery_type=document.getElementById('delivery_type_'+key).value;
      if(consignee_city_to_tpl==shipper_city){
        $("#delivery_type_"+key+" option[value='1']").attr("disabled",false);
      }else{
        $("#delivery_type_"+key+" option[value='1']").attr("disabled",true );
        $("#delivery_type_"+key+" option[value='1']").prop('selected', false);
      }
  }
  function cancel_shipment($id){
    document.getElementById('pageloader').style.display = 'block';   
    var order_id=$id;
      $.ajax({
        url: "/cancel_shipment",
        type:"POST",
        data: {
          'order_id':order_id,
        },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        success: function (response) {
          console.log(response);  
          document.getElementById('pageloader').style.display = 'none';
          document.getElementById('my_id_'+order_id+'').style.display='none';          
          toastr.success('Order Shipment Cancel');
        }
      });
  }
  function default_setting(){
    var loader = document.getElementById('pageloader').style.display = 'block';
    var shipper_city_id=document.getElementById('default_shipper_city_id').value;
    var shipper_address_id=document.getElementById('default_shipper_address_id').value;
    var default_shipper_delivery_id=document.getElementById('default_shipper_delivery_id').value;
    var url = "{{ URL::to('/default_setting') }}";
    $.ajax({
      url: "/default_setting",
      type:"POST",
      data: {
        'shipper_city_id':shipper_city_id,
        'shipper_address_id':shipper_address_id,
        'default_shipper_delivery_id':default_shipper_delivery_id,
      },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success: function (response) {
        window.location.reload(); 
        setTimeout("$('ul.resp-tabs-list li:nth-child(2)').trigger('click');",1 );
        var loader = document.getElementById('pageloader').style.display = 'none';
        toastr.success('Default Setting Save');
      }
    });
  }

  function login(){
    var loader = document.getElementById('pageloader').style.display = 'block';
    var login_id=document.getElementById('login_id').value;
    var enabled=document.getElementById('enabled').value;
    var api_key=document.getElementById('api_key').value;
    var url = "{{ URL::to('/login_save') }}";
    $.ajax({
      url: "/login_save",
      type:"POST",
      data: {
        'login_id':login_id,
        'api_key':api_key,
        'enabled':enabled,
      },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      success: function (response,xhr,e) {
            window.location.reload(); 
            var loader = document.getElementById('pageloader').style.display = 'none';
            if(response == "Connection Established" && e.status == 200) {
              console.log(e.status);
              toastr.success(response);
            }
            else {
              toastr.error(response);
            }
      }
    });
  }

  function hit_me()
  {
      
    var consigneeNumber=document.getElementById('consignee_number').value;
    var url = "{{ URL::to('/orders_post') }}";
    var order_id=[];
    var order_number=[];
    var order_array = document.getElementsByName('order_id[]');
    var order_number_array = document.getElementsByName('order_number[]');
    for (var i = 0; i < order_array.length; i++) {
      var a = order_array[i];
      order_id.push(a.value)
    }
    for (var i = 0; i < order_number_array.length; i++) {
      var a = order_number_array[i];
      order_number.push(a.value)
    }
    var consignee_name_set=[];
    var consignee_name_array = document.getElementsByName('consignee_name[]');
    for (var i = 0; i < consignee_name_array.length; i++) {
      var consignee_name = consignee_name_array[i];
      if(consignee_name.value==''){

        document.getElementById('pageloader').style.display = 'none';  
        toastr.error('Consignee Name Required');
        return false;
      }else{
        consignee_name_set.push(consignee_name.value)
      }
    }
    var consignee_number_set=[];
    var consignee_number_array = document.getElementsByName('consignee_number[]');
    for (var i = 0; i < consignee_number_array.length; i++) {
      var consignee_number = consignee_number_array[i];
      if(consignee_number.value.length<=9){
        toastr.error('Consignee Number Should be greater then 10');
        return false;
      }
      if(consignee_number.value==''){
        document.getElementById('pageloader').style.display = 'none';  
        toastr.error('Consignee Number Required');
        return false;
      }else{
        consignee_number_set.push(consignee_number.value)
      }
    }
    var consignee_address_set=[];
    var consignee_address_array = document.getElementsByName('consignee_address[]');
    for (var i = 0; i < consignee_address_array.length; i++) {
      var consignee_address = consignee_address_array[i];
      if(consignee_address.value==''){
        document.getElementById('pageloader').style.display = 'none';  
        toastr.error('Consignee Address Required');
        return false;
      }else{
        consignee_address_set.push(consignee_address.value)
      }
    }
    var consignee_city_order_set=[];
    var consignee_city_order_array = document.getElementsByName('consignee_city_order[]');
    for (var i = 0; i < consignee_city_order_array.length; i++) {
      var consignee_city_order = consignee_city_order_array[i];
      consignee_city_order_set.push(consignee_city_order.value)
    }
    var consignee_city_to_tpl_set=[];
    var consignee_city_to_tpl_array = document.getElementsByName('consignee_city_to_tpl[]');
    for (var i = 0; i < consignee_city_to_tpl_array.length; i++) {
      var consignee_city_to_tpl = consignee_city_to_tpl_array[i];
      if(consignee_city_to_tpl.value==''){
        document.getElementById('pageloader').style.display = 'none';  
        toastr.error('Consignee City To TPL Required');
        return false;
      }else{
        consignee_city_to_tpl_set.push(consignee_city_to_tpl.value)
      }
    }
    var shipper_city_set=[];
    var shipper_city_array = document.getElementsByName('shipper_city[]');
    for (var i = 0; i < shipper_city_array.length; i++) {
      var shipper_city = shipper_city_array[i];
      if(shipper_city.value==''){
        document.getElementById('pageloader').style.display = 'none';  
        toastr.error('Shipper City Required');
        return false;
      }else{
        shipper_city_set.push(shipper_city.value)
      }
    }
    var shipper_address_set=[];
    var shipper_address_array = document.getElementsByName('shipper_address[]');
    for (var i = 0; i < shipper_address_array.length; i++) {
      var shipper_address = shipper_address_array[i];
      if(shipper_address.value==''){
        document.getElementById('pageloader').style.display = 'none';  
        toastr.error('Shipper Address Required');
        return false;
      }else{
        shipper_address_set.push(shipper_address.value)
      }
    }
    var service_type_set=[];
    var service_type_array = document.getElementsByName('service_type[]');
    for (var i = 0; i < service_type_array.length; i++) {
      var service_type = service_type_array[i];
      service_type_set.push(service_type.value)
    }
    var delivery_type_set=[];
    var delivery_type_array = document.getElementsByName('delivery_type[]');
    for (var i = 0; i < delivery_type_array.length; i++) {
      var delivery_type = delivery_type_array[i];
      delivery_type_set.push(delivery_type.value)
    }
    var cod_amount_set=[];
    var cod_amount_array = document.getElementsByName('cod_amount[]');
    for (var i = 0; i < cod_amount_array.length; i++) {
      var cod_amount = cod_amount_array[i];
      cod_amount_set.push(cod_amount.value)
    }
    var pieces_set=[];
    var pieces_array = document.getElementsByName('pieces[]');
    for (var i = 0; i < pieces_array.length; i++) {
      var pieces = pieces_array[i];
      if(pieces.value==''){
        document.getElementById('pageloader').style.display = 'none';  
        toastr.error('Pieces Required');
        return false;
      }else{
        pieces_set.push(pieces.value)
      }
    }
    var weight_set=[];
    var weight_array = document.getElementsByName('weight[]');
    for (var i = 0; i < weight_array.length; i++) {
      if( delivery_type_array[i].value == 5){
        if(weight_array[i].value < 3){
          toastr.error("Order Number:"+order_number_array[i].value + ' Weight must be greater than 3 for delivery type detain');
          return false;
        }else{
          var weight = weight_array[i];
          weight_set.push(weight.value)
        }
      }else{
        var weight = weight_array[i];
        weight_set.push(weight.value)
      }
    }
    var description_set=[];
    var description_array = document.getElementsByName('description[]');
    for (var i = 0; i < description_array.length; i++) {
      var description = description_array[i];
      description_set.push(description.value)
    }
    var remarks_set=[];
    var remarks_array = document.getElementsByName('remarks[]');
    for (var i = 0; i < remarks_array.length; i++) {
      var remarks = remarks_array[i];
      remarks_set.push(remarks.value)
    }
    var loader = document.getElementById('pageloader').style.display = 'block'; 
      $.ajax({
        url: "/orders_post",
        type:"POST",
        data: {
          'order_id':order_id,
          'order_number':order_number,
          'consignee_name':consignee_name_set,
          'consignee_number':consignee_number_set,
          'consignee_address':consignee_address_set,
          'consignee_city_order':consignee_city_order_set,
          'consignee_city_to_tpl':consignee_city_to_tpl_set,
          'shipper_city':shipper_city_set,
          'shipper_address':shipper_address_set,
          'service_type':service_type_set,
          'delivery_type':delivery_type_set,
          'cod_amount':cod_amount_set,
          'pieces':pieces_set,
          'weight':weight_set,
          'description':description_set,
          'remarks':remarks_set,
        },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        success: function (response) {
            document.getElementById("hit_me").style.display='none';        
              document.getElementById('pageloader').style.display = 'none';   
              toastr.success('Order Booked on Rider');
              var cnNumber=[];
              var k=0;
              var i=1;
               console.log(response);
              response.forEach(myFunction);
              function myFunction(item, index) {
                if(item.statuscode=='200')
                {
                  //console.log(item.orderId);
                  //window.location.reload();
                  k++;
                  var appendme=item.CNUM;
                    var data='<a href="http://track.withrider.com/#/track/'+appendme+'" target="_blank" id="cn_number_styling"  >'+appendme+'</a>';
                  cnNumber.push(appendme);
                  document.getElementById('consignee_number_header').style.display='block';
                  document.getElementById('cn_number_'+item.orderNumber).style.display='block';
                  document.getElementById('cn_number_'+item.orderNumber).innerHTML=data;
                  
                  $("#allOrderTableAjax").append('<tr id="my_id_'+item.orderNumber+'"><td><input type="checkbox" class="sub_chk" data-id="'+appendme+'"></td><td>'+i+'</td><td>'+item.orderId+'</td><td>'+item.orderConsigneeName+'</td><td>'+item.orderConsigneeNumber+'</td><td>'+item.orderConsigneeCity+'</td><td>'+item.orderCodAmount+'</td><td>'+appendme+'</td><td style="width: 10px;" ><div class="dropdown dropleft"> <button type="button" class="actionIcon" data-toggle="dropdown"> </button> <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"> <a href="http://api.withrider.com/airwaybill?cn='+appendme+'&loginId='+item.LoginId+'&apikey='+item.apiKey+'" class="dropdown-item" style="color: black;" target="_blank">Print Waybill</a> <a href="http://track.withrider.com/#/track/'+appendme+'" class="dropdown-item" target="_blank" style="color: black;">Track Shipment</a> <a onclick="cancel_shipment('+item.orderNumber+')" class="dropdown-item mydata" style="color:black;"   >Cancel Shipment </a> </div></td></tr>');
                  $("#removeOrder"+i).prop("disabled", true);
                  Forrefreshpage();
                  i++;
                }else{
                  document.getElementById("hit_me").style.display='block';
                  document.getElementById('consignee_number_header').style.display='block';
                  document.getElementById('cn_number_'+item.orderNumber).style.display='block';
                  document.getElementById('cn_number_'+item.orderNumber).innerHTML='<p>'+item.message+'</p>';
                }
              
              }
        },
        dataType: 'json',
        error: function (response) {
        }
    });
  }

  function Forrefreshpage(){
    $('td :checkbox').bind('change click', function () {
            $(this).closest('tr').toggleClass('highlight', this.checked);
      }).change();
    $('#master').on('click', function(e) {
       if($(this).is(':checked',true))  {
          $(".sub_chk").prop('checked', true);
          $('td :checkbox').bind('change click', function () {
            $(this).closest('tr').toggleClass('highlight', this.checked);
      }).change();
       } else{  
          $(".sub_chk").prop('checked',false);
          $('td :checkbox').bind('change click', function () {
            $(this).closest('tr').toggleClass('highlight', this.checked);
      }).change();
       }
      });
    $('#print_all').on('change', function(e) {
      var printCheck =  $('#print_all').val();
        if(printCheck == "printWaybill" ){
          var allVals = [];
              $(".sub_chk:checked").each(function() {  
                  const orderIds = $(this).attr('data-id');
                  allVals.push(orderIds);
              });  
                    var cc=`http://api.withrider.com/airwaybill?cn=${allVals}&loginId=@if($already_exist){{$already_exist->login_id}}@endif&apikey=@if($already_exist){{$already_exist->api_key}}@endif`;
              if(allVals.length <=0)  
              {  
                  alert("Please select order row.");  
              }  else {  
                  var check = confirm("Are you sure you want to Print the all waybil?");  
                  if(check == true){
                      window.open(cc);
                  }  
              } 
        }
      });
    }
  
  function description_change(order_id){
    var data=document.getElementById('descriptionShow_'+order_id).value;
    if(data=='show'){
        document.getElementById('description_'+order_id).innerHTML= document.getElementById('hidden_text_'+order_id).value;
    }else{
        document.getElementById('description_'+order_id).innerHTML=' ';
      }
  }

	// <!--Plug-in Initialisation-->
        $(document).ready(function() {
            //Horizontal Tab
            $('#parentHorizontalTab').easyResponsiveTabs({
                type: 'default', //Types: default, vertical, accordion
                width: 'auto', //auto or any width like 600px
                fit: false, // 100% fit in a container
                tabidentify: 'hor_1', // The tab groups identifier
                activate: function(event) { // Callback function if tab is switched
                    var $tab = $(this);
                    var $info = $('#nested-tabInfo');
                    var $name = $('span', $info);
                    $name.text($tab.text());
                    $info.show();
                }
            });
            var countCheck = document.getElementById("countCheck").innerHTML;
            if(countCheck > 0)
            setTimeout("$('ul.resp-tabs-list li:nth-child(3)').trigger('click');",1 );
            else
            setTimeout("$('ul.resp-tabs-list li:nth-child(1)').trigger('click');",1 );
        });
  
    

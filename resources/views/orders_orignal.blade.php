<!DOCTYPE html>
<html lang="en">
<head>
<title>Ajax Request Example Laravel 8</title>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
  
    
</head>
<body>

@extends('shopify-app::layouts.default')
@section('scripts')
@parent
<script type="text/javascript">
var AppBridge = window['app-bridge'];
var actions = AppBridge.actions;
var TitleBar = actions.TitleBar;
var Button = actions.Button;
var Redirect = actions.Redirect;
var titleBarOptions = {
title: 'Welcome',
};
var myTitleBar = TitleBar.create(app, titleBarOptions);
</script>
@endsection
@section('content')

<!-- You are: (shop domain name) -->
<!-- <p>You are: {{ Auth::user()->name }}</p>
<p>Hello Raja</p> -->

   
         <!--  @foreach($bulk_orders_get as $item)
        @foreach($item as $key=>$sub)
            <div>
                <Label>Id</Label>
                <input type="text" value="{{$sub['id']}}">
            </div> 
            @endforeach
        @endforeach  -->


        <input type="text" name="login_id" id="login_id" value="@if($already_exist){{$already_exist->login_id}} @endif">
        <input type="text" name="api_key" id="api_key" value="@if($already_exist) {{$already_exist->api_key}} @endif">
        @if(!$already_exist)
        <input type="submit" onclick="login()">
        @endif
         <br>
 
     <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">S:No</th>
              <th scope="col">Order Id</th>
              <th scope="col">Consignee Name <span style="color:red">*</span></th>
              <th scope="col">Consignee Number<span style="color:red">*</span></th>
              <th scope="col">Consignee Address<span style="color:red">*</span></th>
              <th scope="col">Consignee City in Order</th>
              <th scope="col">Consignee City to send TPL<span style="color:red">*</span></th>
              <th scope="col">Shipper City<span style="color:red">*</span></th>
              <th scope="col">Shipper Address<span style="color:red">*</span></th>
              <th scope="col">Service Type</th>
              <th scope="col">Delivery Type</th>
              <th scope="col">COD Amount</th>
              <th scope="col">Pieces<span style="color:red">*</span></th>
              <th scope="col">Weight</th>
              <th scope="col">Description</th>
              <th scope="col">Description Show/Hide</th>
              <th scope="col">Remarks</th>
            </tr>
          </thead>
          <tbody>
              <?php $i=1;$j=0;?>
              @foreach($bulk_orders_get as $item)
              @foreach($item as $sub)
              <tr>
                  <td>{{$i++}}</td>
                  <td><input name="order_id[]" disabled="true"  type="number" value="{{$sub['id']}}"/></td>
                  <td><input name="consignee_name[]" id="consignee_name" type="text" value="{{$sub['customer']['first_name']}} {{$sub['customer']['last_name']}}"/></td> 
                  <td><input name="consignee_number[]" type="text" value="{{$sub['customer']['phone']}}" id="consignee_number"/></td>
                  <td><textarea name="consignee_address[]" id="consignee_address">{{$sub['customer']['default_address']['address1']}}</textarea></td>
                  <td><input  disabled="true" type="text" value="{{$sub['customer']['default_address']['city']}}"/></td>
                  <td>
                    <select name="consignee_city_to_tpl[]" id="consignee_city_to_tpl">
                      <option disabled="true" value='' >Select City</option>
                      @if($cities)
                      @foreach($cities as $item)
                      <option <?php if(strtolower($sub['customer']['default_address']['city']) == strtolower($item->description)){ echo 'selected'; } ?> value="<?php echo $item->id; ?>">{{$item->description}}</option>
                      @endforeach
                      @endif
                    </select>
                  </td>

                  <td>
                    <select name="shipper_city[]" id="shipper_city" >
                      <option disabled="true"  value='' >Select City</option>
                      @if($cities)
                      @foreach($cities as $item)
                      <option <?php if(strtolower($sub['customer']['default_address']['city']) == strtolower($item->description)){ echo 'selected'; } ?> value="<?php echo $item->id; ?>">{{$item->description}}</option>
                      @endforeach
                      @endif

                    </select>
                  </td>
                  @csrf
                  
                  <td>
                    <select name="shipper_address[]" >
                      <option disabled="true" value='' >Select Shipper Address</option>
                      @if($shipper_location)
                      @foreach($shipper_location as $item)
                      <option value="{{$item->id}}">{{$item->address}}</option>
                      @endforeach
                      @endif
                    </select>
                  </td>

                  <td>
                    <select name="service_type[]">
                      <option @if($sub['total_price']>0) selected  @endif  value="1">COD</option>
                      <option @if($sub['total_price']<=0) selected  @endif value="2">Non COD</option>
                    </select>
                  </td>
                  
                  <td>
                    <select name="delivery_type[]" required>
                        <!-- <option selected disabled="true">Select Delivery Type</option> -->
                        @if($delivery_type)
                        @foreach($delivery_type as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                        @endforeach
                        @endif
                    </select>
                  </td>
                  


                    <td><input name="cod_amount[]" type="text"  value="{{$sub['total_price']}}"/></td>
                    @if($sub['line_items'])
                  @foreach($sub['line_items'] as $cc)
                  <td><input name="pieces[]" type="number" min="0" value="{{$cc['quantity']}}" /></td>
                  @endforeach
                  @endif
                  <td><input name="weight[]" type="number" min="0" value="{{$sub['total_weight']}}" /></td>
                  <td>
                    <select  name="descriptionShow[]"  id="descriptionShow_<?php echo $j;?>" onchange="description_change(<?php  echo $j;  ?>)" required>
                      <!-- <option selected disabled="true" >Select Description</option> -->
                      <option value="show">Show</option>
                      <option value="hide">Hide</option>
                    </select>
                  </td>

                  
                  @if($sub['line_items'])
                  @foreach($sub['line_items'] as $cc)
                  <td>
                    <textarea name="description[]" id="description_<?php echo $j;?>">{{$cc['name']}},</textarea>
                    <input type="hidden" id="hidden_text_<?php echo $j;?>" value="{{$cc['name']}}">
                  </td>
                  @endforeach
                  @endif
                  <td><textarea name="remarks[]"></textarea></td>
              </tr>
            @endforeach
            <?php $j++;?>
            @endforeach

          </tbody>
        </table>
    </div>
      <br>
    <div class="container">
      <div class="row">
        <div class="col-md-4">
            <span style="color:red;" id="error"></span>
        </div>
        <div class="col-md-4">
        <span style="color:green;font-size:15px;" id="order_booked"></span>
        <span style="color:red;font-size:15px;" id="order_booked_error"></span>
        </div>
        <div class="col-md-4" style="text-align:right">
        
        <input type="submit" onclick="hit_me()" id="hit_me">

     </div>
      </div>
    </div> 
    
<!-- Backend Hit -->
<script type="text/javascript">


    function login(){

      var login_id=document.getElementById('login_id').value;
      var api_key=document.getElementById('api_key').value;
      var url = '{{ URL::to('/login_save') }}';

      $.ajax({
         url: "/login_save",
         type:"POST",
         data: {
           'login_id':login_id,
           'api_key':api_key,
         },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
         success: function (response) {
              console.log(response);
         }
     });
    }

    function hit_me(){

      var consigneeNumber=document.getElementById('consignee_number').value;

   
      var url = '{{ URL::to('/orders_post') }}';
      var order_id=[];
      var order_array = document.getElementsByName('order_id[]');
      for (var i = 0; i < order_array.length; i++) {
        var a = order_array[i];
        order_id.push(a.value)
      }


      var consignee_name_set=[];
      var consignee_name_array = document.getElementsByName('consignee_name[]');
      for (var i = 0; i < consignee_name_array.length; i++) {
        var consignee_name = consignee_name_array[i];
        if(consignee_name.value==''){
          document.getElementById('error').innerHTML='* fields are required';
          return false;
        }else{
          consignee_name_set.push(consignee_name.value)
        }
      }

      var consignee_number_set=[];
      var consignee_number_array = document.getElementsByName('consignee_number[]');
      for (var i = 0; i < consignee_number_array.length; i++) {
        var consignee_number = consignee_number_array[i];
        if(consignee_number.value==''){
          document.getElementById('error').innerHTML='* fields are required';
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
          document.getElementById('error').innerHTML='* fields are required';
          return false;
        }else{
          consignee_address_set.push(consignee_address.value)
        }
      }


      var consignee_city_to_tpl_set=[];
      var consignee_city_to_tpl_array = document.getElementsByName('consignee_city_to_tpl[]');
      for (var i = 0; i < consignee_city_to_tpl_array.length; i++) {
        var consignee_city_to_tpl = consignee_city_to_tpl_array[i];
        if(consignee_city_to_tpl.value==''){
          document.getElementById('error').innerHTML='* fields are required';
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
          document.getElementById('error').innerHTML='* fields are required';
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
          document.getElementById('error').innerHTML='* fields are required';
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
          document.getElementById('error').innerHTML='* fields are required';
          return false;
        }else{
          pieces_set.push(pieces.value)
        }
      }

      var weight_set=[];
      var weight_array = document.getElementsByName('weight[]');
      for (var i = 0; i < weight_array.length; i++) {
        var weight = weight_array[i];
        weight_set.push(weight.value)
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
        
      $.ajax({
         url: "/orders_post",
         type:"POST",
         data: {
           'order_id':order_id,
           'consignee_name':consignee_name_set,
           'consignee_number':consignee_number_set,
           'consignee_address':consignee_address_set,
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
      
              // var data=response['0'];
              // console.log('status code is',data['statuscode']);

              console.log(response);
              response.forEach(myFunction);
              function myFunction(item, index) {
                if(item.statuscode=='200')
                {
                  document.getElementById("order_booked").innerHTML='Order Booked On TPL';  
                  document.getElementById("hit_me").style.display='none';  
                }else{
                  document.getElementById("order_booked_error").innerHTML='Some Error Occurs';  
                  document.getElementById("hit_me").style.display='block';  
                }
              }


         },
         dataType: 'json',
         error: function (response) {
          console.log(response);
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
</script>
@endsection
    </body>
</html>
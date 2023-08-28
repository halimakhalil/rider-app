<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/easy-responsive-tabs@0.0.2/js/easyResponsiveTabs.js"></script>
    <link rel="stylesheet" href="{{ asset('css/easy-responsive-tabs.css') }}" class="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
 
    <style>
      .dropdown-menu a:hover{
        background:#5ab1d0;
      }
      .highlight td{
      background-color: #a2c4ab;
      }
        #pageloader{
      background: rgba( 255, 255, 255, 0.8 );
          display:none;
      height: 100%;
      position: fixed;
      width: 100%;
      z-index: 9999;
      }
      #pageloader img{
          position: absolute;
          top: 36%;
          left: 44%;
          transform: translate(-50%, -50%);
      }
      .actionIcon:after {
        content: '\2807';
        }
      .table-wrapper {
          background: #fff;
          padding: 20px;
          box-shadow: 0 1px 1px rgba(0,0,0,.05);
      }
      .tableBooking{
          border-collapse: collapse;
          width: 100%;
          display: block;
          overflow-x: auto;
          white-space: nowrap;
      }
      .table-title {
          font-size: 15px;
          padding-bottom: 10px;
          margin: 0 0 10px;
          min-height: 45px;
      }
      .table-title h2 {
          margin: 5px 0 0;
          font-size: 24px;
      }
      .table-title select {
          border-color: rgb(104, 104, 104);
          border-width: 0 0 1px 0;
          padding: 3px 10px 3px 5px;
          margin: 0 5px;
      }
      .table-title .show-entries {
          margin-top: 7px;
      }
      table.table tr th, table.table tr td {
          border-color: #e9e9e9;
      }
      table.table th i {
          font-size: 13px;
          margin: 0 5px;
          cursor: pointer;
      }
      table.table td:last-child {
          width: 130px;
      }
      table.table td a {
          color: #a0a5b1;
          display: inline-block;
          margin: 0 5px;
      }
      table.table td a.view {
          color: #03A9F4;
      }
      table.table td a.edit {
          color: #FFC107;
      }
      table.table td a.delete {
          color: #E34724;
      }
      table.table td i {
          font-size: 19px;
      }
      #consignee_number_header{
          display:none;
      } 
      .toast-success{
        font-size:15px;
      }
      .toast-error{
        font-size:15px;
      }      
      #cn_number_styling{
        color: blue;
        text-decoration: underline;
      }
      .form-control{
        width: -moz-fit-content; 
      }
      </style>
</head>
<body>
@extends('shopify-app::layouts.default')
@section('script')
    @parent
        <script>
            var AppBridge = window['app-birdge'];
            var actions =  AppBridge.actions;
            var TitleBar = actions.TitleBar;
            var Button = actions.Button
            var Redirect = actions.Redirect;
            var titleBarOptions = {
                title:'Hello world ',
            };
            var myTitleBar = TitleBar.create(app, titleBarOptions);
        </script>
@endsection
@section('content')
    <div id="container">
      <div id="pageloader">
          <img src="{{ asset('loader.gif') }}" alt="processing..." />
      </div>
      <div id="parentHorizontalTab">
          <ul class="resp-tabs-list hor_1">
              <li>Connection setting</li>
              @if($already_exist != null)
              @if($already_exist->enabled=='1')
              <li>Default Setting</li>
              @endif
              @if(isset($_GET['ids']) || isset($_GET['id']))
              @if($already_exist->enabled=='1')
              <li>Order Booking Page</li>
              @endif
              @endif
              <li id="tabClick4" >Order Booked on Rider</li>
              @endif
          </ul>
          <div class="resp-tabs-container hor_1" id="clickTab" style="overflow-x: auto;">
              {{-- first Tab --}}
              <div>
                  <div class="row">
                      <div class="col-lg-4 col-md-4"> 
                          <div class="card">
                          <div class="card-header" >General Settings</div>
                              <div class="card-body">
                                  <form role="form">
                                      <div class="form-group">
                                          <label>Version: </label>
                                          <label><a href="http://www.Mean3.com" title="TPL Shipping" target="_blank">1.0.1</a></label>
                                      </div>

                                      <div class="form-group">
                                          <label>Enabled</label>
                                          <select class="form-control" name="enabled" id="enabled">
                                              <option @if($already_exist)@if($already_exist->enabled=='1') selected @endif @endif value="1">Yes</option>
                                              <option @if($already_exist)@if($already_exist->enabled=='0') selected @endif @endif value="0">No</option>
                                          </select>
                                      </div>
                                      <div class="form-group">
                                          <label>Login ID</label>
                                          <input type="number" name="login_id" id="login_id" value="@if($already_exist){{(int)$already_exist->login_id}}@endif" class="form-control"  placeholder="Login Id">
                                      </div>

                                      <div class="form-group">
                                          <label>Api Key</label>
                                          <div class="form-group">
                                              <input type="text" class="form-control" name="api_key" id="api_key" value="@if($already_exist) {{$already_exist->api_key}} @endif" placeholder="Api Key">
                                          </div>
                                      </div>
                                      <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                      <div class="form-group">
                                          <input type="button" class="btn btn-primary btn-block" onclick="login()" value="Save">
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                      <!-- <div class="col-lg-1 col-md-1"></div> -->
                      <div class="col-lg-7 col-md-7">
                      <div class="card">
                          <div class="card-header" >Setup Instruction</div> 
                            <div class="card-body">
                                <label>Let's get started by following guide</label>
                                <ul>
                                  <li>Please provide a valid Login ID and API key. If you don't have one please contact Rider support. customerservice@withrider.com.</li>
                                  <li>After providing valid Login ID and API key head towards Orders tab on the top left of the screen.</li>
                                  <li>Select single order and click Booked Through Rider button or select multiple orders and click Booked Through Rider button to book orders.</li>
                                  <li>After successful booking orders move towards Order Booked on Rider tab for Print, Track, and Cancel Shipment.</li>
                                </ul> 
                            </div>
                          </div>       
                      </div>
                  </div>
              </div>
              @if(isset($already_exist))
              @if($already_exist->enabled=='1')
              {{-- Second Tab --}}
              <div>
                  <div class="row">
                      <div class="col-lg-4 col-md-4"> 
                          <div class="card">
                          <div class="card-header" >Default Setting</div>
                              <div class="card-body">
                                <form role="form">
                                  <div class="form-group">
                                    <label>Shipper Default City</label>
                                    <select class="form-control" id="default_shipper_city_id" >
                                      @if($cities)
                                      <option disabled>Select City</option>
                                        @foreach($cities as $item)
                                          <option @if(isset($already_exist_default)) @if($item->id==$already_exist_default->shipper_city_id) selected @endif @endif value="<?php echo $item->id; ?>">{{$item->description}}</option>
                                        @endforeach
                                      @endif
                                    </select>
                                  </div>

                                      <div class="form-group">
                                          <label>Shipper Default Address</label>
                                          <select class="form-control" id="default_shipper_address_id" >
                                              <option disabled>Select Address</option>
                                              @if($shipper_location)
                                              @foreach($shipper_location as $item)
                                              <option @if(isset($already_exist_default))  @if($item->id==$already_exist_default->shipper_address_id) selected @endif @endif value="{{$item->id}}">{{$item->address}}</option>
                                              @endforeach
                                              @endif
                                          </select>
                                      </div>

                                      <div class="form-group">
                                          <label>Shipper Delivery Type	</label>
                                          <select class="form-control"  id="default_shipper_delivery_id">
                                            <option disabled>Select Delivery Type</option>
                                            @if($delivery_type)
                                            @foreach($delivery_type as $item)
                                            <option @if(isset($already_exist_default)) @if($item->id==$already_exist_default->default_shipper_delivery_id) selected @endif @endif value="{{$item->id}}">{{$item->title}}</option>
                                            @endforeach
                                            @endif
                                          </select>
                                      </div>
                                      <div class="form-group">
                                          <input type="button"  class="btn btn-primary btn-block" onclick="default_setting()" value="Save">
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              {{-- Third Tab --}}
              @if($already_exist !=null && isset($_GET['ids']) || isset($_GET['id']))
              <div>
                  <div class="table-wrapper">			
                      <div class="table-title">
                          <div class="row">
                              <div class="col-lg-12 col-md-12 col-sm-12">
                                  <h2 class="text-center">Order Booking Page</h2>
                              </div>
                          </div>
                      </div>
                      
                      <table id="riderTableBooking" class="table table-bordered tableBooking" cellspacing="0" width="100%">
                          <thead>
                              <tr>
                                <th scope="col">S:No</th>
                                <th scope="col" id="consignee_number_header">C N: No</th>
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
                                <th scope="col">Remove</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1;$j=0;$k=0;$m=1;?>
                              <h1 id="countCheck" style="display:none;">{{ count($bulk_orders_get) }}</h1>
                                @foreach($bulk_orders_get as $key=> $item)
                                  @foreach($item as  $sub)
                                  {{ info($sub)}}
                                  <span style="display:none">{{$k++}}</span>
                                    <tr data-id="{{$sub['id']}}" id="{{$i}}">
                                        <td>{{$i}}</td>
                                        <td id="cn_number_<?php echo $sub['id'] ?>" style="display:none" ></td>
                                        <td>
                                          <div class="form-group">
                                            <input name="order_number[]" class="form-control" disabled="true"  type="text" style="width:fit-content !important;" value="{{$sub['name']}}"/>
                                            <input name="order_id[]" class="form-control" disabled="true"  type="hidden" style="width:fit-content !important;" value="{{$sub['id']}}"/>
                                          </div>  
                                        </td>
                                        <td>
                                          <div class="form-group">
                                              <input name="consignee_name[]" class="form-control" id="consignee_name" type="text" style="width:fit-content !important;" value="@if (isset($sub['shipping_address']) && !empty($sub['shipping_address'])){{$sub['shipping_address']['first_name']}} {{$sub['shipping_address']['last_name']}}@elseif (isset($sub['billing_address']) && !empty($sub['billing_address'])){{$sub['billing_address']['first_name']}} {{$sub['billing_address']['last_name']}}@endif"/></td>
                                          </div>
                                        </td> 
                                        <td>
                                          <div class="form-group">
                                              <input name="consignee_number[]" type="text" class="form-control" style="width:fit-content !important;" value="@if(isset($sub['shipping_address'])){{$sub['shipping_address']['phone']}}@else{{$sub['billing_address']['phone']}}@endif" id="consignee_number"/>
                                          </div>
                                        </td>
                                        <td>
                                          <div class="form-group"> 
                                              <textarea name="consignee_address[]" class="form-control" style="width:fit-content !important;" id="consignee_address">@if(isset($sub['shipping_address'])){{$sub['shipping_address']['address1']}}{{$sub['shipping_address']['address2']}}@else{{$sub['billing_address']['address1']}}{{$sub['billing_address']['address2']}}@endif</textarea>
                                          </div>
                                        </td>
                                        <td>
                                          <div class="form-group">
                                              <input  disabled="true" name="consignee_city_order[]" id="{{$key}}" class="form-control" style="width:fit-content !important;" type="text" value="@if(isset($sub['shipping_address'])){{$sub['shipping_address']['city']}}@else{{$sub['billing_address']['city']}}@endif"/>
                                          </div>
                                        </td>
                                        <td>
                                          <div class="form-group">
                                              <select class="form-control" name="consignee_city_to_tpl[]" style="width:fit-content !important;" id="consignee_city_to_tpl_{{$key}}" onchange="changeCityConsignee({{$key}})" >
                                              <option value="" >Select City</option>
                                              @if($cities)
                                              @foreach($cities as $item)
                                              <option <?php if(isset($sub['shipping_address'])){if (trim(strtolower($sub['shipping_address']['city']), " ") == strtolower($item->description)){ echo 'selected';}}?> value="<?php echo $item->id; ?>">{{$item->description}}</option>
                                              @endforeach
                                              @endif
                                              </select>
                                          </div>
                                        </td>

                                        <td>
                                          <div class="form-group">
                                              <select name="shipper_city[]" id="shipper_city_{{$key}}" style="width:fit-content !important;" class="form-control" onchange="changeCityConsignee({{$key}})">
                                              <option disabled="true">Select City</option>
                                              @if($cities)
                                              @foreach($cities as $item)
                                              <option @if(isset($already_exist_default)) @if($item->id==$already_exist_default->shipper_city_id) selected @endif @endif value="<?php echo $item->id; ?>">{{$item->description}}</option>
                                              @endforeach
                                              @endif
                                              </select>
                                          </div>
                                        </td>
                                          @csrf

                                        <td>
                                          <div class="form-group">
                                              <select name="shipper_address[]" class="form-control">
                                                <option disabled="true" value='' >Select Shipper Address</option>
                                                @if($shipper_location)
                                                  @foreach($shipper_location as $item)
                                                    <option @if(isset($already_exist_default)) @if($item->id==$already_exist_default->shipper_address_id) selected @endif @endif value="{{$item->address}}">{{$item->address}}</option>
                                                  @endforeach
                                                @endif
                                              </select>
                                          </div>
                                        </td>

                                        <td>
                                          <div class="form-group">
                                              <select name="service_type[]" class="form-control" id="service_<?php echo $j;?>" onchange="codCheck(<?php  echo $j;  ?>)" style="width:fit-content !important;">
                                                <option  @if($sub['financial_status'] == 'paid') selected  @endif  value="2">Non COD</option>
                                                <option @if($sub['financial_status'] != 'paid') selected  @endif value="1">COD</option>
                                              </select>
                                          </div>
                                          <input type="hidden" id="hidden_cod_<?php echo $j;?>" value="{{$sub['current_total_price']}}">
                                        </td>

                                        <td>
                                          <div class="form-group">
                                              <select name="delivery_type[]" required class="form-control" style="width:fit-content !important;" id="delivery_type_{{$key}}">
                                                  @if($delivery_type)
                                                    @foreach($delivery_type as $item)
                                                      <option @if(isset($already_exist_default)) @if($item->id==$already_exist_default->default_shipper_delivery_id) selected @endif @endif  value="{{$item->id}}">{{$item->title}}</option>
                                                    @endforeach
                                                  @endif
                                              </select>
                                          </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input id="cod_amount_{{$j}}"  name="cod_amount[]" type="text" style="width:fit-content !important;" @if($sub['financial_status'] == 'paid') value="0" @else value="{{$sub['current_total_price']}}"@endif class="form-control" />
                                            </div>
                                        </td>
                                        <?php $my_value=0;  ?>
                                          @if($sub['line_items'])
                                        @foreach($sub['line_items'] as $cc)
                                        <?php if ($cc['fulfillable_quantity']>0) {
    $my_value+=$cc['fulfillable_quantity'];
} ?>
                                        @endforeach
                                        @endif
                                      <td>
                                          <div class="form-group">
                                              <input name="pieces[]" type="number" min="0" style="width:fit-content !important;" value="{{$my_value}}" class="form-control" />
                                          </div>
                                      </td>

                                      <td>
                                        <div class="form-group">
                                          <input name="weight[]" type="text" style="width:fit-content !important;"  value="{{$sub['total_weight']/1000}}" class="form-control"/>
                                        </div>
                                      </td>
                                        <td>
                                          <div class="form-group">
                                              <select  name="descriptionShow[]" class="form-control" style="width:fit-content !important;" id="descriptionShow_<?php echo $j;?>" onchange="description_change(<?php  echo $j;  ?>)" required>
                                              <option value="show">Show</option>
                                              <option value="hide">Hide</option>
                                              </select>
                                          </div>
                                        </td>  
                                        <td>
                                          <div class="form-group">
                                            @if($sub['line_items'])
                                              <textarea name="description[]" class="form-control" style="width:fit-content !important;" id="description_<?php echo $j;?>">@foreach($sub['line_items'] as $cc)@if($cc['fulfillable_quantity']>0){{$cc['name']}} X {{$cc['fulfillable_quantity']}}, @endif @endforeach
                                              </textarea>
                                              <input type="hidden" id="hidden_text_<?php echo $j;?>" value="@foreach($sub['line_items'] as $cc)@if($cc['fulfillable_quantity']>0){{$cc['name']}} X {{$cc['fulfillable_quantity']}} , @endif @endforeach">
                                              @endif
                                          </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <textarea class="form-control" style="width:fit-content !important;" name="remarks[]"></textarea>
                                            </div>
                                        </td>
                                        <td>
                                          <div class="form-group">
                                            <input type="button" class="btn btn-danger btn-block"  id="removeOrder" onclick="deleteRow({{$i}})" value="Remove" >
                                          </div>
                                          <?php $i++ ?>
                                      </td>
                                    </tr>
                                  @endforeach
                              <?php $j++;?>
                              @endforeach
                            </tbody>
                          </table>
                          <div class="row">
                            <div class="col-md-3">
                              <div class="form-group">
                                <input type="button" class="btn btn-primary btn-block success" onclick="hit_me()" id="hit_me" value="Submit" >
                              </div>
                            </div>
                          </div>

                          <span id="order_booked" style="color:green"></span>
                          <span id="order_booked_error" style="color:red"></span>
                          <span id="error" style="color:red"></span>
                    </div>
              </div>
              @endif
              @endif
              @endif
              {{-- Fourth Tab --}}
              <div>
                  <div class="table-wrapper">			
                      <div class="table-title">
                          <div class="row">
                              <div class="col-sm-4">
                                  <div class="show-entries">
                                      <span>Action</span>
                                      <select id="print_all">
                                          <option selected >Select Option</option>
                                          <option value="printWaybill">Print Bulk waybill</option>
                                      </select>
                                  </div>
                              </div>
                              <div class="col-sm-4">
                                  <h2 class="text-center">Order Booked on Rider</h2>
                              </div>
                              <!-- <div class="col-sm-2">
                              </div>
                              <div class="col-sm-2">
                                <input type="button" class="btn btn-info btn-block" onclick="refreshPage()" id="refreshPage" value="Refresh" >
                              </div> -->
                          </div>
                      </div>
                      <h1 id="countCheck" style="display:none;"></h1>
                      <table id="dtBasicExample" class="table table-bordered allOrderTable" cellspacing="0">
                          <thead>
                              <tr>
                                  <th><input type="checkbox" id="master"></th>
                                  <th>S:No</th>
                                  <th>Order Id</th>
                                  <th>Consignee Name</th>
                                  <th>Consignee Number</th>
                                  <th>Consignee City</th>
                                  <th>COD Amount</th>
                                  <th>CN no#</th>
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody id="allOrderTableAjax">
                            <?php $i=1;?>
                            @foreach($order_booked as $item)
                                @php
                                  $countFull = count($item['fulfillments'])-1;
                                @endphp
                              @if(isset($item['fulfillments'][$countFull]['tracking_number']))
                              <tr id="my_id_{{$item['id']}}">

                                  <td><input type="checkbox" class="sub_chk" data-id="{{$item['fulfillments'][$countFull]['tracking_number']}}"></td>
                                  <td>{{$i++}}</td>
                                  <td>{{$item['name']}}</td>
                                  <td>@if(isset($item['shipping_address'])){{$item['shipping_address']['first_name']}} {{$item['shipping_address']['last_name']}}@else{{$item['billing_address']['first_name']}} {{$item['billing_address']['last_name']}}@endif</td>
                                  <td>@if(isset($item['shipping_address'])){{$item['shipping_address']['phone']}}@else{{$item['billing_address']['phone']}}@endif</td>
                                  <td>@if(isset($item['shipping_address'])){{$item['shipping_address']['city']}}@else{{$item['billing_address']['city']}}@endif</td>
                                  <td> @if( $item['financial_status'] == 'paid' )0.00 @else {{$item['current_total_price']}}@endif</td>
                                  <td>{{$item['fulfillments'][$countFull]['tracking_number']}}</td>
                                  <td style="width: 10px;">
                                    <div class="dropdown dropleft">
                                      <button type="button" class="actionIcon" data-toggle="dropdown">
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="http://api.withrider.com/airwaybill?cn={{$item['fulfillments'][$countFull]['tracking_number']}}&loginId=@if($already_exist){{$already_exist->login_id}}@endif&apikey=@if($already_exist){{$already_exist->api_key}}@endif" class="dropdown-item" style="color: black;" target="_blank">Print Waybill</a>
                                        <a href="http://track.withrider.com/#/track/{{$item['fulfillments'][$countFull]['tracking_number']}}" class="dropdown-item" target="_blank" style="color: black;">Track Shipment</a>
                                        <a onclick="cancel_shipment({{$item['id']}})" class="dropdown-item mydata" style="color:black;"   >Cancel Shipment </a>
                                      </div>
                                    </div>
                                  </td>
                              </tr> 
                              @endif
                            @endforeach
                          </tbody>
                      </table>
              </div>
          </div>
      </div>
    </div>
    <script type="text/javascript">

      function codCheck(id){
          var data = document.getElementById('service_'+id).value;
          
          if( data == '1' ){
              document.getElementById('cod_amount_'+id).value = document.getElementById('hidden_cod_'+id).value;
          }else if( data == '2' ){
            document.getElementById('cod_amount_'+id).value = '0';
          }
      }

    function deleteRow(rowIndex) {
      document.getElementById("riderTableBooking").deleteRow(rowIndex);
    }
    function refreshPage() {
      window.location.reload();
    }

    $(document).ready(function () {
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
    });
    
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>
<!-- Backend Hit -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="js/order_script.js"></script>
  @endsection
</body>
</html>
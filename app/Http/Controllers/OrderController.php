<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
class OrderController extends Controller
{

    public function connectionCheckApi($login_id,$api_key){

        $result = array();

        $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.withrider.com/rider/v1/credentials/check',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "loginId":"'.$login_id.'",
            "apikey":"'.$api_key.'"
            }
            ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $result = json_decode($response);
            return $result;
    }


    public function login_save(Request $request){

        $login_id=$request->login_id;
        $apiKey=$request->api_key;
        $enabled=$request->enabled;
        $data=$this->connectionCheckApi($login_id,$apiKey);
        if($data->statuscode==200)
        {
            $store_user=Auth::user();
            $isalready_exist=DB::table('store_user')->where('user_id',$store_user->id)->first();
            if(isset($isalready_exist)){
                DB::table('store_user')->where('user_id',$store_user->id)->update(['enabled'=>$enabled,'user_id'=>$store_user->id,'login_id'=>$login_id,'api_key'=>$apiKey,'created_at'=>
            Carbon::now()]);
                return response()->json('Connection Established');
            }else{
                DB::table('store_user')->insert(['enabled'=>$enabled,'user_id'=>$store_user->id,'login_id'=>$login_id,'api_key'=>$apiKey]);
                return response()->json('Connection Established');
            }
        }else{
            return response()->json('Invalid Credentials');
        }
        return $data;
    }

    public function orders1(){

        $bulk_orders_get = array();
        $shop = Auth::user();

        if(isset($_GET['ids']) || isset($_GET['id'])){
            //$id= $_GET['ids'];

            $id= (isset($_GET['ids'])) ? $_GET['ids'] : $_GET['id'];
            if(!is_array($id)){
                $id = (array)$id;
            }
            $shop = Auth::user();

            $bulk_orders_get=array();
            foreach ($id as $item) {
                $ordersArray = $shop->api()->rest('GET', "/admin/api/2021-07/orders/$item.json");
                array_push($bulk_orders_get, $ordersArray['body']['container']);
            }

            $cities= json_decode($this->citiesapi());

            $shipper_location=json_decode($this->GetShipperLocations());

            $delivery_type=json_decode($this->getDeliveryTypes());

            $already_exist=DB::table('store_user')->where('user_id',$shop->id)->first();

            $already_exist_default=DB::table('store_default_setting')->where('user_id',$shop->id)->first();
            return view('orders',compact('bulk_orders_get','cities','shipper_location','delivery_type','already_exist','already_exist_default'));
        }
        else{
            $cities= json_decode($this->citiesapi());
            // dd($cities);
            $shipper_location=json_decode($this->GetShipperLocations());

            $delivery_type=json_decode($this->getDeliveryTypes());

            $already_exist=DB::table('store_user')->where('user_id',$shop->id)->first();

            $already_exist_default=DB::table('store_default_setting')->where('user_id',$shop->id)->first();
            return view('orders',compact('bulk_orders_get','cities','shipper_location','delivery_type','already_exist','already_exist_default'));
        }
    }

    public function orders(){

        $bulk_orders_get = array();
        $cities = array();
        $shipper_location = array();
        $delivery_type = array();
        $already_exist_default = array();
        $fullfillment_array = array();

        $shop = Auth::user();
        $already_exist=DB::table('store_user')->where('user_id',$shop->id)->first();

        $order_booked=array();
        // $fullfillment_orders = $shop->api()->rest('GET', "/admin/api/2021-07/products/6764437995706.json");
        //$fullfillment_orders = $shop->api()->rest('GET', "/admin/api/2021-07/orders.json",['status' => 'any']);
        $fullfillment_orders = $this->getAllOrders();

        $my_data=$fullfillment_orders['body']['container']['orders'];

        foreach($my_data as $item)
        {
            if(strpos($item['tags'], 'Book Through Rider') !== false){
                array_push($order_booked,$item);
            }
        }
        if($already_exist != null)
        {
            $cities= json_decode($this->citiesapi());
            $shipper_location=json_decode($this->GetShipperLocations());
            $delivery_type=json_decode($this->getDeliveryTypes());
            $already_exist_default=DB::table('store_default_setting')->where('user_id',$shop->id)->first();

            if(isset($_GET['ids']) || isset($_GET['id'])){

                $id= (isset($_GET['ids'])) ? $_GET['ids'] : $_GET['id'];
                if(!is_array($id)){
                    $id = (array)$id;
                }
                foreach($id as $item)
                {
                    $ordersArray = $shop->api()->rest('GET', "/admin/api/2022-01/orders/$item.json" );
                    foreach($ordersArray['body']['container'] as $item)
                    {
                        if(strpos($item['tags'], 'Book Through Rider') === false){  
                            array_push($bulk_orders_get,$ordersArray['body']['container']);
                        }
                    }
                }
            }

            return view('orders',compact('bulk_orders_get','cities','shipper_location','delivery_type','already_exist','already_exist_default','order_booked'));
        }
        else
        {
            return view('orders',compact('bulk_orders_get','cities','shipper_location','delivery_type','already_exist','already_exist_default','order_booked'));
        }
    }

    // public function getAllOrders() {
    //     $shop = Auth::user();
    //     $orderIds = DB::table('store_order')->where('user_id', $shop->id)->latest('id')->take(250)->pluck('order_id')->toArray();

    //     $orderIdForApi = implode(",",$orderIds);

    //     $allOrderHit = $shop->api()->rest('GET', "/admin/api/2021-07/orders.json",[ 'limit'=>250,'status' => 'any','ids' =>  $orderIdForApi]);

    //     return $allOrderHit;
    // }

    public function getAllOrders() {
        $shop = Auth::user();
        $merged = array();
        $page_info = '';
        $last_page = false;
        $i = 0;
        $debug = 0;

        while(!$last_page) {
            if( $i == 0 ){
                $allOrderHit = $shop->api()->rest('GET', "/admin/api/2021-07/orders.json",[ 'limit'=>250,'status' => 'any' ]);
                $i = 1;
            }
            else{
                $allOrderHit = $shop->api()->rest('GET', "/admin/api/2021-07/orders.json",[ 'limit'=>250,'page_info'=>$page_info ]);
            }

            if($allOrderHit['link'] != null) {
                $page_info =  $allOrderHit['link']['next'];
            }
            else{
                $last_page = true;
            }
            //$source_array = json_decode($allOrderHit, true);
            $merged = array_merge_recursive($merged, $allOrderHit);

            if($debug >= 5) {
               break;
            }
            $debug++;

        }
        return $merged;
    }

    public function citiesapi(){

        $store_user = Auth::user();
        $store_user=DB::table('store_user')->where('user_id',$store_user->id)->first();

       if(isset($store_user)){
           $loginId =$store_user->login_id;
           $apiKey =$store_user->api_key;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://api.withrider.com/rider/v1/GetCityList?loginId='.$loginId.'&apikey='.$apiKey,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_HEADER => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        //\Log::info($response);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);

            if($http_status != 200 ){
                $curlError = "cURL Error(".curl_errno($curl).")". $curl_error;
                curl_close($curl);
                return $curlError;
            }
            elseif($http_status == 200){
                curl_close($curl);
                return $response;
            }
        }
    }


    public function GetShipperLocations(){

            $store_user = Auth::user();
            $store_user=DB::table('store_user')->where('user_id',$store_user->id)->first(); 

            if(isset($store_user)){
            $loginId = $store_user->login_id;

            $apiKey = $store_user->api_key;

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.withrider.com/rider/v1/GetShipperLocations?loginId='.$loginId.'&apikey='.$apiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($curl);
        
            if($http_status != 200 ){
                    $curlError = "cURL Error(".curl_errno($curl).")". $curl_error;
                    curl_close($curl);
                    return $curlError;
            }
            elseif($http_status == 200){
                curl_close($curl);
        
                return $response;
            }
        }
    }


    public function getDeliveryTypes(){

            $store_user = Auth::user();
            $store_user=DB::table('store_user')->where('user_id',$store_user->id)->first(); 

            if(isset($store_user)){
            $loginId =$store_user->login_id;

            $apiKey =$store_user->api_key;

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.withrider.com/rider/v1/GetDeliveryTypes?loginId='.$loginId.'&apikey='.$apiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($curl);
        
            if($http_status != 200 ){
                    $curlError = "cURL Error(".curl_errno($curl).")". $curl_error;
                    curl_close($curl);
                    
                    return $curlError;
            }
            elseif($http_status == 200){
                curl_close($curl);
                return $response;
            }
        }
    }

    public function  orders_post(Request $request){

            $order_id=$request->order_id;
            $order_number=$request->order_number;
            $consignee_name=$request->consignee_name;
            $consignee_number=$request->consignee_number;
            $consignee_address=$request->consignee_address;
            $consignee_city_to_tpl=$request->consignee_city_to_tpl;
            $shipper_city=$request->shipper_city;
            $shipper_address=$request->shipper_address;
            $service_type=$request->service_type;
            $delivery_type=$request->delivery_type;
            $cod_amount=$request->cod_amount;
            $pieces=$request->pieces;
            $weight=$request->weight;
            $description=$request->description;
            $remarks=$request->remarks;



            $orderDataForApi = [];

       
        for ($i = 0; $i < count($request->order_id); $i++){
            $orderDataForApi[] = array( 'orderId' =>$request->order_id[$i],
                                        'orderNumber' =>$request->order_number[$i],
                                        'consigneeName' =>$request->consignee_name[$i],
                                        'consigneeCellNo' =>$request->consignee_number[$i],
                                        'consigneeAddress' =>$request->consignee_address[$i],
                                        'codAmount' =>$request->cod_amount[$i],
                                        'pcs' =>$request->pieces[$i],
                                        'weight' =>$request->weight[$i],
                                        'remarks' =>$request->remarks[$i],
                                        'consignee_city_order' =>$request->consignee_city_order[$i],
                                        'consigneeCities' =>$request->consignee_city_to_tpl[$i],
                                        'shipperCities' =>$request->shipper_city[$i],
                                        'shipperLocations' =>$request->shipper_address[$i],
                                        'serviceTypes' =>$request->service_type[$i],
                                        'deliveryTypes' =>$request->delivery_type[$i],
                                        'description' =>$request->description[$i],
                                    );
        }
        
        
        $store_user = Auth::user();
        $store_user=DB::table('store_user')->where('user_id',$store_user->id)->first();
        
        $responseArray = array();
        if(isset($store_user)){
        $loginId =$store_user->login_id;
        $apiKey =$store_user->api_key;
        
        foreach($orderDataForApi as $item){
        
            $addressConsignee = $item['consigneeAddress'];
            $escapers =     array("\\", "\"",  "\n",  "\r",  "\t", "\x08", "\x0c", '\'');
            $replacements = array("\\\\",  "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b",  "\\\'");
            $convertedConsigneeAddress = str_replace($escapers, $replacements, $addressConsignee);
            
        //post api hit 
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.withrider.com/rider/v3/SaveBooking',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_HEADER => 0,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "loginId":"'.$loginId.'",
                "ConsigneeName":"'.$item['consigneeName'].'",
                "ConsigneeRefNo":"'.$item['orderNumber'].'",
                "ConsigneeCellNo":"'.$item['consigneeCellNo'].'",
                "Address":"'.$convertedConsigneeAddress.'",
                "OriginCityId":"'.$item['shipperCities'].'",
                "DestCityId":"'.$item['consigneeCities'].'",
                "DestAreaId":"",
                "ServiceTypeId":"'.$item['serviceTypes'].'",
                "DeliveryTypeId":"'.$item['deliveryTypes'].'",
                "Pcs":"'.$item['pcs'].'",
                "Weight":"'.$item['weight'].'",
                "Description":"'.$item['description'].'",
                "CodAmount":"'.intval($item['codAmount']).'",
                "remarks":"'.$item['remarks'].'",
                "ShipperAddress":"'.$item['shipperLocations'].'",
                "apikey":"'.$apiKey.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));
     
            $response = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
            $curl_error = curl_error($curl);

            curl_close($curl);
            $convertedResponse = json_decode($response);
            
            if( $convertedResponse->statuscode == 200)
            {
                $shop=Auth::user();
                
                $ordersArray = $shop->api()->rest('GET', '/admin/api/2021-07/orders/'.$item['orderId'].'.json' );
                $tags = $ordersArray['body']['container']['order']['tags'];

                if(empty($tags)){
                $tags = "Book Through Rider";
                }
                else{
                    $tags .= ", Book Through Rider";
                }

                $array = array('order' => array('id' => $item['orderId'], 'tags' => $tags));
                $shop->api()->rest('PUT', '/admin/api/2021-07/orders/'.$item['orderId'].'.json', $array);
                $locationToInsert = '';
                $locations = $shop->api()->rest('GET', '/admin/api/2021-07/locations.json');
                // $locationToInsert = $locations['body']['container']['locations'][0]['id'] ; 
                
                $locations = $locations['body']['container']['locations'];
                foreach ($locations as $location){
                    if( $location['active'] !== false ){
                        $locationToInsert = $location['id'] ;
                        break;
                    }
                }
                 
                $fullFillmentData = array (
                    'fulfillment' => 
                    array (
                      'location_id' => $locationToInsert,
                      'tracking_number' => $convertedResponse->CNUM,
                      'tracking_urls' => 
                      array (
                        0 => "http://track.withrider.com/#/track/$convertedResponse->CNUM"
                      ),
                      'notify_customer' => true,
                    ),
                );
                $shop->api()->rest('POST', '/admin/api/2021-07/orders/'.$item['orderId'].'/fulfillments.json', $fullFillmentData);

                // DB::table('store_order')->insert(['user_id'=>$shop->id , 'order_id'=>$item['orderId']]);

                // array_push($convertedResponse,$item['orderId']);
                $convertedResponse->orderId = $item['orderNumber'];
                $convertedResponse->orderNumber = $item['orderId'];
                $convertedResponse->orderConsigneeName = $item['consigneeName'];
                $convertedResponse->orderConsigneeNumber = $item['consigneeCellNo'];
                $convertedResponse->orderConsigneeCity = $item['consignee_city_order'];
                $convertedResponse->orderCodAmount = $item['codAmount'];
                $convertedResponse->apiKey = $apiKey;
                $convertedResponse->LoginId = $loginId;
    
            }else{
                $convertedResponse->orderId = $item['orderNumber'];
            }
            
            array_push($responseArray,$convertedResponse);

            }
            return $responseArray;
        }
    }

    public function default_setting(Request $request){

        $store_user=Auth::user();        
        $shipper_city_id=$request->shipper_city_id;
        $shipper_address_id=$request->shipper_address_id;
        $default_shipper_delivery_id=$request->default_shipper_delivery_id;
        $is_already_exist=DB::table('store_default_setting')->where('user_id',$store_user->id)->first();
        if($is_already_exist){

            $store=DB::table('store_default_setting')->update(['user_id'=>$store_user->id,'shipper_city_id'=>$shipper_city_id,'shipper_address_id'=>$shipper_address_id,'default_shipper_delivery_id'=>$default_shipper_delivery_id]);
        }else{
            $store=DB::table('store_default_setting')->insert(['user_id'=>$store_user->id,'shipper_city_id'=>$shipper_city_id,'shipper_address_id'=>$shipper_address_id,'default_shipper_delivery_id'=>$default_shipper_delivery_id]);
        }

        return response()->json('its working');
    }

    public function cancel_shipment(Request $request){
        
         $order_id = $request->order_id;
         $tagCancel = "";

         $shop=Auth::user(); 
         $ordersArray = $shop->api()->rest('GET', '/admin/api/2021-07/orders/'.$order_id.'.json' );
         $count_last=count($ordersArray['body']['container']['order']['fulfillments'])-1;
         $cn_number = $ordersArray['body']['container']['order']['fulfillments'][$count_last]['tracking_number'];
         $fullFillmentID=$ordersArray['body']['container']['order']['fulfillments'][$count_last]['id'];


        $tags = $ordersArray['body']['container']['order']['tags'];

        if(strpos($tags, 'Book Through Rider') !== false){
            $tagName = explode(", ",$tags);
            $newTagArray = \array_diff($tagName, ["Book Through Rider"]);
            $tagCancel = implode(", ",$newTagArray);
            
        }
     
         $store_user=DB::table('store_user')->where('user_id',$shop->id)->first();
         if(isset($store_user))
         {

            //post api cancel hit 
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://api.withrider.com/rider/v1/CancelBooking?CNNo='.$cn_number.'&loginId='.$store_user->login_id.'&apikey='.$store_user->api_key.'',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HEADER => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            $curl_error = curl_error($curl);
            
            $convertedResponse = json_decode($response);
           
            $fullFillment = array();
            $fullFillmentCancel = $shop->api()->rest('POST', "/admin/api/2021-07/orders/$order_id/fulfillments/$fullFillmentID/cancel.json",$fullFillment);
            
            $array = array('order' => array('id' => $order_id, 'tags' => $tagCancel));
            $shop->api()->rest('PUT', '/admin/api/2021-07/orders/'.$order_id.'.json', $array);

            if($http_status == 200 ){
                if(curl_errno($curl)){
                    $curlError = "cURL Error(".$curl_errno.")". $curl_error;
                    curl_close($curl);
                    
                    return $curlError;
                }else{
                    curl_close($curl); 
                    return $convertedResponse;            
                }
            }

            return $convertedResponse;  
        }  
    }
}



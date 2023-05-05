<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\library\service;
use App\Models\Shop;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderRelateToSeller;
use Request;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('shared.main-menu', function($view){

          if(Auth::check()) {
            
            $hasOrder = false;

            // Check still has order not complete
            $_order = Order::where('created_by','=',Auth::user()->id)
              ->whereBetween('order_status_id',[1,4]);

            $_clientOrder = OrderRelateToSeller::select('order_relate_to_seller.*')
              ->join('orders','orders.id','=','order_relate_to_seller.order_id')
              ->where('order_relate_to_seller.user_id','=',Auth::user()->id)
              ->whereBetween('orders.order_status_id',[1,4]);

            if($_order->exists() || $_clientOrder->exists()) {
              $hasOrder = true;
            }

            view()->share('_has_order',$hasOrder);

            view()->share('_total_order',$_order->count() + $_clientOrder->count());
            view()->share('_total_my_order',$_order->count());
            view()->share('_total_client_order',$_clientOrder->count());
          }

        });

        view()->composer('shared.modal-user-menu', function($view){

          if(Auth::check()) {

            $has = false;

            $shop = Shop::select('id','slug','name')->where([
                ['deleted','=',0],
                ['created_by','=',Auth::user()->id]
            ]);

            if($shop->exists()) {
                $has = true;
                view()->share('_shop',$shop->first()->buildDataList());
            }
            
            view()->share('_has_shop',$has);

          }

        });

        view()->composer('shared.account-with-menu', function($view){

          if(Auth::check()) {

            $has = false;

            $shop = Shop::select('id','slug','name')->where([
                ['deleted','=',0],
                ['created_by','=',Auth::user()->id]
            ]);

            if($shop->exists()) {
                $has = true;
                view()->share('_shop',$shop->first()->buildDataList());
            }
            
            view()->share('_has_shop',$has);

          }
          
        });

        view()->composer('shared.modal-order-menu', function($view){

          if(Auth::check()) {
            // Get Order Status
            $orderStatuses = OrderStatus::select('id','label')->where('default_value','=',1)->get();

            $orderLists = [];
            $orderClientLists = [];
            foreach ($orderStatuses as $orderStatus) {
              $orderLists[] = [
                'label' => $orderStatus->label,
                'total' => Order::where('created_by','=',Auth::user()->id)->where('order_status_id','=',$orderStatus->id)->count()
              ];

              $orderClientLists[] = [
                'label' => $orderStatus->label,
                'total' => OrderRelateToSeller::select('order_relate_to_seller.*')->join('orders','orders.id','=','order_relate_to_seller.order_id')->where('order_relate_to_seller.user_id','=',Auth::user()->id)->where('orders.order_status_id','=',$orderStatus->id)->count()
              ];
            }

            view()->share('_order_lists',$orderLists);
            view()->share('_order_client_lists',$orderClientLists);
          }

        });

        view()->composer('shared.shop-with-menu', function($view){

          $slug = Request::route()->parameters['slug'];

          $shop = Shop::select('id','name','slug','created_by')
          ->where([
            ['slug','=',$slug],
            ['deleted','=',0]
          ])
          ->first();

          if(!empty($shop)) {

            $blocked = false;
            if(Auth::check() && (Auth::user()->id != $shop->created_by)) {
              $blocked = Service::loadModel('UserBlocking')
              ->where([
                ['model','=','User'],
                ['model_id','=',$shop->created_by],
                ['user_id','=',Auth::user()->id]
              ])->exists();
            }

            view()->share('_shop',$shop->buildDataDetail());
            view()->share('_blocked',$blocked);
          }

        });

        view()->composer('pages.shop.layout.page-blocking-bar', function($view){

          $slug = Request::route()->parameters['slug'];

          $shop = Shop::select('created_by')
          ->where([
            ['slug','=',$slug],
            ['deleted','=',0]
          ])
          ->first();

          if(!empty($shop)) {

            $blocked = false;
            if(Auth::check() && (Auth::user()->id != $shop->created_by)) {
              $blocked = Service::loadModel('UserBlocking')
              ->where([
                ['model','=','User'],
                ['model_id','=',$shop->created_by],
                ['user_id','=',Auth::user()->id]
              ])->exists();
            }

            view()->share('_blocked',$blocked);
          }

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

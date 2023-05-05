<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class SupportController extends Controller
{
  public function index($page) {

    switch ($page) {
      case 'shop-creating':
        $this->setData('page','pages.support.shop-creating');
        $this->setMeta('title','ร้านขายสินค้าและรายการขายจากร้านขายสินค้า');
        break;
      
      default:
          abort(404);
        break;
    }

    return $this->view('pages.support.index');

  }
}

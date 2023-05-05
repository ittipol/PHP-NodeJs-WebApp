<?php

namespace App\Http\Controllers\Web;

use App\library\service;

class HomeController extends Controller
{
  public function index() {

    // $this->botDisallowed();

    return $this->view('pages.home.index');
  }

  public function aaa() {
    ini_set('max_execution_time', 800000);

    $data = Service::loadModel('Category')->get();

    foreach ($data as $value) {

      // $value->update([
      //   'slug' => 'c'.$value->id.'-'.$value->slug
      // ]);

      // $_cat = Service::loadModel('Category')->where([
      //   ['slug','=',$value->slug],
      //   ['id','!=',$value->id]
      // ])->get();

      // if(!empty($_cat)) {

      //   echo 'data->id: '.$value->id.' | slug: '.$value->slug;
      //   echo '<br/>';

      //   foreach ($_cat as $_value) {
      //     echo 'match->id: '.$_value->id.' | slug: '.$_value->slug;
      //     echo '<br/>';
      //   }

      //   echo '<br/><br/>';
      // }

    }

    dd('done!!!');
  }

  public function r() {

    ini_set('max_execution_time', 800000);

    $data = Service::loadModel('Category')->get();

    foreach ($data as $value) {
      $value->update([
        'slug' => 'c'.$value->id.'-'.str_replace(' ', '-', $value->name)
      ]);
    }

    dd('done xxx !!!');

  }

    public function catimg2() {

      ini_set('max_execution_time', 800000);

      $id = 167;
      $image = 'c'.$id.'.png';

      Service::loadModel('Category')->find($id)->update([
        'image' => $image
      ]);

      $_parents = Service::loadModel('Category')->where('parent_id','=',$id)->get();

      foreach ($_parents as $record) {
        $this->xxx($record,$image);
      }

      // $value->update([
      //   'image' => $_parent->image
      // ]);

      dd('img done!!!');
    }

    private function xxx($data,$image) {

      $data->update([
        'image' => $image
      ]);

      $data = Service::loadModel('Category')->where('parent_id','=',$data->id)->get();

      foreach ($data as $_value) {
        $this->xxx($_value,$image);
      }

    }

      public function catPath() {
    exit('Error!');
        ini_set('max_execution_time', 200000);

        $page = 1;
        $perPage = 100;
        $total = Service::loadModel('Category')->count();

        // $count = 1;

        do {

          $offset = ($page - 1)  * $perPage;

          $records = Service::loadModel('Category')
          ->take($perPage)
          ->skip($offset)
          ->get();

          foreach ($records as $record) {
            $categoryId = $record->id;

            $ids = array();
            $ids[] = $categoryId;

            $data = Service::loadModel('Category')->find($categoryId);

            while (!empty($data->parent_id)) {
              $ids[] = $data->parent_id;
              $data = Service::loadModel('Category')->find($data->parent_id);
            }

            $level = count($ids)-1;

            for ($i=0; $i < count($ids); $i++) { 
              $value = array(
                'category_id' => $categoryId,
                'path_id' => $ids[$i],
                'level' => $level--
              );

              $model = Service::loadModel('CategoryPath')->newInstance();
              $model->fill($value)->save();
            }

          }

          $page++;

          // if($count++ > 10) {
          //   break;
          // }

        } while (($offset + $perPage) < $total);

        var_dump($page);

        dd('done');

      }
    

//   public function catimg() {

//     ini_set('max_execution_time', 800000);

//     // $data = Service::loadModel('Category')->where('parent_id','!=',null)->get();
//     $data = Service::loadModel('Category')->where('id','=',596)->get();


//     foreach ($data as $value) {

//       $_parent = Service::loadModel('Category')->select('image')->find($value->parent_id);
// dd($_parent);
//       if(empty($_parent->image)) {
//         dd($value);
//       }

//       $value->update([
//         'image' => $_parent->image
//       ]);
//     }

//     dd('img done!!!');

//   }

  public function cc() {

    // ini_set('max_execution_time', 800000);

    // $data = Service::loadModel('Category')->where('parent_id','=',null)->get();

    // foreach ($data as $value) {

    //   $image = 'c'.$value->id.'.png';

    //   $value->update([
    //     'image' => $image
    //   ]);
    // }

    // dd('done!!!');

  }

}

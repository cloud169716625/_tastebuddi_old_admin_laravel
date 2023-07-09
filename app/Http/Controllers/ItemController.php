<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Items\Item;

class ItemController extends Controller
{
    public function show( Request $request )
    {
        $data = ['test' => 'ok'];

//        if($request->has('item_name')){
            $item = Item::where('item_id', $request->itemId)->first();

            $data = [
                'item' => $item
            ];
//        }

        return view('item.item', $data);
//        return redirect()->route('item.item',$data);
    }
}


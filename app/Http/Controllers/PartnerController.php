<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use Intervention\Image\Facades\Image;

class PartnerController extends Controller
{
    public function index()
    {
        $partner = Partner::all();
        return $partner;      
    }

    public function create(Request $request)
    {
        if($file = $request->file('photo')){
            $file = $request->file('photo');
            $name = $request->get('name').'_'.time().'.jpg';
            $file->move(public_path().'/upload/avatar/', $name);
            $image = Image::make('upload/avatar/' .$name);
            $image->resize(100,100);
            $path = 'upload/avatar/'.$name;
        }else{
            $path = Null;
        }
        $partner = new Partner([
            'name' => $request->input('name'),
            'photo' => $path
        ]);
        $partner->save();

        return response()->json('Partner created!');
    }

    public function show($id)
    {
        $partner = Partner::find($id);
        return response()->json($partner);
    }

    public function update($id, Request $request)
    {
        if($file = $request->file('photo')){
            $file = $request->file('photo');
            $name = $request->get('name').'_'.time().'.jpg';
            $file->move(public_path().'/upload/avatar/', $name);
            $path = 'upload/avatar/'.$name;
            $partner = Partner::find($id);
            $partner->update(['name' => $request->input('name'), 'photo' => $path]);
        }else{
            $partner = Partner::find($id);
            $partner->update(['name' => $request->input('name')]);
        }
        return response()->json('Partner updated!');
    }

    public function destroy($id)
    {
        $partner = Partner::find($id);
        $partner->delete();

        return response()->json('Partner deleted!');
    }
}
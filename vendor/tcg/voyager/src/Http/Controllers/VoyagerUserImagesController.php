<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Facades\Voyager;
use App\Userimage;

class VoyagerUserImagesController extends Controller
{
    public function index(Request $request)
    {

        $slug = $this->getSlug($request);
        Voyager::canOrFail('userimages');

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $user_id = $request->get('user_id');        
        
        $images = Userimage::where('user_id', '=', $user_id)->get();

        return view('voyager::userimages.browse', compact('images','dataType', 'user_id'));
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $user_id = $request->get('user_id');

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Voyager::canOrFail('add_'.$dataType->name);

        $dataTypeContent = (strlen($dataType->model_name) != 0)
                            ? new $dataType->model_name()
                            : false;
        
        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::userimages.edit-add';

        if (view()->exists("voyager::userimages.edit-add")) {
            $view =  "voyager::userimages.edit-add";
        }


        return view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'user_id'));
    }

    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $user_id = $request->get('user_id');

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Voyager::canOrFail('add_'.$dataType->name);

        //Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->ajax()) {

            $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
            return redirect()
                ->route('voyager.userimages.index', ['user_id' => $user_id])
                ->with([
                        'message'    => "Successfully Added New Image",
                        'alert-type' => 'success',
                    ]);
        }
    }

    /*public function store(Request $request)
    {
        // Check permission
        Voyager::canOrFail('browse_settings');

        $lastSetting = Voyager::model('Setting')->orderBy('order', 'DESC')->first();

        if (is_null($lastSetting)) {
            $order = 0;
        } else {
            $order = intval($lastSetting->order) + 1;
        }

        $request->merge(['order' => $order]);
        $request->merge(['value' => '']);

        Voyager::model('Setting')->create($request->all());

        return back()->with([
            'message'    => 'Successfully Created Settings',
            'alert-type' => 'success',
        ]);
    }

    public function update(Request $request)
    {
        // Check permission
        Voyager::canOrFail('visit_settings');

        $settings = Voyager::model('Setting')->all();

        foreach ($settings as $setting) {
            $content = $this->getContentBasedOnType($request, 'settings', (object) [
                'type'    => $setting->type,
                'field'   => $setting->key,
                'details' => $setting->details,
            ]);

            if ($content === null && isset($setting->value)) {
                $content = $setting->value;
            }

            $setting->value = $content;
            $setting->save();
        }

        return back()->with([
            'message'    => 'Successfully Saved Settings',
            'alert-type' => 'success',
        ]);
    }

    public function delete($id)
    {
        Voyager::canOrFail('browse_settings');

        // Check permission
        Voyager::canOrFail('visit_settings');

        Voyager::model('Setting')->destroy($id);

        return back()->with([
            'message'    => 'Successfully Deleted Setting',
            'alert-type' => 'success',
        ]);
    }

    public function move_up($id)
    {
        $setting = Voyager::model('Setting')->find($id);
        $swapOrder = $setting->order;
        $previousSetting = Voyager::model('Setting')->where('order', '<', $swapOrder)->orderBy('order', 'DESC')->first();
        $data = [
            'message'    => 'This is already at the top of the list',
            'alert-type' => 'error',
        ];

        if (isset($previousSetting->order)) {
            $setting->order = $previousSetting->order;
            $setting->save();
            $previousSetting->order = $swapOrder;
            $previousSetting->save();

            $data = [
                'message'    => "Moved {$setting->display_name} setting order up",
                'alert-type' => 'success',
            ];
        }

        return back()->with($data);
    }

    public function delete_value($id)
    {
        // Check permission
        Voyager::canOrFail('browse_settings');

        $setting = Voyager::model('Setting')->find($id);

        if (isset($setting->id)) {
            // If the type is an image... Then delete it
            if ($setting->type == 'image') {
                if (Storage::disk(config('voyager.storage.disk'))->exists($setting->value)) {
                    Storage::disk(config('voyager.storage.disk'))->delete($setting->value);
                }
            }
            $setting->value = '';
            $setting->save();
        }

        return back()->with([
            'message'    => "Successfully removed {$setting->display_name} value",
            'alert-type' => 'success',
        ]);
    }

    public function move_down($id)
    {
        $setting = Voyager::model('Setting')->find($id);
        $swapOrder = $setting->order;

        $previousSetting = Voyager::model('Setting')->where('order', '>', $swapOrder)->orderBy('order', 'ASC')->first();
        $data = [
            'message'    => 'This is already at the bottom of the list',
            'alert-type' => 'error',
        ];

        if (isset($previousSetting->order)) {
            $setting->order = $previousSetting->order;
            $setting->save();
            $previousSetting->order = $swapOrder;
            $previousSetting->save();

            $data = [
                'message'    => "Moved {$setting->display_name} setting order down",
                'alert-type' => 'success',
            ];
        }

        return back()->with($data);
    }*/
}

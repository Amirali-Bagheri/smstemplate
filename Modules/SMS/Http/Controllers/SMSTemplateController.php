<?php

namespace Modules\SMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SMS\Models\SmsTemplate;

class SMSTemplateController extends Controller
{
    public function index(Request $request)
    {
        $sms_templates = SmsTemplate::all();

        return response([
            'status' => true,
            'data' => $sms_templates,
        ], 200);
    }

    public function create(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'variables' => 'required',
        ]);

        $sms_template = SmsTemplate::create([
            'title' => $request->title,
            'body' => $request->body,
            'variables' => json_encode($request->variables),
        ]);

        return response([
            'status' => true,
            'data' => $sms_template,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $sms_template = SmsTemplate::query()->findOrFail($id);

        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'variables' => 'required',
        ]);

        $sms_template->update([
            'title' => $request->title,
            'body' => $request->body,
            'variables' => $request->variables,
        ]);

        return response([
            'status' => true,
            'data' => $sms_template,
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $sms_template = SmsTemplate::query()->findOrFail($id);
        return response([
            'status' => true,
            'data' => $sms_template
        ], 200);

    }

    public function delete(Request $request, $id)
    {
        $sms_template = SmsTemplate::query()->findOrFail($id);
        $sms_template->delete();

        return response([
            'status' => true,
            'message' => 'تمپلیت با موفقیت حذف شد.',
        ], 200);
    }
}
